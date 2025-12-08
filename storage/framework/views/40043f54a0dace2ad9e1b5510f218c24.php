<?php
// Data yang dikirim dari ReportController
$activeReportTab = $activeReportTab ?? 'rating';
$categories      = $categories ?? collect([]);
$reportData      = $reportData ?? collect([]);

// DATA TOKO DARI USER LOGIN
$user        = auth()->user();
$storeName   = $user->nama_toko ?? 'Nama Toko';
$storeInitial = mb_substr($storeName, 0, 1, 'UTF-8');
$storeCity   = $user->kabupaten ?? 'Semarang';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjual | QuadMarket</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #007bff; --secondary-color: #6c757d; --background-color: #f8f9fa;
            --card-background: #ffffff; --text-color: #212529; --border-color: #e9ecef;
            --active-status: #28a745; --inactive-status: #dc3545; --warn-status: #ffc107;
        }
        body { background-color: var(--background-color); color: var(--text-color); font-family: 'Inter', sans-serif; }
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
        .card { background-color: var(--card-background); padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); }
        .nav-link.active { background-color: var(--background-color); color: var(--primary-color); font-weight: 500; }
        .tab-link { padding: 10px 15px; border-bottom: 3px solid transparent; font-weight: 500; cursor: pointer; transition: all 0.2s; }
        .tab-link.active { color: var(--primary-color); border-bottom-color: var(--primary-color); }
        .status-badge { padding: 5px 10px; border-radius: 20px; font-size: 0.75em; font-weight: 700; display: inline-block; }
        .status-aman { background-color: #d4edda; color: var(--active-status); }
        .status-hampir-habis { background-color: #fff3cd; color: #856404; }
        .status-habis { background-color: #f8d7da; color: var(--inactive-status); }
        .action-icon:hover { color: var(--primary-color); }
        .logo-section { display: flex; align-items: center; padding-bottom: 30px; border-bottom: 1px solid var(--border-color); }
        .logo-icon { width: 30px; height: 30px; background-color: var(--primary-color); color: white; display: flex; align-items: center; justify-content: center; border-radius: 5px; font-weight: bold; margin-right: 10px; }
        .settings-nav .nav-link { transition: all 0.2s; }
        .settings-nav .nav-link:hover { background-color: #f8d7da; color: var(--inactive-status); } 
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
                        <strong class="text-lg"><?php echo e($storeName); ?></strong>
                        <span class="block text-xs text-gray-500"><?php echo e($storeCity); ?></span>
                    </div>
                </div>
                <nav class="main-nav">
                    <ul>
                        <li class="mb-1">
                            <a href="<?php echo e(route('seller.dashboard', ['tab' => 'overview'])); ?>" class="nav-link flex items-center p-2 rounded-lg text-gray-700">
                                <i class="fas fa-chart-line mr-3 text-lg text-gray-500"></i> Dashboard
                            </a>
                        </li>
                        <li class="mb-1">
                            <a href="<?php echo e(route('seller.dashboard', ['tab' => 'products'])); ?>" class="nav-link flex items-center p-2 rounded-lg text-gray-700">
                                <i class="fas fa-box-open mr-3 text-lg text-gray-500"></i> Produk
                            </a>
                        </li>
                        <li class="mb-1">
                            <a href="<?php echo e(route('seller.reports.index')); ?>" class="nav-link flex items-center p-2 rounded-lg text-gray-700 active">
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
            <header class="header flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">
                        <?php if($activeReportTab === 'rating'): ?>
                            Laporan Rating
                        <?php elseif($activeReportTab === 'stock'): ?>
                            Laporan Stok
                        <?php elseif($activeReportTab === 'low_stock'): ?>
                            Laporan Peringatan Stok Rendah
                        <?php endif; ?>
                    </h1>
                    <p class="text-sm text-gray-500">
                        <?php if($activeReportTab === 'low_stock'): ?>
                            Daftar produk dengan stok kurang dari atau sama dengan 2 unit.
                        <?php else: ?>
                            Kelola dan unduh laporan performa produk Anda.
                        <?php endif; ?>
                    </p>
                </div>
                
                <a href="<?php echo e(route('seller.reports.download', array_merge(request()->all(), ['type' => $activeReportTab]))); ?>" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center transition">
                    <i class="fas fa-download mr-2"></i> Unduh PDF
                </a>
            </header>

            
            <div class="flex border-b border-gray-200 mb-6">
                <a href="<?php echo e(route('seller.reports.index', ['report_tab' => 'rating'])); ?>" class="tab-link <?php if($activeReportTab === 'rating'): ?> active <?php endif; ?>">Laporan Rating</a>
                <a href="<?php echo e(route('seller.reports.index', ['report_tab' => 'stock'])); ?>" class="tab-link <?php if($activeReportTab === 'stock'): ?> active <?php endif; ?>">Laporan Stok</a>
                <a href="<?php echo e(route('seller.reports.index', ['report_tab' => 'low_stock'])); ?>" class="tab-link <?php if($activeReportTab === 'low_stock'): ?> active <?php endif; ?>">Laporan Peringatan Stok Rendah</a>
            </div>

            <div class="card p-0">
                <?php if($activeReportTab === 'rating'): ?>
                    
                    <div class="filter-bar p-4 flex gap-4 items-center border-b border-gray-200">
                        <form method="GET" class="flex gap-4 items-center" action="<?php echo e(route('seller.reports.index')); ?>">
                            <input type="hidden" name="report_tab" value="rating">
                            <select name="category_id" class="p-2 border border-gray-300 rounded-lg">
                                <option value="">Semua Kategori</option>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($category->id); ?>" <?php if(request('category_id') == $category->id): ?> selected <?php endif; ?>><?php echo e($category->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <select name="rating_min" class="p-2 border border-gray-300 rounded-lg">
                                <option value="1" <?php if(request('rating_min') == 1): ?> selected <?php endif; ?>>Rating Min: 1</option>
                                <option value="3" <?php if(request('rating_min') == 3): ?> selected <?php endif; ?>>Rating Min: 3</option>
                            </select>
                            <select name="rating_max" class="p-2 border border-gray-300 rounded-lg">
                                <option value="5" <?php if(request('rating_max') == 5): ?> selected <?php endif; ?>>Rating Max: 5</option>
                            </select>
                            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 px-4 rounded-lg transition">Terapkan</button>
                        </form>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PRODUK</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">RATING RATA-RATA</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TOTAL ULASAN</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__empty_1 = true; $__currentLoopData = $reportData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="px-6 py-4"><input type="checkbox"></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <img src="<?php echo e($product->image_path ?? 'https://via.placeholder.com/40x40?text=P'); ?>" alt="<?php echo e($product->name); ?>" class="w-24 h-24 object-cover rounded-md mr-3 bg-gray-100">
                                            <div class="text-sm font-medium text-gray-900"><?php echo e($product->name); ?></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="text-yellow-500 flex items-center">
                                            <?php $rating = $product->rating ?? 0; ?>
                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star text-xs <?php if($i <= floor($rating)): ?> text-yellow-500 <?php else: ?> text-gray-300 <?php endif; ?>"></i>
                                            <?php endfor; ?>
                                            <span class="ml-1"><?php echo e(number_format($rating, 1)); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo e(number_format($product->total_ulasan ?? 0)); ?>

                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Tidak ada data rating produk.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                <?php elseif($activeReportTab === 'stock'): ?>
                    
                    <div class="filter-bar p-4 flex gap-4 items-center border-b border-gray-200">
                        <form method="GET" class="flex gap-4 items-center" action="<?php echo e(route('seller.reports.index')); ?>">
                            <input type="hidden" name="report_tab" value="stock">
                            <input type="text" name="search" placeholder="Cari Produk" class="p-2 border border-gray-300 rounded-lg w-64" value="<?php echo e(request('search')); ?>">
                            <select name="category_id" class="p-2 border border-gray-300 rounded-lg">
                                <option value="">Filter Kategori</option>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($category->id); ?>" <?php if(request('category_id') == $category->id): ?> selected <?php endif; ?>><?php echo e($category->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <select name="sort" class="p-2 border border-gray-300 rounded-lg">
                                <option value="stock_desc">Urutan Stok (Default)</option>
                                <option value="stock_desc" <?php if(request('sort') == 'stock_desc'): ?> selected <?php endif; ?>>Stok Terbanyak</option>
                                <option value="stock_asc"  <?php if(request('sort') == 'stock_asc'): ?>  selected <?php endif; ?>>Stok Tersedikit</option>
                            </select>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">Terapkan</button>
                        </form>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PRODUK</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">KATEGORI</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">HARGA</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">STOK</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">RATING</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__empty_1 = true; $__currentLoopData = $reportData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap"><input type="checkbox"></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <img src="<?php echo e($product->image_path ?? 'https://via.placeholder.com/40x40?text=P'); ?>" alt="<?php echo e($product->name); ?>" class="w-10 h-10 object-cover rounded-md mr-3 bg-gray-100">
                                            <div class="text-sm font-medium text-gray-900"><?php echo e($product->name); ?></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($product->category->name ?? 'N/A'); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">Rp <?php echo e(number_format($product->price, 0, ',', '.')); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($product->stock); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e(number_format($product->rating ?? 0, 1)); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data stok produk.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                <?php elseif($activeReportTab === 'low_stock'): ?>
                    
                    <div class="filter-bar p-4 flex justify-between items-center border-b border-gray-200">
                        <form method="GET" class="flex gap-4 items-center" action="<?php echo e(route('seller.reports.index')); ?>">
                            <input type="hidden" name="report_tab" value="low_stock">
                            <input type="text" name="search" placeholder="Cari Produk" class="p-2 border border-gray-300 rounded-lg w-64" value="<?php echo e(request('search')); ?>">
                            <select name="category_id" class="p-2 border border-gray-300 rounded-lg">
                                <option value="">Filter Kategori</option>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($category->id); ?>" <?php if(request('category_id') == $category->id): ?> selected <?php endif; ?>><?php echo e($category->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">Terapkan</button>
                        </form>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PRODUK</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">KATEGORI</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">HARGA</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">STOK</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">STATUS</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__empty_1 = true; $__currentLoopData = $reportData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap"><input type="checkbox"></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <img src="<?php echo e($product->image_path ?? 'https://via.placeholder.com/40x40?text=P'); ?>" alt="<?php echo e($product->name); ?>" class="w-10 h-10 object-cover rounded-md mr-3 bg-gray-100">
                                            <div class="text-sm font-medium text-gray-900"><?php echo e($product->name); ?></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($product->category->name ?? 'N/A'); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">Rp <?php echo e(number_format($product->price, 0, ',', '.')); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($product->stock); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                            $stockStatus = $product->stock === 0 ? 'Stok Habis' : ($product->stock <= 2 ? 'Stok Hampir Habis' : 'Stok Aman');
                                            $statusClass = $product->stock === 0 ? 'status-habis' : ($product->stock <= 2 ? 'status-hampir-habis' : 'status-aman');
                                        ?>
                                        <span class="status-badge <?php echo e($statusClass); ?>">
                                            <?php echo e($stockStatus); ?>

                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="editProduct(<?php echo e($product->id); ?>)" class="text-blue-600 hover:text-blue-900 transition mr-2 action-icon"><i class="fas fa-pencil-alt"></i></button>
                                        <button onclick="deleteProductAction(<?php echo e($product->id); ?>)" class="text-red-600 hover:text-red-900 transition action-icon"><i class="fas fa-trash-alt"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada produk dengan stok rendah.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <div class="table-footer flex justify-between items-center p-4 border-t border-gray-200">
                    <div>Menampilkan <?php echo e($reportData->firstItem() ?? 0); ?> sampai <?php echo e($reportData->lastItem() ?? 0); ?> dari <?php echo e($reportData->total()); ?> hasil</div>
                    <?php echo e($reportData->appends(request()->except('page'))->links()); ?>

                </div>
            </div>
        </main>
    </div>

    
    <form id="delete-form" method="POST" style="display:none;">
        <?php echo csrf_field(); ?>
        <?php echo method_field('DELETE'); ?>
    </form>

    <script>
        function editProduct(id) {
            window.location.href = "<?php echo e(route('seller.dashboard', ['tab' => 'addProduct'])); ?>" + `&mode=edit&id=${id}`;
        }
        function deleteProductAction(id) {
            const routeUrl     = "<?php echo e(route('seller.products.destroy', ['product' => '__ID__'])); ?>";
            const finalRouteUrl = routeUrl.replace('__ID__', id);
            
            if (confirm('Yakin ingin menghapus produk ini?')) {
                const form = document.getElementById('delete-form');
                if (form) {
                    form.action = finalRouteUrl;
                    form.submit();
                }
            }
        }
    </script>
</body>
</html><?php /**PATH C:\xampp\htdocs\PPL QUADMARKET\QuadMarket\resources\views/seller/reports/reports.blade.php ENDPATH**/ ?>