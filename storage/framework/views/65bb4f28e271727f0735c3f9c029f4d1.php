<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Produk Lengkap</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        /* Menggunakan lebar sidebar 250px, fixed, dan box-shadow */
        .sidebar { width: 250px; background-color: #ffffff; padding: 20px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.05); flex-shrink: 0; position: fixed; height: 100%; }
        /* Main Content bergeser sesuai lebar sidebar 250px */
        .main-content { flex-grow: 1; padding: 30px; margin-left: 250px; } 
        /* Gaya Nav Link */
        .nav-link { display: flex; align-items: center; padding: 12px 16px; border-radius: 8px; color: #6b7280; transition: all 0.2s; }
        /* Gaya Hover yang disesuaikan */
        .nav-link:hover { 
            background-color: #e0f2fe; /* blue-50 */
            color: #1e40af; /* blue-800 */
        }
        /* Gaya Active (Warna biru utama #007bff diganti dengan warna dari dashboard: #e5e7eb) */
        .nav-link.active { background-color: #e5e7eb; color: #007bff; font-weight: 600; }
        .blue-gradient { background-image: linear-gradient(to right, #1E3A8A, #3B82F6); }
    </style>
</head>
<body>
    <div class="flex min-h-screen">
        
        <aside class="sidebar">
            <div class="p-6 border-b mb-4">
                <h3 class="font-bold text-lg text-gray-800">Admin Menu</h3>
            </div>
            <nav class="space-y-2">
                <a href="<?php echo e(route('platform.dashboard')); ?>" class="nav-link"><i class="fas fa-chart-line mr-3"></i> Dashboard</a>
                <a href="<?php echo e(route('platform.verifikasi.list')); ?>" class="nav-link"><i class="fas fa-check-circle mr-3"></i> Verifikasi Penjual</a>
                
                <a href="<?php echo e(route('platform.laporan')); ?>" class="nav-link active"><i class="fas fa-file-alt mr-3"></i> Laporan</a>
                <a href="<?php echo e(route('platform.categories.index')); ?>" class="nav-link"><i class="fas fa-tags mr-3"></i> Manajemen Kategori</a>
            </nav>

            <div class="absolute bottom-0 w-full pr-4 border-t p-4 bg-white">
                <a href="#" class="nav-link">
                    <i class="fas fa-cog mr-3"></i>
                    <span>Pengaturan</span>
                </a>
                <a href="#" class="nav-link">
                    <i class="fas fa-question-circle mr-3"></i>
                    <span>Bantuan</span>
                </a>
            </div>
        </aside>

        <main class="main-content">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-blue-900">Dashboard Laporan</h1>
                </div>
                
                <img src="<?php echo e(url('assets/images/logo.png')); ?>" alt="QuadMarket" class="h-20" style="height: 5rem;"> 
            </div>

            <div class="bg-white rounded-t-lg shadow">
                <div class="flex border-b">
                    <a href="<?php echo e(route('platform.laporan')); ?>" class="px-6 py-4 text-gray-600 hover:text-gray-800 hover:bg-gray-50 transition-colors whitespace-nowrap">
                        Daftar Penjual
                    </a>
                    <a href="<?php echo e(route('platform.laporan.provinsi')); ?>" class="px-6 py-4 text-gray-600 hover:text-gray-800 hover:bg-gray-50 transition-colors whitespace-nowrap">
                        Penjual per Provinsi
                    </a>
                    <a href="<?php echo e(route('platform.laporan.produk')); ?>" class="px-6 py-4 text-blue-900 font-semibold border-b-2 border-blue-900 whitespace-nowrap">
                        Produk Lengkap
                    </a>
                </div>
            </div>

            <div class="bg-white shadow px-6 py-4">
                <div class="grid grid-cols-[1fr_auto] items-center gap-4">

                    <div class="flex items-center flex-wrap gap-3">
                        <label class="text-gray-700 whitespace-nowrap pt-1">Filter berdasarkan :</label>

                        <select class="border border-gray-300 rounded px-4 py-2 min-w-[150px]">
                            <option>Semua Kategori</option>
                            <option>Pakaian</option>
                            <option>Makanan</option>
                            <option>Elektronik</option>
                            <option>Kesehatan</option>
                            <option>Olahraga</option>
                        </select>

                        <select class="border border-gray-300 rounded px-4 py-2 min-w-[150px]">
                            <option>Semua Rating</option>
                            <option>5 Bintang</option>
                            <option>4 Bintang</option>
                            <option>3 Bintang</option>
                            <option>2 Bintang</option>
                            <option>1 Bintang</option>
                        </select>

                        <select class="border border-gray-300 rounded px-4 py-2 min-w-[150px]">
                            <option>Semua Harga</option>
                            <option>< Rp 50.000</option>
                            <option>Rp 50.000 - Rp 100.000</option>
                            <option>Rp 100.000 - Rp 500.000</option>
                            <option>> Rp 500.000</option>
                        </select>
                    </div>

                    <button class="flex items-center space-x-2 bg-blue-500 text-white px-5 py-2 rounded hover:bg-blue-600 transition-colors whitespace-nowrap">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Unduh PDF</span>
                    </button>

                </div>
            </div>

            <div class="bg-white shadow rounded-b-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700 w-12">
                                    <input type="checkbox" class="w-4 h-4 rounded border-gray-300 cursor-pointer">
                                </th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 min-w-[250px]">Produk</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 min-w-[120px]">Kategori</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 min-w-[130px]">Harga</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 min-w-[80px]">Stok</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 min-w-[100px]">Rating</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 min-w-[150px]">Penjual</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-center">
                                    <input type="checkbox" class="w-4 h-4 rounded border-gray-300 cursor-pointer">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-blue-200 rounded flex-shrink-0 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800">Sepatu Sneakers</p>
                                            <p class="text-sm text-gray-500">Kasual</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">Pakaian</td>
                                <td class="px-6 py-4 text-gray-800 font-medium">Rp 230,000</td>
                                <td class="px-6 py-4 text-gray-600">30</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-1">
                                        <span class="text-yellow-400 text-lg">★★★★★</span>
                                        <span class="text-gray-600 text-sm font-medium">4.6</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">Toko Sejahtera</td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-center">
                                    <input type="checkbox" class="w-4 h-4 rounded border-gray-300 cursor-pointer">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-16 h-16 bg-gradient-to-br from-amber-100 to-amber-200 rounded flex-shrink-0 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800">Kopi Robusta Gayo</p>
                                            <p class="text-sm text-gray-500">250gr</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">Makanan</td>
                                <td class="px-6 py-4 text-gray-800 font-medium">Rp 80,000</td>
                                <td class="px-6 py-4 text-gray-600">8</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-1">
                                        <span class="text-yellow-400 text-lg">★★★★★</span>
                                        <span class="text-gray-600 text-sm font-medium">5.0</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">Toko Tembakang</td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-center">
                                    <input type="checkbox" class="w-4 h-4 rounded border-gray-300 cursor-pointer">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-16 h-16 bg-gradient-to-br from-purple-100 to-purple-200 rounded flex-shrink-0 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800">Laptop Gaming ROG</p>
                                            <p class="text-sm text-gray-500">15.6 inch</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">Elektronik</td>
                                <td class="px-6 py-4 text-gray-800 font-medium">Rp 15,500,000</td>
                                <td class="px-6 py-4 text-gray-600">5</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-1">
                                        <span class="text-yellow-400 text-lg">★★★★★</span>
                                        <span class="text-gray-600 text-sm font-medium">4.8</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">Toko Makmur Jaya</td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-center">
                                    <input type="checkbox" class="w-4 h-4 rounded border-gray-300 cursor-pointer">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-16 h-16 bg-gradient-to-br from-green-100 to-green-200 rounded flex-shrink-0 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800">Vitamin C 1000mg</p>
                                            <p class="text-sm text-gray-500">60 Tablet</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">Kesehatan</td>
                                <td class="px-6 py-4 text-gray-800 font-medium">Rp 125,000</td>
                                <td class="px-6 py-4 text-gray-600">50</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-1">
                                        <span class="text-yellow-400 text-lg">★★★★☆</span>
                                        <span class="text-gray-600 text-sm font-medium">4.3</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">Warung Berkah</td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-center">
                                    <input type="checkbox" class="w-4 h-4 rounded border-gray-300 cursor-pointer">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-16 h-16 bg-gradient-to-br from-red-100 to-red-200 rounded flex-shrink-0 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800">Matras Yoga Premium</p>
                                            <p class="text-sm text-gray-500">180 x 60 cm</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">Olahraga</td>
                                <td class="px-6 py-4 text-gray-800 font-medium">Rp 350,000</td>
                                <td class="px-6 py-4 text-gray-600">15</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-1">
                                        <span class="text-yellow-400 text-lg">★★★★★</span>
                                        <span class="text-gray-600 text-sm font-medium">4.9</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">Kios Mentari</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="bg-white px-6 py-4 border-t flex items-center justify-between flex-wrap gap-4">
                    <div class="text-sm text-gray-600">
                        Menampilkan <span class="font-semibold">1</span> sampai <span class="font-semibold">6</span> dari <span class="font-semibold">97</span> hasil
                    </div>
                    <div class="flex items-center space-x-2">
                        <button class="p-2 rounded hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button class="px-3 py-1 rounded bg-blue-500 text-white font-medium transition-colors">1</button>
                        <button class="px-3 py-1 rounded hover:bg-gray-100 text-gray-700 transition-colors">2</button>
                        <span class="text-gray-600">...</span>
                        <button class="px-3 py-1 rounded hover:bg-gray-100 text-gray-700 transition-colors">10</button>
                        <button class="p-2 rounded hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html><?php /**PATH C:\xampp\htdocs\PPL QUADMARKET\QuadMarket\resources\views/platform/produk.blade.php ENDPATH**/ ?>