<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\PlatformController;
use App\Http\Controllers\SellerDashboardController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ReviewController; 
use App\Models\Category;
use App\Http\Controllers\SearchController;

// Pencarian Produk
Route::get('/search', [SearchController::class, 'search'])->name('search');
// Pencarian Produk (dropdown suggestion, AJAX)
Route::get('/search/suggest', [SearchController::class, 'suggest'])->name('search.suggest');
Route::get('/search', [SearchController::class, 'index'])->name('search');


// PENGUNJUNG--------------------------------------------------------------------------
// homepage
// Route::get('/', action: function () {
//     return view('home');
// });
Route::get('/', [CatalogController::class, 'home'])->name('home'); // DIPERBARUI

// katalog produk
Route::get('/katalog', [CatalogController::class, 'index'])->name('katalog');

// detail produk
Route::get('/product/{id}/detail', function ($id) {
    // TAMBAH 'reviews' ke eager load
    $product = App\Models\Product::with(['user', 'category', 'reviews'])->findOrFail($id); 
    return view('products.detail', compact('product'));
})->name('product.detail');

// ulasan produk
Route::post('/product/{product}/review', [ReviewController::class, 'store'])->name('review.store');

// PENJUAL--------------------------------------------------------------------------
// regist
Route::prefix('register')->name('register.')->group(function () {
    Route::get('/step1', [RegisterController::class, 'showStep1'])->name('step1');
    Route::post('/step1', [RegisterController::class, 'processStep1'])->name('step1.post');

    Route::get('/step2', [RegisterController::class, 'showStep2'])->name('step2');
    Route::post('/step2', [RegisterController::class, 'processStep2'])->name('step2.post');

    Route::get('/step3', [RegisterController::class, 'showStep3'])->name('step3');
    Route::post('/step3', [RegisterController::class, 'processStep3'])->name('step3.post');

    // pendaftaran berhasil
    Route::get('/success', [RegisterController::class, 'showSuccess'])->name('success');
});

// verif email
Route::get('/email/verify/{token}/{email}', [VerificationController::class, 'verify'])
    ->name('verification.verify')
    ->middleware('guest');

// login
Route::prefix('login')->group(function () {
    Route::get('/pilih', [LoginController::class, 'showPilih'])->name('login.pilih');

    Route::get('/login', [LoginController::class, 'showLogin'])->name('login.login');
    Route::post('/login', [LoginController::class, 'processLogin'])->name('login.post.login');

    Route::get('/admin', [LoginController::class, 'showAdmin'])->name('login.admin');
    Route::post('/admin', [LoginController::class, 'processAdmin'])->name('login.post.admin');
});

// ini ga pake middleware biar bisa logout
Route::prefix('seller')->name('seller.')->group(function () {
    // dashboard
    Route::get('/dashboard', [SellerController::class, 'dashboard'])->name('dashboard');

    // CRUD produk
    Route::get('/products', [SellerController::class, 'listProducts'])->name('products.index');
    Route::get('/products/create', [SellerController::class, 'showCreateForm'])->name('products.create');
    Route::post('/products/store', [SellerController::class, 'storeProduct'])->name('products.store');
    Route::put('/products/{product}', [SellerController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{product}', [SellerController::class, 'deleteProduct'])->name('products.destroy');

    // laporan
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/download', [ReportController::class, 'downloadPdf'])->name('reports.download');

    //categories
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
    });
});

// middleware auth login penjual (harus login baru bisa akses dashboard)
Route::middleware('auth')->prefix('seller')->name('seller.')->group(function () {
    // Route::get('/dashboard', [SellerController::class, 'dashboard'])->name('dashboard');
});

// logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ADMIN --------------------------------------------------------------------------
// platform
Route::prefix('platform')->name('platform.')->group(function () {

    // DASHBOARD ADMIN
    Route::get('/dashboard', [PlatformController::class, 'dashboard'])
        ->name('dashboard');

    // VERIFIKASI PENJUAL
    Route::get('/verifikasi', [PlatformController::class, 'verificationList'])
        ->name('verifikasi.list');

    Route::get('/verifikasi/{id}', [PlatformController::class, 'verificationDetail'])
        ->name('verifikasi.detail');

    Route::post('/verifikasi/{id}/process', [PlatformController::class, 'processVerification'])
        ->name('verifikasi.process');

    // ==============================
    // LAPORAN
    // ==============================

    // TAB 1 – "Daftar Penjual"
    Route::get('/laporan', [PlatformController::class, 'sellerReportIndex'])
        ->name('laporan');

    // TAB 2 – "Penjual per Provinsi"
    Route::get('/laporan/provinsi', function () {
        return view('platform.provinsi');
    })->name('laporan.provinsi');

    // TAB 3 – "Produk Lengkap"
    Route::get('/laporan/produk', function () {
        return view('platform.produk');
    })->name('laporan.produk');

    // DOWNLOAD PDF – 3 jenis: status, provinsi, produk
    Route::get('/laporan/download', [PlatformController::class, 'downloadPlatformReport'])
        ->name('laporan.download');

    // ==============================
    // MANAJEMEN KATEGORI (CRUD)
    // ==============================
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');   // platform.categories.index
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
    });
});


Route::get('/seller/reports', [ReportController::class, 'index'])
    ->name('seller.reports.index')
    ->middleware('auth');

Route::get('/seller/reports/download', [ReportController::class, 'downloadPdf'])
    ->name('seller.reports.download')
    ->middleware('auth');

Route::get('/kategori/{slug}', [CategoryController::class, 'showProducts'])
    ->name('catalog.byCategory');

// DRAFT!!!!!
// Route::get('/home', function () {
//     return view('home');
// })->middleware('auth')->name('home');

// Route::get('/verify-otp', [AuthController::class, 'showVerifyOtp'])->name('verify.otp');
// Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);



























Route::get('/local-file', function (\Illuminate\Http\Request $request) {
    $path = $request->query('path');

    $fullPath = storage_path('app/private/public/' . $path);

    if (!file_exists($fullPath)) {
        abort(404, "File not found: $fullPath");
    }

    return response()->file($fullPath);
})->name('local.file');
