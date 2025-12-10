<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Category;
use App\Models\Review;

use App\Mail\SellerVerificationMail;
use App\Mail\SellerRejectionMail;

class PlatformController extends Controller
{
    // =======================
    // 1. DASHBOARD UTAMA
    // =======================
    public function dashboard(Request $request)
    {
        // UI params
        $topCategories = $request->query('top_categories', '10');
        $reviewPeriod = (int) $request->query('review_period', 7);

        // Seller counts (only active & rejected)
        $total_penjual_aktif = User::whereNotNull('nama_toko')
            ->where('status_akun', 'active')
            ->count();

        $total_penjual_tidak_aktif = User::whereNotNull('nama_toko')
            ->where('status_akun', 'rejected')
            ->count();

        // Categories with counts (left join so categories with zero products included)
        $categoriesQuery = DB::table('categories')
            ->leftJoin('products', 'categories.id', '=', 'products.category_id')
            ->select('categories.id', 'categories.name', DB::raw('COUNT(products.id) as products_count'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('products_count');

        $categoriesAll = $categoriesQuery->get();

        if ($topCategories !== 'all') {
            $topN = max(1, (int)$topCategories);
            $categoriesForChart = $categoriesAll->take($topN);
        } else {
            $categoriesForChart = $categoriesAll;
        }

        $category_labels = $categoriesForChart->pluck('name')->map(fn($v) => (string)$v)->toArray();
        $category_counts = $categoriesForChart->pluck('products_count')->map(fn($v) => (int)$v)->toArray();

        // Provinsi distribution (count sellers per provinsi)
        $provinsiDistribution = User::whereNotNull('nama_toko')
            ->select('provinsi', DB::raw('COUNT(*) as total'))
            ->groupBy('provinsi')
            ->orderByDesc('total')
            ->get();

        $provinsi_labels = $provinsiDistribution->pluck('provinsi')->map(fn($v) => $v ?: 'Tidak Diketahui')->toArray();
        $provinsi_counts = $provinsiDistribution->pluck('total')->map(fn($v) => (int)$v)->toArray();

        // Reviews (interaction) grouped by date within period
        $end = now()->endOfDay();
        $start = now()->subDays(max(1, $reviewPeriod - 1))->startOfDay();

        $reviewsByDate = Review::whereBetween('created_at', [$start, $end])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // build full date range
        $period = [];
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $period[] = $cursor->format('Y-m-d');
            $cursor->addDay();
        }

        $reviewsMap = $reviewsByDate->pluck('total', 'date')->toArray();
        $reviews_labels = array_map(fn($d) => date('d M', strtotime($d)), $period);
        $reviews_counts = array_map(fn($d) => isset($reviewsMap[$d]) ? (int)$reviewsMap[$d] : 0, $period);

        $total_pengunjung_rating_period = array_sum($reviews_counts);

        return view('platform.dashboard', [
            'total_penjual_aktif' => $total_penjual_aktif,
            'total_penjual_tidak_aktif' => $total_penjual_tidak_aktif,
            'category_labels' => $category_labels,
            'category_counts' => $category_counts,
            'categories_all' => $categoriesAll,
            'provinsi_labels' => $provinsi_labels,
            'provinsi_counts' => $provinsi_counts,
            'reviews_labels' => $reviews_labels,
            'reviews_counts' => $reviews_counts,
            'review_period_days' => $reviewPeriod,
            'total_pengunjung_rating_period' => $total_pengunjung_rating_period,
            'selected_top_categories' => $topCategories,
            'selected_review_period' => $reviewPeriod,
        ]);
    }


    // =======================
    // 2. LIST VERIFIKASI
    // =======================
    public function verificationList()
    {
        $pending_sellers = User::where('status_akun', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('platform.verification_list', compact('pending_sellers'));
    }

    // =======================
    // 3. DETAIL VERIFIKASI
    // =======================
    public function verificationDetail($id)
    {
        $seller = User::findOrFail($id);
        return view('platform.verification_detail', compact('seller'));
    }

    // =======================
    // 4. PROSES VERIFIKASI
    // =======================
    public function processVerification(Request $request, $id)
    {
        $seller = User::findOrFail($id);

        $request->validate([
            'action' => 'required|in:approve,reject'
        ]);

        if ($seller->status_akun !== 'pending') {
            return redirect()->route('platform.verifikasi.list')
                ->with('info', 'Status akun sudah final.');
        }

        // APPROVE
        if ($request->action === 'approve') {

            $seller->status_akun = 'active';
            $seller->verification_date = now();
            $seller->save();

            Mail::to($seller->email_pic)->send(new SellerVerificationMail(
                $seller->nama_toko,
                url('/login/login')
            ));

            return redirect()->route('platform.verifikasi.list')
                ->with('success', "Penjual {$seller->nama_toko} berhasil diaktifkan.");
        }

        // REJECT
        if ($request->action === 'reject') {

            $seller->status_akun = 'rejected';
            $seller->verification_date = now();
            $seller->save();

            Mail::to($seller->email_pic)->send(new SellerRejectionMail(
                $seller->nama_toko,
                $request->alasan
            ));

            return redirect()->route('platform.verifikasi.list')
                ->with('error', 'Penjual ditolak.');
        }
    }

    // =======================
    // 5. HALAMAN LAPORAN – TAB "Daftar Penjual"
    //    (yang daftar penjual udah aman)
    // =======================
    public function sellerReportIndex(Request $request)
    {
        // status = semua | aktif | tidak_aktif
        $statusFilter = $request->query('status', 'semua');

        // pakai nama_toko sebagai penanda seller
        $query = User::whereNotNull('nama_toko');

        if ($statusFilter === 'aktif') {
            $query->where('status_akun', 'active');
        } elseif ($statusFilter === 'tidak_aktif') {
            $query->where('status_akun', 'rejected');
        }

        $sellers = $query->orderBy('created_at', 'asc')->get();

        return view('platform.laporan', [
            'sellers'      => $sellers,
            'statusFilter' => $statusFilter,
        ]);
    }

    // =======================
// 8. DOWNLOAD PDF LAPORAN (3 jenis)
// =======================
public function downloadPlatformReport(Request $request)
{
    // type = status | provinsi | produk
    $type        = $request->query('type', 'status');
    $generatedAt = now();
    $processedBy = "Admin QuadMarket";

    // ---------- 8a. STATUS PENJUAL (INI SUDAH JALAN DI KAMU) ----------
    if ($type === 'status') {

        $statusFilter = $request->query('status', 'semua');

        // AMBIL DARI DB: tabel users, yang punya nama_toko
        $query = User::whereNotNull('nama_toko');

        if ($statusFilter === 'aktif') {
            $query->where('status_akun', 'active');
        } elseif ($statusFilter === 'tidak_aktif') {
            $query->where('status_akun', 'rejected');
        }

        $sellers = $query->orderBy('created_at', 'asc')->get();

        $statusLabel = match ($statusFilter) {
            'aktif'       => 'Aktif',
            'tidak_aktif' => 'Tidak Aktif',
            default       => 'Semua Status',
        };

        // VIEW: resources/views/platform/pdf/laporan_status_penjual.blade.php
        $pdf = Pdf::loadView('platform.pdf.laporan_status_penjual', [
            'sellers'      => $sellers,
            'statusLabel'  => $statusLabel,
            'generatedAt'  => $generatedAt,
            'processedBy'  => $processedBy,
        ])->setPaper('A4', 'portrait');

        return $pdf->download('laporan_status_penjual.pdf');
    }

    // ---------- 8b. PENJUAL PER PROVINSI ----------
    if ($type === 'provinsi') {

        // PARAMETER DARI URL (?provinsi=...)
        $provinsi = $request->query('provinsi', 'Semua');

        // AMBIL DARI DB: sama kayak status, tapi bisa difilter provinsi
        $query = User::whereNotNull('nama_toko');

        if ($provinsi !== 'Semua') {
            $query->where('provinsi', $provinsi);
        }

        $sellers = $query->orderBy('created_at', 'asc')->get();

        // VIEW: resources/views/platform/pdf/laporan_per_provinsi.blade.php
        $pdf = Pdf::loadView('platform.pdf.laporan_per_provinsi', [
            'sellers'     => $sellers,
            'provinsi'    => $provinsi,
            'generatedAt' => $generatedAt,
            'processedBy' => $processedBy,
        ])->setPaper('A4', 'portrait');

        return $pdf->download('laporan_per_provinsi.pdf');
    }

    // ---------- 8c. PRODUK LENGKAP ----------
    if ($type === 'produk') {

        // AMBIL DARI DB: tabel products + relasi category & user
        $products = Product::with(['category', 'user'])
            ->orderBy('created_at', 'asc')
            ->get();

        // VIEW: resources/views/platform/pdf/laporan_produk_rating.blade.php
        $pdf = Pdf::loadView('platform.pdf.laporan_produk_rating', [
            'products'    => $products,
            'generatedAt' => $generatedAt,
            'processedBy' => $processedBy,
        ])->setPaper('A4', 'landscape');

        return $pdf->download('laporan_produk_lengkap.pdf');
    }

    return redirect()->back()->with('error', 'Tipe laporan tidak valid.');
}


}
