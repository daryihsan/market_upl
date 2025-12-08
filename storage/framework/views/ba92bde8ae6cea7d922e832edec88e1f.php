<?php
$user = auth()->user();
$storeName    = $user->nama_toko ?? 'Nama Toko';
$storeInitial = mb_substr($storeName, 0, 1, 'UTF-8'); 
$storeCity    = $user->kabupaten ?? 'Semarang'; 

// Ambil data dari array $data yang dikirim dari controller
$activeTab = request()->query('tab', 'overview');

// Dashboard Data
$totalProducts = $data['totalProducts'] ?? 0;
$averageRating = $data['averageRating'] ?? 0;
$productCountsByCategory = []; 

// Ambil semua produk milik user yang sedang login, beserta kategorinya
$sellerProducts = \App\Models\Product::where('user_id', $user->id)
    ->with('category')
    ->get();

// Hitung total stok per kategori
foreach ($sellerProducts as $product) {
    $categoryName = $product->category->name ?? 'Lain-lain';
    
    if (!isset($productCountsByCategory[$categoryName])) {
        $productCountsByCategory[$categoryName] = 0;
    }
    $productCountsByCategory[$categoryName] += $product->stock;
}

// Konversi ke format array objek yang dibutuhkan oleh chart JS ($salesByCategory)
$stockByCategoryForChart = [];
foreach ($productCountsByCategory as $name => $stock) {
    $stockByCategoryForChart[] = (object) [
        'Kategori'  => $name,
        'Penjualan' => $stock // nilai sebenarnya stok
    ];
}

$salesByCategory = $stockByCategoryForChart;

$locationData = $data['locationData'] ?? [
    'TotalOrders' => 0,
    0 => ['Lokasi' => 'N/A', 'Persentase' => 0],
    1 => ['Lokasi' => 'N/A', 'Persentase' => 0],
    2 => ['Lokasi' => 'N/A', 'Persentase' => 0],
];

$latestProducts = $data['latestProducts'] ?? collect([]);

// Product Data
$productStats = $data['productStats'] ?? [
    'total_produk' => 0,
    'produk_aktif' => 0,
    'stok_habis'   => 0,
    'tidak_aktif'  => 0
];

$products      = $data['products'] ?? collect([]);
$allCategories = $data['allCategories'] ?? collect([]);

// Edit Mode Logika
$editMode    = $activeTab === 'addProduct' && request()->query('mode') === 'edit';
$editProduct = $data['editProduct'] ?? null;

