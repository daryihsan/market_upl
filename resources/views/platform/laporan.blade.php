<!DOCTYPE html>
<html lang="id">
<head>
    
<meta charset="UTF-8">
    
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    
<title>Laporan Platform QuadMarket</title>
    
<script src="https://cdn.tailwindcss.com"></script>
    
<link 
rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
<link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
<style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        
        .sidebar {
            width: 250px;
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            flex-shrink: 0;
            position: fixed;
            height: 100%;
        }
        
        .main-content {
            flex-grow: 1;
            padding: 30px;
            margin-left: 250px;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border-radius: 8px;
            color: #6b7280;
            transition: all 0.2s;
        }
        
        .nav-link:hover {
            background-color: #f3f4f6;
            color: #1f2937;
        }
        
        .nav-link.active {
            background-color: #e5e7eb;
            color: #007bff;
            font-weight: 600;
        }
        
        /* Tambahkan style untuk konsistensi icon rating di produk lengkap */
        
        .star-rating {
            color: #FBBF24; /* yellow-400 */
        }
    </style>
</head>
<body>
    
<div class="flex min-h-screen">
        {{-- SIDEBAR --}}
        
<aside class="sidebar">
            
<div class="p-6 border-b mb-4">
                
<h3 class="font-bold text-lg text-gray-800">Admin Menu</h3>
            </div>
            
<nav class="space-y-2">
                
<a href="{{ route('platform.dashboard') }}"
                    class="nav-link">
                    
<i 
class="fas fa-chart-line mr-3"></i> Dashboard
                </a>
                
<a href="{{ route('platform.verifikasi.list') }}"
                    class="nav-link">
                    
<i 
class="fas fa-check-circle mr-3"></i> Verifikasi
                    Penjual
                </a>
                
<a href="{{ route('platform.laporan') }}" class="nav-link active">
                    
<i 
class="fas fa-file-alt mr-3"></i> Laporan
                </a>
                
<a href="{{ route('platform.categories.index') }}"
                    class="nav-link">
                    
<i 
class="fas fa-tags mr-3"></i> Manajemen Kategori
                </a>
            </nav>
            
<div class="absolute bottom-0 w-full pr-4 border-t p-4
                bg-white">
                
<a href="#" class="nav-link">
                    
<i 
class="fas fa-cog mr-3"></i>
                    
<span>Pengaturan</span>
                </a>
                
<a href="#" class="nav-link">
                    
<i 
class="fas fa-question-circle mr-3"></i>
                    
<span>Bantuan</span>
                </a>
            </div>
        </aside>

        {{-- MAIN CONTENT --}}
        
<main class="main-content">
            
<div class="flex justify-between items-center mb-8">
                
<div>
                    
<h1 
class="text-2xl font-bold text-blue-900">Dashboard
                        Laporan</h1>
                    
<p class="text-sm text-gray-500 mt-1">
                        @if ($activeReportTab === 'penjual_status')
                            Daftar penjual yang terdaftar di QuadMarket.
                        @elseif ($activeReportTab === 'penjual_provinsi')
                            Laporan penjual berdasarkan provinsi.
                        @elseif ($activeReportTab === 'produk_lengkap')
                            Daftar produk lengkap dari seluruh penjual.
                        @endif
                    </p>
                </div>
                
<img src="{{ url('assets/images/logo.png') }}"
                    alt="QuadMarket" class="h-20">
            </div>

            {{-- TAB NAVIGATION (MASALAH 1 & 2 TERATASI) --}}
            
<div class="bg-white rounded-t-lg shadow">
                
<div class="flex border-b">
                    
<a href="{{ route('platform.laporan', ['report_tab' => 'penjual_status']) }}"
                        class="px-6 py-4 {{ $activeReportTab === 'penjual_status' ? 'text-blue-900 font-semibold border-b-2 border-blue-900' : 'text-gray-600' }} hover:text-gray-800 hover:bg-gray-50 transition-colors whitespace-nowrap">
                        Daftar Penjual
                    </a>
                    
