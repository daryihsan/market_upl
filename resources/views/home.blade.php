@php
use Illuminate\Support\Str;
// Variabel yang dikirim dari CatalogController: $trendingProducts, $categories

// 1. Ambil 6 kategori teratas
$visibleCategories = $categories->take(6); 
// 2. Tentukan kategori yang akan menjadi ikon "Lihat Semua" (kategori ke-7)
//    Jika ada kategori ke-7, ambil ikonnya. Jika tidak ada, gunakan fallback default.
$nextCategory = $categories->get(6); // Mengambil elemen pada index 6 (kategori ke-7)

$allCategoriesIcon = $nextCategory->icon_path ?? asset('assets/images/kategori-lainnya.jpg');

// Jika kategori ke-7 tidak ada atau tidak punya ikon, gunakan ikon kategori pertama (sebagai secondary fallback)
if (!$nextCategory || !$nextCategory->icon_path) {
    $firstVisibleCategory = $categories->where('icon_path', '!=', null)->first();
    $allCategoriesIcon = $firstVisibleCategory->icon_path ?? asset('assets/images/kategori-lainnya.jpg');
}
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda QuadMarket</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        html, body {
            font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", "Liberation Sans", sans-serif;
        }
        .category-card, .product-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            cursor: pointer;
        }
        .category-card:hover, .product-card:hover {
            transform: translateY(-5px); 
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); 
        }
        .hero-section {
            min-height: 400px;
            background-color: #e6f1f8;
            /* GANTI path gambar hero-bg.png dengan path gambar produk background Anda */
            background-image: url('assets/images/hero.png'); 
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .hero-search-input:focus {
            outline: none;
            box-shadow: none;
            border-color: transparent;
        }
    </style>
</head>
<body class="bg-gray-50">
    @include('layouts.header')

    {{-- HERO --}}
    <section class="hero-section text-center pt-20 pb-28 flex flex-col items-center justify-center">
        <div class="max-w-7xl mx-auto px-4">
            <h1 class="text-4xl sm:text-5xl font-bold text-gray-800 mb-4">
                Temukan Jutaan Produk Terbaik
            </h1>
            <p class="text-lg text-gray-600 mb-10">
                Jelajahi berbagai kategori dan temukan barang impian Anda dengan harga terbaik
            </p>

            <div class="max-w-xl mx-auto">
                <form
                    id="hero-search-form"
                    action="{{ route('search') }}"
                    method="GET"
                    class="relative flex items-center bg-white p-2 rounded-xl shadow-lg"
                >
                    <input
                        id="hero-search-input"
                        type="text"
                        name="q"
                        placeholder="Cari Produk..."
                        value="{{ request('q') }}"
                        autocomplete="off"
                        class="hero-search-input flex-grow pl-4 pr-4 py-3 border-none rounded-xl text-gray-700"
                    >

                    <button
                        type="submit"
                        class="bg-blue-600 text-white font-semibold py-3 px-8 rounded-xl hover:bg-blue-700 transition duration-150 flex items-center space-x-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span class="hidden sm:inline">Cari Sekarang</span>
                    </button>

                    {{-- DROPDOWN HASIL PENCARIAN --}}
                    <div
                        id="hero-search-dropdown"
                        class="absolute left-2 right-2 top-full mt-2 bg-white border border-gray-200 rounded-lg shadow-lg max-h-64 overflow-y-auto hidden z-50"
                    ></div>
                </form>
            </div>
        </div>
    </section>
    
    {{-- Kategori Dinamis --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20 z-10 relative">
        <div class="grid grid-cols-3 sm:grid-cols-7 gap-4 sm:gap-6 bg-white p-4 sm:p-8 rounded-2xl shadow-xl">
            
            {{-- Tampilkan maksimal 6 kategori teratas secara dinamis --}}
            @foreach($visibleCategories as $category)
            <a href="{{ route('katalog', ['kategori' => $category->name]) }}" class="category-card flex flex-col items-center text-center p-2 sm:p-3 hover:shadow-xl rounded-lg">
                {{-- Menggunakan icon_path dari DB --}}
                <img src="{{ $category->icon_path ?? asset('assets/images/kategori-placeholder.jpg') }}" 
                    alt="{{ $category->name }}" 
                    class="w-16 h-16 sm:w-20 sm:h-20 object-cover rounded-lg mb-2"
                    onerror="this.onerror=null;this.src='{{ asset('assets/images/kategori-placeholder.jpg') }}';"
                >
                <span class="text-xs sm:text-sm font-medium text-gray-700">{{ $category->name }}</span>
            </a>
            @endforeach

            {{-- Slot Terakhir: "Lihat Semua" (Slot ke-7), menggunakan ikon dari kategori ke-7 --}}
            <a href="{{ route('katalog') }}" class="category-card flex flex-col items-center text-center p-2 sm:p-3 hover:shadow-xl rounded-lg">
                {{-- Menggunakan ikon dari kategori ke-7 atau fallback --}}
                <img src="{{ $allCategoriesIcon }}" 
                    alt="Lihat Semua" 
                    class="w-16 h-16 sm:w-20 sm:h-20 object-cover rounded-lg mb-2"
                    onerror="this.onerror=null;this.src='{{ asset('assets/images/kategori-lainnya.jpg') }}';"
                >
                <span class="text-xs sm:text-sm font-medium text-gray-700">Lihat Semua</span>
            </a>

        </div>
    </section>
    
    {{-- Produk Tren Dinamis --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 mb-16">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Produk yang Sedang Tren</h2>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 lg:gap-6">
            
            @forelse($trendingProducts as $product)
            <a href="{{ route('product.detail', ['id' => $product->id]) }}" class="product-card block bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl">
                {{-- Tampilkan gambar, fallback ke placeholder --}}
                <img src="{{ $product->image_path ?? asset('assets/images/placeholder.png') }}" 
                    alt="{{ $product->name }}" 
                    class="w-full h-40 object-cover"
                    onerror="this.onerror=null;this.src='{{ asset('assets/images/placeholder.png') }}';">
                
                <div class="p-4">
                    <p class="text-sm font-medium text-gray-800 truncate mb-1">{{ $product->name }}</p>
                    <div class="flex items-center mb-2 text-xs">
                        <span class="font-semibold text-yellow-500 mr-1"> ⭐  {{ number_format($product->rating, 1) }}</span>
                        <span class="text-gray-500">({{ number_format($product->total_ulasan) }})</span>
                    </div>
                    {{-- Format harga --}}
                    <p class="text-lg font-bold text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    <div class="text-right">
                        {{-- Ambil lokasi dari relasi user --}}
                        <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full"> 📍  {{ $product->user->kabupaten ?? 'N/A' }}</span>
                    </div>
                </div>
            </a>
            @empty
            <p class="col-span-5 text-center text-gray-500">Tidak ada produk yang sedang tren saat ini.</p>
            @endforelse
        </div>
    </section>

    @include('layouts.footer')

    {{-- SCRIPT DROPDOWN SUGGESTION HERO SEARCH (PAKAI ENDPOINT /search?ajax=1 SAMA KAYAK HEADER) --}}
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const input    = document.getElementById('hero-search-input');
        const dropdown = document.getElementById('hero-search-dropdown');
        const form     = document.getElementById('hero-search-form');

        if (!input || !dropdown || !form) return;

        function clearDropdown() {
            dropdown.innerHTML = '';
            dropdown.classList.add('hidden');
        }

        input.addEventListener('keyup', function () {
            const q = input.value.trim();

            if (q === '') {
                clearDropdown();
                return;
            }

            fetch(`/search?ajax=1&q=${encodeURIComponent(q)}`)
                .then(res => res.json())
                .then(data => {
                    dropdown.innerHTML = '';
                    dropdown.classList.remove('hidden');

                    if (!data || data.length === 0) {
                        dropdown.innerHTML =
                            '<div class="px-4 py-2 text-sm text-gray-500 text-left">Tidak ada produk ditemukan</div>';
                        return;
                    }

                    data.forEach(item => {
                        const a = document.createElement('a');
                        a.href = `/product/${item.id}/detail`;
                        a.className = 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-left';
                        a.textContent = item.name;
                        dropdown.appendChild(a);
                    });
                })
                .catch(() => {
                    clearDropdown();
                });
        });

        // klik luar form -> tutup dropdown
        document.addEventListener('click', function (e) {
            if (!form.contains(e.target)) {
                clearDropdown();
            }
        });
    });
    </script>
</body>
</html>