// Ringkasan
$summaryData = [
    (object)['title' => 'Total Produk',      'value' => number_format($totalProducts),    'class' => 'text-blue-600'],
    (object)['title' => 'Rating Rata-Rata',  'value' => number_format($averageRating, 1), 'class' => 'text-red-500'],    
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Penjual | QuadMarket</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --background-color: #f8f9fa;
            --card-background: #ffffff;
            --text-color: #212529;
            --border-color: #e9ecef;
            --active-status: #28a745;
            --inactive-status: #dc3545;
        }
        body {
            background-color: var(--background-color);
            color: var(--text-color);
            font-family: 'Inter', sans-serif;
        }
        .dashboard-container { display: flex; min-height: 100vh; }
        .sidebar {
            width: 250px; 
            background-color: var(--card-background);
            padding: 20px; 
            display: flex; 
            flex-direction: column; 
            justify-content: space-between; 
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05); 
            flex-shrink: 0;
        }
        .main-content { flex-grow: 1; padding: 30px; overflow-y: auto; }
        .card {
            background-color: var(--card-background);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .form-input {
            width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;
        }
        .action-footer {
            position: fixed;
            bottom: 0; right: 0; left: 250px;
            padding: 15px 30px; background-color: var(--card-background);
            z-index: 50; border-top: 1px solid var(--border-color);
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.05);
            display: flex; justify-content: flex-end; gap: 15px;
        }

        .summary-card {
            display: flex; 
            flex-direction: column; 
            justify-content: space-between; 
            height: 100%; 
        }
        .summary-card .card-title { font-size: 0.9em; color: var(--secondary-color); margin-bottom: 4px; }
        .summary-card .card-value { font-size: 2.5em; font-weight: 700; color: var(--text-color); line-height: 1; } 
        .status-badge { padding: 5px 10px; border-radius: 20px; font-size: 0.75em; font-weight: 700; display: inline-block; }
        .status-active { background-color: #d4edda; color: var(--active-status); }
        .status-inactive { background-color: #f8d7da; color: var(--inactive-status); }
        .nav-link { transition: all 0.2s; }
        .nav-link:hover { background-color: var(--background-color); color: var(--primary-color); }
        .nav-link.active { background-color: var(--background-color); color: var(--primary-color); font-weight: 500; }
        .nav-link.active i { color: var(--primary-color); }
        .logo-section { display: flex; align-items: center; padding-bottom: 30px; border-bottom: 1px solid var(--border-color); }
        .logo-icon { width: 30px; height: 30px; background-color: var(--primary-color); color: white; display: flex; align-items: center; justify-content: center; border-radius: 5px; font-weight: bold;
        margin-right: 10px; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        
        <aside class="sidebar">
            <div>
                <div class="logo-section mb-10">
                    <div class="logo-icon">
                        <?php echo e($storeInitial); ?>

                    </div>
                    <div class="logo-text">
                        <strong class="text-lg">
                            <?php echo e($storeName); ?>

                        </strong>
                        <span class="block text-xs text-gray-500">
                            <?php echo e($storeCity); ?>

                        </span>
                    </div>
                </div>
                <nav class="main-nav">
                    <ul>
                        <li class="mb-1">
                            <a href="<?php echo e(route('seller.dashboard', ['tab' => 'overview'])); ?>" class="nav-link flex items-center p-2 rounded-lg text-gray-700 <?php if($activeTab === 'overview'): ?> active <?php endif; ?>">
                                <i class="fas fa-chart-line mr-3 text-lg text-gray-500"></i> Dashboard
                            </a>
                        </li>
                        <li class="mb-1">
                            <a href="<?php echo e(route('seller.dashboard', ['tab' => 'products'])); ?>" class="nav-link flex items-center p-2 rounded-lg text-gray-700 <?php if($activeTab === 'products' || $activeTab === 'addProduct'): ?> active <?php endif; ?>">
                                <i class="fas fa-box-open mr-3 text-lg text-gray-500"></i> Produk
                            </a>
                        </li>
                        <li class="mb-1">
                            <a href="<?php echo e(route('seller.reports.index')); ?>" class="nav-link flex items-center p-2 rounded-lg text-gray-700 <?php if(request()->routeIs('seller.reports.index')): ?> active <?php endif; ?>">
                                <i class="fas fa-file-alt mr-3 text-lg text-gray-500"></i> Laporan
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            
            
            <div class="settings-nav pt-4 border-t" style="border-color: var(--border-color);">
                <ul>
                    <li class="mt-4">
                        <form action="<?php echo e(route('logout')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="w-full text-left nav-link flex items-center p-2 rounded-lg hover:bg-red-50 text-red-600 transition">
                                <i class="fas fa-sign-out-alt mr-3 text-lg"></i> Keluar
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </aside>

        
        <main class="main-content">
            <header class="header flex justify-between items-start mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">
                        <?php if($activeTab === 'overview'): ?>
                            Dashboard Toko
                        <?php elseif($activeTab === 'products'): ?>
                            Produk Saya
                        <?php elseif($activeTab === 'addProduct'): ?>
                            <?php if($editMode): ?> Edit Produk <?php else: ?> Tambah Produk Baru <?php endif; ?>
                        <?php endif; ?>
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">
                        <?php if($activeTab === 'overview'): ?>
                            Selamat Datang, <?php echo e($storeName); ?>! Ini ringkasan performa tokomu.
                        <?php elseif($activeTab === 'products'): ?>
                            Kelola semua produk yang Anda jual.
                        <?php elseif($activeTab === 'addProduct'): ?>
                            <?php if($editMode): ?> Perbarui detail produk <?php else: ?> Lengkapi informasi produk yang akan dijual. <?php endif; ?>
                        <?php endif; ?>
                    </p>
                </div>
                <div>
                    <img src="<?php echo e(asset('assets/images/logo.png')); ?>" alt="Logo QuadMarket" class="h-20"> 
                </div>
            </header>

            
            <?php if(session('success')): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>
            <?php if($errors->any()): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">Terdapat kesalahan pada input form:</span>
                    <ul class="mt-2 list-disc list-inside">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            
            <section id="overview-content" <?php if($activeTab !== 'overview'): ?> style="display: none;" <?php endif; ?>>
                <div class="grid grid-cols-2 gap-6 mb-8 w-1/2"> 
                    <?php $__currentLoopData = $summaryData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="card summary-card border border-gray-200 p-5"> 
                            <span class="card-title text-sm uppercase"><?php echo e($item->title); ?></span>
                            <strong class="card-value text-4xl mt-1 <?php echo e($item->class); ?>"><?php echo e($item->value); ?></strong>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div class="grid grid-cols-3 gap-6 mb-8">
                    <div class="card col-span-2 p-5">
                        <h3 class="text-xl font-semibold mb-4 text-gray-800">Stok Produk Berdasarkan Kategori</h3>
                        <div class="h-64">
                            <canvas id="salesBarChart"></canvas>
                        </div>
                    </div>
                    <div class="card p-5">
                        <h3 class="text-xl font-semibold mb-6 text-gray-800">Lokasi Pemberi Rating</h3>
                        <div class="flex items-center justify-between">
                            <div class="relative w-36 h-36 flex-shrink-0">
                                <canvas id="locationDoughnutChart"></canvas>
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <span class="text-2xl font-bold text-gray-800"><?php echo e($locationData['TotalOrders'] ?? 0); ?></span>
                                    <span class="text-xs text-gray-500">Rating</span>
                                </div>
                            </div>
                            <ul class="legend text-sm space-y-2 ml-4">
                                <?php
                                    $chartColors = ['#dc3545', '#007bff', '#ccc'];
                                ?>
                                <?php $__currentLoopData = $locationData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(is_numeric($key) && isset($item['Lokasi'])): ?>
                                        <li>
                                            <span class="inline-block w-3 h-3 rounded-full mr-2" style="background-color: <?php echo e($chartColors[$key]); ?>"></span>
                                            <?php echo e($item['Lokasi']); ?> (<?php echo e($item['Persentase']); ?>%)
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="recent-products-section">
                    <h2 class="text-xl font-semibold mb-4 text-gray-800">Daftar Produk Terbaru</h2>
                    <div class="table-responsive card p-0 overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">PRODUK</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">KATEGORI</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">HARGA</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">STOK</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">STATUS</th>
                                </tr>
                            </thead>
                            <tbody id="latestProductTableBody" class="bg-white divide-y divide-gray-200">
                                <?php $__empty_1 = true; $__currentLoopData = $latestProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <img src="<?php echo e($product->image_path ?? 'https://via.placeholder.com/40x40?text=P'); ?>"
                                                    onerror="this.onerror=null;this.src='https://via.placeholder.com/40';"
                                                    alt="<?php echo e($product->name); ?>" class="w-24 h-24 object-cover rounded-md mr-3 bg-gray-200">
                                                <div class="text-sm font-medium text-gray-900"><?php echo e($product->name); ?></div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo e($product->category->name ?? 'N/A'); ?>

                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-700">
                                            Rp <?php echo e(number_format($product->price, 0, ',', '.')); ?>

                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo e($product->stock); ?>

                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php
                                                $status = $product->status ?? ($product->stock > 0 ? 'Aktif' : 'NonAktif');
                                                $statusClass = $status === 'Aktif' ? 'status-active' : 'status-inactive';
                                            ?>
                                            <span class="status-badge <?php echo e($statusClass); ?>">
                                                <?php echo e($status); ?>

                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada produk terbaru.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            
            <section id="products-content" <?php if($activeTab !== 'products'): ?> style="display: none;" <?php endif; ?>>
                <div class="product-toolbar flex justify-between items-center mb-6">
                    <input type="text" placeholder="Cari Produk" class="p-2 border border-gray-300 rounded-lg w-1/3 focus:ring-blue-500 focus:border-blue-500" oninput="filterProducts(this.value)">
                    <a href="<?php echo e(route('seller.dashboard', ['tab' => 'addProduct'])); ?>" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center transition">
                        <i class="fas fa-plus mr-2"></i> Tambah Produk
                    </a>
                </div>

                <div class="grid grid-cols-4 gap-6 mb-8">
                    <?php $__currentLoopData = $productStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="card summary-card border border-gray-200 p-5">
                            <span class="card-title text-sm uppercase"><?php echo e(str_replace('_', ' ', strtoupper($key))); ?></span>
                            <strong class="card-value text-3xl mt-1"><?php echo e(number_format($value)); ?></strong>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div class="card p-0 overflow-hidden">
                    <div class="product-table-wrapper overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">PRODUK</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">KATEGORI</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">HARGA</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">STOK</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">STATUS</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">AKSI</th>
                                </tr>
                            </thead>
                            <tbody id="productTableBody" class="bg-white divide-y divide-gray-200">
                                <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <img src="<?php echo e($product->image_path ?? 'https://via.placeholder.com/40x40?text=P'); ?>"
                                                    onerror="this.onerror=null;this.src='https://via.placeholder.com/40';"
                                                    alt="<?php echo e($product->name); ?>" class="w-24 h-24 object-cover rounded-md mr-3 bg-gray-200">
                                                <div class="text-sm font-medium text-gray-900"><?php echo e($product->name); ?></div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo e($product->category->name ?? 'N/A'); ?>

                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-700">
                                            Rp <?php echo e(number_format($product->price, 0, ',', '.')); ?>

                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo e($product->stock); ?>

                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php
                                                $status = $product->status ?? ($product->stock > 0 ? 'Aktif' : 'NonAktif');
                                                $statusClass = $status === 'Aktif' ? 'status-active' : 'status-inactive';
                                            ?>
                                            <span class="status-badge <?php echo e($statusClass); ?>">
                                                <?php echo e($status); ?>

                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button onclick="editProduct(<?php echo e($product->id); ?>)"
                                                class="text-blue-600 hover:text-blue-800 transition mr-2 p-1">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                            <button onclick="deleteProductAction(<?php echo e($product->id); ?>)"
                                                class="text-red-600 hover:text-red-800 transition p-1">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada produk ditemukan.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-footer flex justify-between items-center p-4 border-t border-gray-200">
                        <?php echo e($products->appends(request()->except('page'))->links()); ?>

                    </div>
                </div>
            </section>

            
            <section id="add-product-content" <?php if($activeTab !== 'addProduct'): ?> style="display: none;" <?php endif; ?>>
                <div class="w-full max-w-4xl mx-auto">

                    <a href="<?php echo e(route('seller.dashboard', ['tab' => 'products'])); ?>" class="flex items-center text-gray-600 hover:text-blue-600 mb-6 transition duration-150">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Produk
                    </a>

                    <form id="add-product-form"
                        action="<?php echo e($editMode ? route('seller.products.update', $editProduct->id) : route('seller.products.store')); ?>"
                        method="POST"
                        enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <?php if($editMode): ?>
                            <?php echo method_field('PUT'); ?>
                        <?php endif; ?>

                        
                        <div class="card mb-6 p-6">
                            <h2 class="text-xl font-semibold mb-4 border-b pb-3 border-gray-200">Informasi Produk</h2>
                            <label for="foto_produk_input" class="photo-upload-area block border-2 border-dashed border-gray-300 rounded-lg p-12 text-center text-gray-500 cursor-pointer hover:border-blue-500 transition-colors">
                                <i class="fas fa-cloud-upload-alt text-3xl mb-2 text-gray-400"></i>
                                <div class="upload-text font-medium text-gray-700">Klik untuk mengunggah atau seret dan lepas</div>
                                <small class="block mt-2">Format: JPG, PNG. Maksimal 5MB.</small>

                                <input type="file" id="foto_produk_input" accept=".jpg,.jpeg,.png" style="display: none;" name="foto_produk">

                                <?php if($editMode && $editProduct->image_path): ?>
                                    <div class="mt-4">
                                        <img src="<?php echo e($editProduct->image_path); ?>" alt="Foto Lama" class="w-20 h-20 object-cover rounded-lg mx-auto border border-gray-200">
                                        <small class="block text-xs text-gray-500 mt-1">Foto Lama</small>
                                    </div>
                                <?php endif; ?>
                            </label>
                            <?php $__errorArgs = ['foto_produk'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-red-500 block mt-2"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div class="card mb-6 p-6">
                            <h2 class="text-xl font-semibold mb-4 border-b pb-3 border-gray-200">Informasi Produk</h2>
                            <div class="space-y-5">

                                <div>
                                    <label for="product-name-input" class="block text-sm font-medium text-gray-700 mb-1">Nama Produk</label>
                                    <input type="text" id="product-name-input" name="name" required class="form-input focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Contoh: Buku Panduan Skripsi"
                                        value="<?php echo e(old('name', $editMode ? $editProduct->name : '')); ?>">
                                </div>

                                <div>
                                    <label for="product-description-input" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                                    <textarea id="product-description-input" name="description" rows="4" class="form-input focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Jelaskan produk Anda secara detail..."><?php echo e(old('description', $editMode ? $editProduct->description : '')); ?></textarea>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="product-condition" class="block text-sm font-medium text-gray-700 mb-1">Kondisi</label>
                                        <select id="product-condition" name="condition" class="form-input focus:ring-blue-500 focus:border-blue-500">
                                            <option value="" <?php if(old('condition', $editMode ? $editProduct->condition : '') === ''): ?> selected <?php endif; ?>>Pilih kondisi barang</option>
                                            <option value="baru"  <?php if(old('condition', $editMode ? $editProduct->condition : '') === 'baru'): ?>  selected <?php endif; ?>>Baru</option>
                                            <option value="bekas" <?php if(old('condition', $editMode ? $editProduct->condition : '') === 'bekas'): ?> selected <?php endif; ?>>Bekas</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="product-min_order" class="block text-sm font-medium text-gray-700 mb-1">Minimal Pemesanan</label>
                                        <input type="number" id="product-min_order" name="min_order"
                                            value="<?php echo e(old('min_order', $editMode ? $editProduct->min_order : 1)); ?>" min="1" class="form-input focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>

                                <div>
                                    <label for="product-category-input" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                                    <select id="product-category-input" name="category_id" required class="form-input focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Pilih salah satu</option>
                                        <?php $__currentLoopData = $allCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($category->id); ?>"
                                                <?php if(old('category_id', $editMode ? $editProduct->category_id : '') == $category->id): ?> selected <?php endif; ?>>
                                                <?php echo e($category->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        
                        <div class="card mb-6 p-6">
                            <h2 class="text-xl font-semibold mb-4 border-b pb-3 border-gray-200">Harga & Stok</h2>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="product-price-input" class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp)</label>
                                    <input type="number" id="product-price-input" name="price" required min="0" class="form-input focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="0" value="<?php echo e(old('price', $editMode ? $editProduct->price : '')); ?>">
                                </div>
                                <div>
                                    <label for="product-stock-input" class="block text-sm font-medium text-gray-700 mb-1">Stok Barang</label>
                                    <input type="number" id="product-stock-input" name="stock" required min="0" class="form-input focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="0" value="<?php echo e(old('stock', $editMode ? $editProduct->stock : '')); ?>">
                                </div>
                            </div>
                        </div>

                        
                        <div class="card mb-6 p-6">
                            <div class="flex justify-between items-center mb-4 border-b pb-3 border-gray-200">
                                <h2 class="text-xl font-semibold">Varian Produk</h2>
                                <button type="button"
                                        id="add-variant-btn"
                                        class="bg-blue-500 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-blue-600 transition">
                                    <i class="fas fa-plus mr-1"></i> Tambah Varian
                                </button>
                            </div>
                            <p class="text-sm text-gray-500 mb-4">(Opsional)</p>

                            <div id="variant-list" class="space-y-3"></div>

                            <div id="no-variant-placeholder"
                                 class="text-sm text-gray-500 p-3 border border-gray-200 rounded-lg bg-gray-50">
                                Belum ada varian. Klik <strong>Tambah Varian</strong> untuk menambahkan varian seperti ukuran, warna, dll.
                            </div>
                        </div>

                        <div style="height: 100px;"></div>

                        <div class="action-footer">
                            <a href="<?php echo e(route('seller.dashboard', ['tab' => 'products'])); ?>" class="cancel-btn px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition">Batal</a>
                            <button type="submit" class="save-btn px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                                <?php echo e($editMode ? 'Simpan Perubahan' : 'Simpan Produk'); ?>

                            </button>
                        </div>
                    </form>
                </div>
            </section>
        </main>
    </div>

    
    <form id="delete-form" method="POST" style="display: none;">
        <?php echo csrf_field(); ?>
        <?php echo method_field('DELETE'); ?>
    </form>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            renderSalesBarChart();
            renderLocationDoughnutChart();
            handleFormInteractions();
        });

        function handleFormInteractions() {
            // =====================================================
            // 1. Upload / Preview Foto Produk
            // =====================================================
            const uploadArea = document.querySelector('.photo-upload-area');
            const fileInput  = document.getElementById('foto_produk_input'); 

            if (uploadArea && fileInput) {
                const statusElement = uploadArea.querySelector('.upload-text');

                fileInput.addEventListener('change', (e) => handleFiles(e.target.files));

                function handleFiles(files) {
                    if (files.length > 0) {
                        statusElement.innerHTML = `<span style="color: green; font-weight: bold;">1 file (${files[0].name}) dipilih!</span>`;
                        uploadArea.style.borderColor = 'green';
                    } else {
                        statusElement.innerHTML = `Klik untuk mengunggah atau seret dan lepas`;
                        uploadArea.style.borderColor = '#e9ecef';
                    }
                }
            }

            // =====================================================
            // 2. Varian Produk (Tambah/Hapus + Validasi Stok)
            // =====================================================
            const addVariantBtn         = document.getElementById('add-variant-btn');
            const variantList           = document.getElementById('variant-list');
            const noVariantPlaceholder  = document.getElementById('no-variant-placeholder');
            const stockInput            = document.getElementById('product-stock-input');
            const form                  = document.getElementById('add-product-form');

            let variantIndex = 0;

            if (addVariantBtn && variantList && form && stockInput) {
                // Tambah baris varian
                addVariantBtn.addEventListener('click', () => {
                    if (noVariantPlaceholder) {
                        noVariantPlaceholder.style.display = 'none';
                    }

                    const row = document.createElement('div');
                    row.className = 'variant-row flex gap-4 items-end';

                    row.innerHTML = `
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nama Varian
                            </label>
                            <input type="text"
                                   name="variants[${variantIndex}][name]"
                                   class="form-input focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Contoh: Ukuran S"
                                   required>
                        </div>
                        <div class="w-40">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Stok Varian
                            </label>
                            <input type="number"
                                   name="variants[${variantIndex}][stock]"
                                   class="form-input focus:ring-blue-500 focus:border-blue-500 variant-stock-input"
                                   min="0"
                                   value="0"
                                   required>
                        </div>
                        <button type="button"
                                class="text-red-600 text-sm font-medium hover:text-red-800 remove-variant-btn mb-2">
                            Hapus
                        </button>
                    `;

                    variantList.appendChild(row);
                    variantIndex++;

                    const removeBtn = row.querySelector('.remove-variant-btn');
                    removeBtn.addEventListener('click', () => {
                        row.remove();

                        if (variantList.children.length === 0 && noVariantPlaceholder) {
                            noVariantPlaceholder.style.display = 'block';
                        }
                    });
                });

                // Validasi stok sebelum submit
                form.addEventListener('submit', function (e) {
                    const mainStock = parseInt(stockInput.value || '0', 10);
                    const variantStockInputs = document.querySelectorAll('.variant-stock-input');

                    if (variantStockInputs.length === 0) {
                        return; // tidak ada varian -> bebas
                    }

                    let totalVariantStock = 0;
                    variantStockInputs.forEach((input) => {
                        const val = parseInt(input.value || '0', 10);
                        if (!isNaN(val)) {
                            totalVariantStock += val;
                        }
                    });

                    if (totalVariantStock !== mainStock) {
                        e.preventDefault();
                        alert(
                            `Total stok varian (${totalVariantStock}) ` +
                            `harus sama dengan stok barang (${mainStock}).\n\n` +
                            `Silakan sesuaikan stok varian atau stok barang utama.`
                        );
                    }
                });
            }
        }

        function editProduct(id) {
            window.location.href = "<?php echo e(route('seller.dashboard', ['tab' => 'addProduct'])); ?>" +
                `&mode=edit&id=${id}`;
        }

        function deleteProductAction(id) {
            const routeUrl     = "<?php echo e(route('seller.products.destroy', ['product' => '__ID__'])); ?>";
            const finalRouteUrl = routeUrl.replace('__ID__', id);
            
            if (confirm('Yakin ingin menghapus produk ini? Tindakan ini tidak dapat dibatalkan.')) {
                const form = document.getElementById('delete-form');
                form.action = finalRouteUrl;
                form.submit();
            }
        }

        function filterProducts(searchTerm) {
            const rows   = document.getElementById('productTableBody').getElementsByTagName('tr');
            const search = searchTerm.toLowerCase();
            for (let i = 0; i < rows.length; i++) {
                const productCell = rows[i].getElementsByTagName('td')[0];
                if (productCell) {
                    const productName = productCell.textContent.toLowerCase();
                    rows[i].style.display = productName.includes(search) ? "" : "none";
                }
            }
        }

        // =========================================================
        // LOGIKA CHART
        // =========================================================

        function renderSalesBarChart() {
            const data = <?php echo json_encode($salesByCategory, 15, 512) ?>;
            if (data.length === 0) return;
            const labels = data.map(item => item.Kategori);
            const values = data.map(item => item.Penjualan);
            const ctx = document.getElementById('salesBarChart');
            if (!ctx) return;
            new Chart(ctx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: '#007bff',
                        borderRadius: 5,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, display: false, grid: { display: false } },
                        x: { ticks: { font: { size: 10 } } }
                    },
                }
            });
        }

        function renderLocationDoughnutChart() {
            const data = <?php echo json_encode($locationData, 15, 512) ?>;
            const distributions = [
                { wilayah: data[0].Lokasi, persentase: data[0].Persentase, warna: '#dc3545' },
                { wilayah: data[1].Lokasi, persentase: data[1].Persentase, warna: '#007bff' },
                { wilayah: data[2].Lokasi, persentase: data[2].Persentase, warna: '#ccc' }
            ];
            const ctx = document.getElementById('locationDoughnutChart');
            if (!ctx) return;
            new Chart(ctx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: distributions.map(item => item.wilayah),
                    datasets: [{
                        data: distributions.map(item => item.persentase),
                        backgroundColor: distributions.map(item => item.warna),
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '80%',
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: {
                            label: (context) => {
                                const label = context.label || '';
                                const value = context.parsed;
                                return `${label}: ${value}%`;
                            }
                        } }
                    }
                }
            });
        }
    </script>
</body>
</html><?php /**PATH C:\xampp\htdocs\PPL QUADMARKET\QuadMarket\resources\views/seller/dashboard.blade.php ENDPATH**/ ?>