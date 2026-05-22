<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pencarian - QuadMarket</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", "Liberation Sans", sans-serif;
            background-color: #f6f7f8;
        }
        .product-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            cursor: pointer;
        }
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body class="bg-gray-50">

    {{-- pakai header lama kamu --}}
    @include('layouts.header')

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <p class="text-sm text-gray-500 mb-4">
            Home / Hasil Pencarian
        </p>

        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                    Hasil Pencarian
                </h1>
                <p class="text-gray-500 text-sm mt-1">
                    Kata kunci:
                    <span class="font-semibold text-gray-800">"{{ $q }}"</span>
                    • Menampilkan {{ number_format($products->total(), 0, ',', '.') }} produk
                </p>

                {{-- TOMBOL KEMBALI --}}
                <a href="{{ url('/') }}"
                class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm font-semibold mt-2">
                    ← Kembali ke Beranda
                </a>
            </div>


            {{-- form kecil buat ganti kata kunci langsung di halaman ini --}}
            <form action="{{ route('search') }}" method="GET" class="w-full sm:w-80">
                <div class="relative">
                    <input
                        type="text"
                        name="q"
                        value="{{ $q }}"
                        placeholder="Cari lagi..."
                        class="w-full border border-gray-300 rounded-full py-2 pl-4 pr-10 text-sm text-gray-700 focus:ring-blue-600 focus:border-blue-600"
                    >
                    <button type="submit" class="absolute right-0 top-0 mt-2 mr-3">
                        <svg class="w-5 h-5 text-gray-400 hover:text-blue-500"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        {{-- FILTER SECTION --}}
        <div class="mb-6 bg-white p-4 rounded-lg shadow">
            <form id="filterForm" action="{{ route('search') }}" method="GET">
                <input type="hidden" name="q" value="{{ $q }}">
                <input type="hidden" name="toko" id="filter_toko" value="{{ $toko }}">
                <input type="hidden" name="kategori" id="filter_kategori" value="{{ $kategori }}">
                <input type="hidden" name="provinsi" id="filter_provinsi" value="{{ $provinsi }}">
                <input type="hidden" name="kabupaten" id="filter_kabupaten" value="{{ $kabupaten }}">
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    {{-- Search Shop Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cari Nama Toko</label>
                        <div class="flex gap-2">
                            <input
                                type="text"
                                id="search_toko"
                                placeholder="Nama toko..."
                                class="flex-1 border border-gray-300 rounded-lg py-2 px-3 text-sm text-gray-700 focus:ring-blue-600 focus:border-blue-600"
                                value="{{ $toko }}"
                            >
                            <button
                                type="button"
                                onclick="searchShop()"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium"
                            >
                                Cari
                            </button>
                        </div>
                    </div>

                    {{-- Filter Category --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Filter Kategori</label>
                        <select
                            id="select_kategori"
                            class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm text-gray-700 focus:ring-blue-600 focus:border-blue-600"
                            onchange="filterSearch()"
                        >
                            <option value="">-- Semua Kategori --</option>
                            @foreach ($allCategories as $cat)
                                <option value="{{ $cat->id }}" {{ $kategori == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Province --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Filter Provinsi</label>
                        <select
                            id="select_provinsi"
                            class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm text-gray-700 focus:ring-blue-600 focus:border-blue-600"
                            onchange="filterSearch()"
                        >
                            <option value="">-- Semua Provinsi --</option>
                            @foreach ($allProvinsi as $prov)
                                <option value="{{ $prov }}" {{ $provinsi === $prov ? 'selected' : '' }}>
                                    {{ $prov }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter City/Regency --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Filter Kabupaten/Kota</label>
                        <select
                            id="select_kabupaten"
                            class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm text-gray-700 focus:ring-blue-600 focus:border-blue-600"
                            onchange="filterSearch()"
                        >
                            <option value="">-- Semua Kabupaten/Kota --</option>
                            @foreach ($allKabupaten as $kab)
                                <option value="{{ $kab }}" {{ $kabupaten === $kab ? 'selected' : '' }}>
                                    {{ $kab }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Clear Filters Button --}}
                    @if ($provinsi !== '' || $kabupaten !== '' || $kategori !== '' || $toko !== '')
                        <div class="flex items-end">
                            <button
                                type="button"
                                onclick="clearFilters()"
                                class="w-full bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded-lg text-sm font-medium"
                            >
                                ✕ Hapus Semua Filter
                            </button>
                        </div>
                    @endif
                </div>
            </form>
        </div>

        <script>
            function filterSearch() {
                document.getElementById('filter_toko').value = document.getElementById('search_toko').value;
                document.getElementById('filter_kategori').value = document.getElementById('select_kategori').value;
                document.getElementById('filter_provinsi').value = document.getElementById('select_provinsi').value;
                document.getElementById('filter_kabupaten').value = document.getElementById('select_kabupaten').value;
                document.getElementById('filterForm').submit();
            }

            function searchShop() {
                document.getElementById('filter_toko').value = document.getElementById('search_toko').value;
                document.getElementById('filter_kategori').value = document.getElementById('select_kategori').value;
                document.getElementById('filter_provinsi').value = document.getElementById('select_provinsi').value;
                document.getElementById('filter_kabupaten').value = document.getElementById('select_kabupaten').value;
                document.getElementById('filterForm').submit();
            }

            function clearFilters() {
                document.getElementById('search_toko').value = '';
                document.getElementById('select_kategori').value = '';
                document.getElementById('select_provinsi').value = '';
                document.getElementById('select_kabupaten').value = '';
                document.getElementById('filter_toko').value = '';
                document.getElementById('filter_kategori').value = '';
                document.getElementById('filter_provinsi').value = '';
                document.getElementById('filter_kabupaten').value = '';
                document.getElementById('filterForm').submit();
            }
        </script>

        {{-- HASIL PENCARIAN ATAU PESAN TIDAK DITEMUKAN --}}
        @if ($products->count() === 0)
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Hasil Pencarian</h3>
                <p class="text-gray-500 mb-4">
                    @if ($q !== '' || $toko !== '')
                        Tidak ada produk atau toko yang cocok dengan kriteria pencarian Anda.
                    @elseif ($kategori !== '')
                        Tidak ada produk di kategori yang Anda pilih.
                    @elseif ($provinsi !== '' || $kabupaten !== '')
                        Tidak ada produk yang tersedia di lokasi yang Anda pilih.
                    @else
                        Silakan coba dengan kriteria pencarian yang berbeda.
                    @endif
                </p>
                <a href="{{ route('search') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-semibold">
                    ← Kembali ke Pencarian
                </a>
            </div>
        @else
            {{-- GRID PRODUK --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
                @foreach ($products as $product)
                    <a href="{{ route('product.detail', ['id' => $product->id]) }}"
                       class="bg-white rounded-lg shadow hover:shadow-lg transition border border-gray-100 overflow-hidden product-card">
                        {{-- Gambar Produk --}}
                        @if ($product->image_path)
                            <img src="{{ $product->image_path }}" class="w-full h-40 object-cover" alt="{{ $product->name }}">
                        @else
                            <div class="w-full h-40 bg-gray-200 flex items-center justify-center text-gray-500 text-sm">
                                Tidak ada gambar
                            </div>
                        @endif

                        <div class="p-3">
                            <p class="text-sm font-medium text-gray-800 truncate mb-1">
                                {{ $product->name }}
                            </p>
                            <p class="text-xl font-bold text-gray-900 mb-1">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </p>
                            <p class="text-xs text-gray-500 mb-2">
                                {{ $product->user->nama_toko ?? 'N/A' }} • {{ $product->user->kabupaten ?? 'N/A' }}
                            </p>
                            <div class="flex items-center text-xs">
                                <span class="font-semibold text-yellow-500 mr-1">
                                    ⭐ {{ number_format($product->rating, 1) }}
                                </span>
                                <span class="text-gray-500">
                                    ({{ number_format($product->total_ulasan, 0, ',', '.') }})
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-6 flex justify-center">
                {{ $products->links() }}
            </div>
        @endif
    </main>

    @include('layouts.footer')

</body>
</html>