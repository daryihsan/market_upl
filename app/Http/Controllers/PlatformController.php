<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Review;
use App\Models\Category;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
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
            $topN = max(1, (int) $topCategories);
            $categoriesForChart = $categoriesAll->take($topN);
        } else {
            $categoriesForChart = $categoriesAll;
        }

        $category_labels = $categoriesForChart->pluck('name')->map(fn($v) => (string) $v)->toArray();
        $category_counts = $categoriesForChart->pluck('products_count')->map(fn($v) => (int) $v)->toArray();

        // Provinsi distribution (count sellers per provinsi)
        $provinsiDistribution = User::whereNotNull('nama_toko')
            ->select('provinsi', DB::raw('COUNT(*) as total'))
            ->groupBy('provinsi')
            ->orderByDesc('total')
            ->get();

        $provinsi_labels = $provinsiDistribution->pluck('provinsi')->map(fn($v) => $v ?: 'Tidak Diketahui')->toArray();
        $provinsi_counts = $provinsiDistribution->pluck('total')->map(fn($v) => (int) $v)->toArray();

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
        $reviews_counts = array_map(fn($d) => isset($reviewsMap[$d]) ? (int) $reviewsMap[$d] : 0, $period);

        $total_pengunjung_rating_period = array_sum($reviews_counts);

        // 2. Jumlah Pengunjung (Pemberi Rating/Komentar Unik)
        $total_commenters = Review::distinct('email_address')->count();

        // 3. Sebaran Produk Berdasarkan Kategori (untuk Grafik Batang)
        $product_distribution = Product::select(DB::raw('count(products.id) as product_count'), 'categories.name as category_name')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->groupBy('categories.name')
            ->orderByDesc('product_count')
            ->limit(10) // Ambil 10 kategori teratas agar grafik tidak terlalu padat
            ->get();

        // Konversi ke format yang mudah digunakan di Chart.js
        $product_chart_data = [
            'labels' => $product_distribution->pluck('category_name'),
            'data' => $product_distribution->pluck('product_count'),
        ];


        // 4. Sebaran Toko Berdasarkan Provinsi (untuk Grafik Donut)
        $location_distribution = User::select(DB::raw('count(id) as seller_count'), 'provinsi')
            ->whereNotNull('nama_toko') // Hanya hitung yang punya nama toko (Penjual)
            ->where('provinsi', '!=', '') // Hanya hitung yang provinsinya terisi
            ->groupBy('provinsi')
            ->orderByDesc('seller_count')
            ->get();

        $total_counted_sellers = $location_distribution->sum('seller_count');

        $location_chart_data = [];

        // Ambil 5 provinsi teratas, sisanya masukkan ke 'Lainnya'
        foreach ($location_distribution->take(5) as $dist) {
            $location_chart_data[] = [
                'provinsi' => $dist->provinsi,
                'count' => $dist->seller_count,
                'percentage' => round(($dist->seller_count / $total_counted_sellers) * 100, 1),
            ];
        }

        // Hitung sisanya
        if ($location_distribution->count() > 5) {
            $other_count = $location_distribution->skip(5)->sum('seller_count');
            $other_percentage = round(($other_count / $total_counted_sellers) * 100, 1);

            if ($other_count > 0) {
                $location_chart_data[] = [
                    'provinsi' => 'Lainnya',
                    'count' => $other_count,
                    'percentage' => $other_percentage,
                ];
            }
        }

        // Mengirim semua data ke view
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
    public function verificationList()
    {
        $pending_sellers = User::where('status_akun', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('platform.verification_list', compact('pending_sellers'));
    }

    // 3. DETAIL VERIFIKASI
    public function verificationDetail($id)
    {
        $seller = User::with('documents')->findOrFail($id);
        return view('platform.verification_detail', compact('seller'));
    }

    // 4. PROSES VERIFIKASI
    public function processVerification(Request $request, $id)
    {
        $seller = User::with('documents')->findOrFail($id);

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
                ->with('success', "Penjual ({$seller->nama_toko}) berhasil diaktifkan.");
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

    // 5. HALAMAN LAPORAN UTAMA (reportIndex)
    public function reportIndex(Request $request)
    {
        // Masalah 1 & 2: Tetap di halaman laporan, menggunakan 'report_tab'
        $activeReportTab = $request->query('report_tab', 'penjual_status'); // Default: Daftar Penjual

        $data = [
            'activeReportTab' => $activeReportTab,
        ];

        switch ($activeReportTab) {
            case 'penjual_status':
                // TAB 1: Daftar Penjual (Berdasarkan Status)
                $statusFilter = $request->query('status', 'semua');
                $query = User::whereNotNull('nama_toko');

                if ($statusFilter === 'aktif') {
                    $query->where('status_akun', 'active');
                } elseif ($statusFilter === 'tidak_aktif') {
                    $query->whereIn('status_akun', ['rejected', 'pending']);
                }

                // Urutkan sesuai kebutuhan screenshot (aktif dulu baru tidak aktif)
                $sellers = $query->orderBy(DB::raw("CASE WHEN status_akun = 'active' THEN 0 ELSE 1 END"))
                    ->orderBy('created_at', 'asc')
                    ->get();

                $data['sellers'] = $sellers;
                $data['statusFilter'] = $statusFilter;
                break;

            case 'penjual_provinsi':
                // TAB 2: Penjual per Provinsi
                $provinsiFilter = $request->query('provinsi', 'Semua');
                $query = User::whereNotNull('nama_toko');

                if ($provinsiFilter !== 'Semua') {
                    // Menggunakan kolom 'provinsi' yang sudah ada di model User
                    $query->where('provinsi', $provinsiFilter);
                }

                // Ambil semua provinsi unik untuk filter
                $provinces = User::whereNotNull('nama_toko')
                    ->distinct()
                    ->pluck('provinsi')
                    ->filter() // Hapus null/empty
                    ->sort();

                $sellers = $query->orderBy('provinsi', 'asc')
                    ->orderBy('nama_toko', 'asc')
                    ->get();

                $data['sellers'] = $sellers;
                $data['provinsiFilter'] = $provinsiFilter;
                $data['provinces'] = $provinces; // Kirim daftar provinsi untuk dropdown
                break;

            case 'produk_lengkap':
                // TAB 3: Produk Lengkap
                $categoryFilter = $request->query('kategori'); // Filter berdasarkan ID kategori
                $ratingFilter = $request->query('rating'); // Filter 4+, 3+

                $query = Product::with(['user', 'category']);

                if ($categoryFilter) {
                    $query->where('category_id', $categoryFilter);
                }

                if ($ratingFilter) {
                    // Rating 4+ atau 3+
                    $ratingMin = (int) str_replace('+', '', $ratingFilter);
                    $query->where('rating', '>=', $ratingMin);
                }

                // Urutkan berdasarkan rating menurun (sesuai SRS-MartPlace-11)
                $products = $query->orderByDesc('rating')
                    ->orderBy('total_ulasan', 'desc')
                    ->get();

                // Ambil semua kategori untuk filter
                $categories = Category::all();

                $data['products'] = $products;
                $data['categories'] = $categories;
                $data['categoryFilter'] = $categoryFilter;
                $data['ratingFilter'] = $ratingFilter;
                break;
        }

        return view('platform.laporan', $data);
    }

    // 6. DOWNLOAD PDF LAPORAN (3 jenis)
    public function downloadPlatformReport(Request $request)
    {
        // type: status | provinsi | produk
        $type = $request->query('type', 'status');
        $generatedAt = now();
        $processedBy = "Admin QuadMarket"; // Ganti jika Anda memiliki Admin User

        $view = '';
        $data = [
            'generatedAt' => $generatedAt,
            'processedBy' => $processedBy,
        ];
        $filename = 'laporan_tidak_valid.pdf';

        if ($type === 'status') {
            // BA. STATUS PENJUAL
            $statusFilter = $request->query('status', 'semua');
            $query = User::whereNotNull('nama_toko');

            if ($statusFilter === 'aktif') {
                $query->where('status_akun', 'active');
            } elseif ($statusFilter === 'tidak_aktif') {
                $query->whereIn('status_akun', ['rejected', 'pending']);
            }

            $sellers = $query->orderBy(DB::raw("CASE WHEN status_akun = 'active' THEN 0 ELSE 1 END"))
                ->orderBy('created_at', 'asc')
                ->get();

            $statusLabel = match ($statusFilter) {
                'aktif' => 'Aktif',
                'tidak_aktif' => 'Tidak Aktif',
                default => 'Semua Status',
            };

            $data['sellers'] = $sellers;
            $data['statusLabel'] = $statusLabel;
            $view = 'platform.pdf.laporan_status_penjual';
            $filename = 'laporan_status_penjual_' . $statusFilter . '.pdf';
            $paper = 'A4';
            $orientation = 'portrait';

        } elseif ($type === 'provinsi') {
            // BB. PENJUAL PER PROVINSI
            $provinsiFilter = $request->query('provinsi', 'Semua');
            $query = User::whereNotNull('nama_toko');

            if ($provinsiFilter !== 'Semua') {
                $query->where('provinsi', $provinsiFilter);
            }

            $sellers = $query->orderBy('provinsi', 'asc')
                ->orderBy('nama_toko', 'asc')
                ->get();

            $data['sellers'] = $sellers;
            $data['provinsi'] = $provinsiFilter;
            $view = 'platform.pdf.laporan_per_provinsi';
            $filename = 'laporan_penjual_per_provinsi_' . ($provinsiFilter !== 'Semua' ? $provinsiFilter : 'semua') . '.pdf';
            $paper = 'A4';
            $orientation = 'portrait';

        } elseif ($type === 'produk') {
            // BC. PRODUK LENGKAP
            $categoryFilter = $request->query('kategori'); // Ini adalah ID kategori
            $ratingFilter = $request->query('rating');

            $query = Product::with(['user', 'category']);

            // --- FIX: Ambil Label Kategori ---
            $categoryLabel = 'Semua Kategori';
            if ($categoryFilter) {
                $category = Category::find($categoryFilter);
                if ($category) {
                    $categoryLabel = $category->name;
                    $query->where('category_id', $categoryFilter);
                }
            }

            $ratingLabel = 'Semua Rating';
            if ($ratingFilter) {
                $ratingLabel = (int) str_replace('+', '', $ratingFilter) . '+';
                $query->where('rating', '>=', (int) str_replace('+', '', $ratingFilter));
            }
            // --- END FIX ---


            $products = $query->orderByDesc('rating')
                ->orderBy('total_ulasan', 'desc')
                ->get();

            $data['products'] = $products;
            $data['categoryLabel'] = $categoryLabel; // Kirim label ke view
            $data['ratingLabel'] = $ratingLabel;   // Kirim label ke view

            $view = 'platform.pdf.laporan_produk_rating';
            $filename = 'laporan_produk_lengkap.pdf';
            $paper = 'A4';
            $orientation = 'landscape'; // Layout landscape lebih cocok untuk banyak kolom
        }

        if ($view) {
            $pdf = Pdf::loadView($view, $data)->setPaper($paper, $orientation);
            return $pdf->download($filename);
        }

        return redirect()->back()->with('error', 'Tipe laporan tidak valid.');
    }
}