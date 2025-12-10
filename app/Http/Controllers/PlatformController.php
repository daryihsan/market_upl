<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Mail\SellerVerificationMail;
use App\Mail\SellerRejectionMail;

class PlatformController extends Controller
{
    // =======================
    // 1. DASHBOARD UTAMA
    // =======================
    public function dashboard()
    {
        // 1. Jumlah Penjual (Aktif/Tidak Aktif/Pending) (SRS-MartPlace-07)
        $pending_count     = User::where('status_akun', 'pending')->count();
        $aktif_count       = User::where('status_akun', 'active')->count();
        $rejected_count    = User::where('status_akun', 'rejected')->count();
        $total_penjual     = $pending_count + $aktif_count + $rejected_count;

        // 2. Sebaran Jumlah Produk Berdasarkan Kategori (SRS-MartPlace-07)
        $productsByCategory = Product::with('category')
            ->select('category_id', DB::raw('count(*) as total_products'))
            ->groupBy('category_id')
            ->get();
        
        $categoryLabels = $productsByCategory->pluck('category.name')->toArray();
        $productCounts = $productsByCategory->pluck('total_products')->toArray();
        
        // 3. Sebaran Jumlah Toko Berdasarkan Lokasi Provinsi (SRS-MartPlace-07)
        $sellersByProvince = User::whereNotNull('nama_toko') 
            ->select('provinsi', DB::raw('count(*) as total_sellers'))
            ->groupBy('provinsi')
            ->get();
            
        $provinceLabels = $sellersByProvince->pluck('provinsi')->toArray();
        $sellerCounts = $sellersByProvince->pluck('total_sellers')->toArray();

        // 4. Jumlah Pengunjung Memberi Komentar dan Rating (SRS-MartPlace-07)
        $totalReviews = Review::count(); 

        return view('platform.dashboard', [
            'pending_verifications_count' => $pending_count,
            'total_penjual_aktif'         => $aktif_count,
            'total_penjual_tidak_aktif'   => $rejected_count,
            'total_pengunjung_rating'     => $totalReviews, 
            'totalPenjual'                => $total_penjual, 
            
            // Data Chart Produk per Kategori
            'productCategoryLabels'       => $categoryLabels,
            'productCategoryCounts'       => $productCounts,
            
            // Data Chart Toko per Provinsi
            'provinceLabels'              => $provinceLabels,
            'provinceCounts'              => $sellerCounts,
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