<a href="{{ route('platform.laporan', ['report_tab' => 'penjual_provinsi']) }}"
                        class="px-6 py-4 {{ $activeReportTab === 'penjual_provinsi' ? 'text-blue-900 font-semibold border-b-2 border-blue-900' : 'text-gray-600' }} hover:text-gray-800 hover:bg-gray-50 transition-colors whitespace-nowrap">
                        Penjual per Provinsi
                    </a>
                    
<a href="{{ route('platform.laporan', ['report_tab' => 'produk_lengkap']) }}"
                        class="px-6 py-4 {{ $activeReportTab === 'produk_lengkap' ? 'text-blue-900 font-semibold border-b-2 border-blue-900' : 'text-gray-600' }} hover:text-gray-800 hover:bg-gray-50 transition-colors whitespace-nowrap">
                        Produk Lengkap
                    </a>
                </div>
            </div>

            {{-- KONTEN TAB DINAMIS --}}
            @if ($activeReportTab === 'penjual_status')
                {{-- KONTEN DAFTAR PENJUAL (EXISTING) --}}
                
<div class="bg-white shadow px-6 py-4">
                    
<div class="flex justify-between items-start flex-wrap
                        gap-4">
                        
<form method="GET" action="{{ route('platform.laporan') }}"
                            class="flex items-start space-x-3 flex-wrap gap-3">
                            {{-- Hidden input untuk menjaga tab --}}
                            
<input type="hidden" name="report_tab" value="penjual_status">
                            
<label class="text-gray-700 whitespace-nowrap
                                pt-2">Filter berdasarkan status :</label>
                            
<select name="status"
                                class="border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 min-w-[160px]">
                                
<option value="semua" {{ $statusFilter ===
                                    'semua' ? 'selected' : '' }}>Semua Status</option>
                                
<option value="aktif" {{ $statusFilter ===
                                    'aktif' ? 'selected' : '' }}>Aktif</option>
                                
<option value="tidak_aktif" {{ $statusFilter ===
                                    'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                            
<button type="submit" class="bg-gray-100 text-gray-700 font-semibold py-2 px-4 rounded hover:bg-gray-200 transition">Terapkan</button>
                        </form>

                        {{-- TOMBOL DOWNLOAD PDF (MASALAH 3: Sudah diperbaiki di controller) --}}
                        
<a href="{{ route('platform.laporan.download', ['type' => 'status', 'status' => $statusFilter]) }}"
                            class="flex items-center space-x-2 bg-blue-500 text-white px-5 py-2 rounded hover:bg-blue-600 transition">
                            
<i 
class="fas fa-download text-white"></i>
                            
<span>Unduh PDF</span>
                        </a>
                    </div>
                </div>

                
<div class="bg-white shadow rounded-b-lg overflow-hidden">
                    
<div class="overflow-x-auto">
                        
<table class="w-full">
                            
<thead class="bg-gray-50 border-b">
                                
<tr>
                                    
<th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 min-w-[180px]">Nama User</th>
                                    
<th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 min-w-[150px]">Nama PIC</th>
                                    
<th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 min-w-[200px]">Nama Toko</th>
                                    
<th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 min-w-[120px]">Status</th>                                    
                                </tr>
                            </thead>
                            
<tbody class="divide-y divide-gray-200">
                                @forelse($sellers as $seller)
                                    
<tr class="hover:bg-gray-50 transition-colors">
                                        
<td class="px-6 py-4 text-gray-600">{{ str_pad(strstr($seller->email_pic, '@', true), 3, '0', STR_PAD_LEFT) }}</td>
                                        
<td class="px-6 py-4 text-gray-800
                                            font-medium">{{ $seller->nama_pic ?? '-' }}</td>
                                        
<td class="px-6 py-4 text-gray-600">
                                            {{ str_pad($seller->nama_toko, 3, '0', STR_PAD_LEFT) }}
                                        </td>
                                        

<td class="px-6 py-4">
                                            @php
                                                $isActive = $seller->status_akun === 'active';
                                            @endphp
                                            
<span class="px-3 py-1 text-xs font-medium rounded-full whitespace-nowrap inline-block
                                                {{ $isActive ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                {{ $isActive ? 'Aktif' : 'Tidak Aktif' }}
                                            </span>
                                        </td>
                                        
                                    </tr>
                                @empty
                                    
<tr>
                                        
<td colspan="5" class="px-6 py-4
                                            text-center text-gray-500">
                                            Belum ada penjual yang terdaftar.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
<div class="bg-white px-6 py-4 border-t flex items-center
                        justify-between flex-wrap gap-4">
                        
<div class="text-sm text-gray-600">
                            Menampilkan 
<span class="font-semibold">{{
                                $sellers->count() }}</span> penjual
                        </div>
                    </div>
                </div>

            @elseif ($activeReportTab === 'penjual_provinsi')
                {{-- KONTEN PENJUAL PER PROVINSI --}}
                
<div class="bg-white shadow px-6 py-4">
                    
<div class="flex justify-between items-start flex-wrap
                        gap-4">
                        
<form method="GET" action="{{ route('platform.laporan') }}"
                            class="flex items-start space-x-3 flex-wrap gap-3">
                            
<input type="hidden" name="report_tab" value="penjual_provinsi">
                            
<label class="text-gray-700 whitespace-nowrap
                                pt-2">Filter berdasarkan Provinsi :</label>
                            
<select name="provinsi"
                                class="border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 min-w-[180px]">
                                
<option value="Semua" {{ $provinsiFilter ===
                                    'Semua' ? 'selected' : '' }}>Semua Provinsi</option>
                                @foreach ($provinces as $prov)
                                    
<option value="{{ $prov }}" {{ $provinsiFilter ===
                                        $prov ? 'selected' : '' }}>{{ $prov }}</option>
                                @endforeach
                            </select>
                            
<button type="submit" class="bg-gray-100 text-gray-700 font-semibold py-2 px-4 rounded hover:bg-gray-200 transition">Terapkan</button>
                        </form>

                        {{-- TOMBOL DOWNLOAD PDF --}}
                        
<a href="{{ route('platform.laporan.download', ['type' => 'provinsi', 'provinsi' => $provinsiFilter]) }}"
                            class="flex items-center space-x-2 bg-blue-500 text-white px-5 py-2 rounded hover:bg-blue-600 transition">
                            
<i 
class="fas fa-download text-white"></i>
                            
<span>Unduh PDF</span>
                        </a>
                    </div>
                </div>

                
<div class="bg-white shadow rounded-b-lg overflow-hidden">
                    
<div class="overflow-x-auto">
                        
<table class="w-full">
                            
<thead class="bg-gray-50 border-b">
                                
<tr>
                                    
<th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 min-w-[180px]">Nama Toko</th>
                                    
<th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 min-w-[150px]">Nama PIC</th>
                                    
<th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 min-w-[150px]">Propinsi</th>                                    
                                </tr>
                            </thead>
                            
<tbody class="divide-y divide-gray-200">
                                @forelse ($sellers as $seller)
                                    
<tr class="hover:bg-gray-50 transition-colors">
                                        
<td class="px-6 py-4 text-gray-800
                                            font-medium">{{ $seller->nama_toko ?? '-' }}</td>

<td class="px-6 py-4 text-gray-600">{{ $seller->nama_pic ?? $seller->nama ?? '-' }}</td>

<td class="px-6 py-4 text-gray-600">{{ $seller->provinsi ?? '-' }}</td>
                                    </tr>
                                @empty
                                    
<tr>
                                        
<td colspan="5" class="px-6 py-4
                                            text-center text-gray-500">
                                            Tidak ada data penjual.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
<div class="bg-white px-6 py-4 border-t flex items-center
                        justify-between flex-wrap gap-4">
                        
<div class="text-sm text-gray-600">
                            Menampilkan 
<span class="font-semibold">{{
                                $sellers->count() }}</span> penjual
                        </div>
                        {{-- Dummy pagination or actual pagination links --}}
                    </div>
                </div>

            @elseif ($activeReportTab === 'produk_lengkap')
                {{-- KONTEN PRODUK LENGKAP --}}
                
<div class="bg-white shadow px-6 py-4">
                    
<div class="flex justify-between items-start flex-wrap
                        gap-4">
                        
<form method="GET" action="{{ route('platform.laporan') }}"
                            class="flex items-start space-x-3 flex-wrap gap-3">
                            
<input type="hidden" name="report_tab" value="produk_lengkap">
                            
<label class="text-gray-700 whitespace-nowrap
                                pt-2">Filter berdasarkan:</label>

                            {{-- KATEGORI --}}
                            
<select name="kategori"
                                class="border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 min-w-[160px]">
                                
<option value="">Semua Kategori</option>
                                @foreach ($categories as $cat)
                                    
<option value="{{ $cat->id }}" {{ $categoryFilter ==
                                        $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>

                            {{-- RATING --}}
                            
<select name="rating"
                                class="border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 min-w-[140px]">
                                
<option value="">Semua Rating</option>
<option value="5+" {{ $ratingFilter === '5+' ? 'selected' : '' }}>5+⭐</option>
<option value="4+" {{ $ratingFilter === '4+' ? 'selected' : '' }}>4+⭐</option>
<option value="3+" {{ $ratingFilter === '3+' ? 'selected' : '' }}>3+⭐</option>
<option value="2+" {{ $ratingFilter === '2+' ? 'selected' : '' }}>2+⭐</option>
<option value="1+" {{ $ratingFilter === '1+' ? 'selected' : '' }}>1+⭐</option>
                            </select>
                            
<button type="submit" class="bg-gray-100 text-gray-700 font-semibold py-2 px-4 rounded hover:bg-gray-200 transition">Terapkan</button>
                        </form>

                        {{-- TOMBOL DOWNLOAD PDF --}}
                        
<a href="{{ route('platform.laporan.download', ['type' => 'produk', 'kategori' => $categoryFilter, 'rating' => $ratingFilter]) }}"
                            class="flex items-center space-x-2 bg-blue-500 text-white px-5 py-2 rounded hover:bg-blue-600 transition">
                            
<i 
class="fas fa-download text-white"></i>
                            
<span>Unduh PDF</span>
                        </a>
                    </div>
                </div>

                
<div class="bg-white shadow rounded-b-lg overflow-hidden">
                    
<div class="overflow-x-auto">
                        
<table class="w-full">
                            
<thead class="bg-gray-50 border-b">
                                
<tr>
                                    
<th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 min-w-[200px]">Produk</th>
                                    
<th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 min-w-[100px]">Kategori</th>
                                    
<th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 min-w-[120px]">Harga</th>
                                                                        
<th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 min-w-[100px]">Rating</th>
                                    
<th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 min-w-[150px]">Nama Toko</th>
                                    
<th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 min-w-[120px]">Propinsi</th>
                                </tr>
                            </thead>
                            
<tbody class="divide-y divide-gray-200">
                                @forelse ($products as $product)
                                    
<tr class="hover:bg-gray-50 transition-colors">
                                        
<td class="px-6 py-4 text-gray-800 font-medium">
                                            {{ $product->name }}
                                        </td>
                                        
<td class="px-6 py-4 text-gray-600">
                                            {{ $product->category->name ?? '-' }}
                                        </td>
                                        
<td class="px-6 py-4 text-gray-800 font-medium">
                                            Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </td>
                                        
<td class="px-6 py-4 text-gray-600">
                                            
<span class="star-rating">⭐</span>
                                            {{ number_format($product->rating ?? 0, 1) }}
                                        </td>
                                        
<td class="px-6 py-4 text-gray-600">
                                            {{ $product->user->nama_toko ?? 'N/A' }}
                                        </td>
                                        
<td class="px-6 py-4 text-gray-600">
                                            {{ $product->user->provinsi ?? 'N/A' }}
                                        </td>
                                    </tr>
                                @empty
                                    
<tr>
                                        
<td colspan="7" class="px-6 py-4
                                            text-center text-gray-500">
                                            Tidak ada data produk.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
<div class="bg-white px-6 py-4 border-t flex items-center
                        justify-between flex-wrap gap-4">
                        
<div class="text-sm text-gray-600">
                            Menampilkan 
<span class="font-semibold">{{
                                $products->count() }}</span> produk
                        </div>
                        {{-- Dummy pagination or actual pagination links --}}
                    </div>
                </div>
            @endif
        </main>
    </div>
</body>
</html>