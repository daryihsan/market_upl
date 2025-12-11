<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $currentCategory ?? 'Katalog Produk' }} - Katalog Pembeli QuadMarket</title> 
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
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
        .quad-logo-color {
            color: #4c98e1;
        }
    </style>
</head>

<!-- INI HEADER KHUSUS -->
<body class="bg-gray-50">
    <header class="shadow-md sticky top-0 bg-white z-50">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex justify-between items-center">

        <!-- KIRI: Logo & Menu -->
            <div class="flex items-center space-x-6">
                <div class="flex items-center space-x-2">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="QuadMarket Logo" class="h-12">
                    </a>
                </div>
            </div>

            <!-- TENGAH: Search Bar -->
            <div class="flex-1 max-w-sm mx-8 hidden md:block">
                <form action="{{ route('search') }}" method="GET" class="relative" id="header-search-form">

                    <input type="text" name="q" id="header-search-input" placeholder="Cari Produk..."
                        autocomplete="off"
                        value="{{ request('q') }}"
                        class="w-full pl-4 pr-10 py-2 border border-gray-300 rounded-lg
                                focus:outline-none focus:ring-1 focus:ring-blue-500 text-sm">

                    <button type="submit" class="absolute right-0 top-0 mt-2 mr-3">
                        <svg class="w-5 h-5 text-gray-400 hover:text-blue-500"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>

                    <!-- DROPDOWN HASIL LIVE SEARCH -->
                    <div id="live-search-results"
                        class="absolute bg-white border border-gray-200 rounded-lg shadow w-full mt-1 hidden z-50"></div>

                </form>
            </div>

            <!-- KANAN: Tombol Login -->
            <div class="flex items-center">
                <a href="{{ route('login.pilih') }}"
                class="bg-blue-600 text-white font-semibold py-2 px-5 rounded-xl hover:bg-blue-700 transition duration-150 flex items-center space-x-2">
                    Masuk
                </a>
            </div>
        </nav>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <p class="text-sm text-gray-500 mb-6">
            <a href="{{ route('home') }}" class="text-blue-600 hover:underline">Home</a>
            / {{ $currentCategory ?? 'Semua Kategori' }}
        </p>

        <div class="flex flex-col lg:flex-row gap-8">
            
            <div class="w-full lg:w-72 bg-white p-6 rounded-lg shadow-lg flex-shrink-0">
                <h2 class="text-xl font-bold text-gray-800 mb-6 border-b pb-3">Filter</h2>
                <form method="GET" action="{{ route('katalog') }}" class="space-y-6">
                    
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-3">Kategori</h3>
                        <div class="space-y-2 text-sm text-gray-600">
                            @php
                                $categories = \App\Models\Category::all(); 
                            @endphp
                            @foreach ($categories as $cat)
                                <label class="flex items-center space-x-2">
                                    <input type="radio" name="kategori" value="{{ $cat->id }}"
                                        {{ ($filters['kategori'] ?? null) == $cat->id ? 'checked' : '' }}
                                        class="form-radio text-blue-600 rounded">
                                    <span>{{ $cat->name }}</span>
                                </label>
                            @endforeach

                        </div>
                    </div>
                    <div class="pt-4 border-t border-gray-200">
                        <h3 class="font-semibold text-gray-700 mb-3">Lokasi</h3>
                        <div class="space-y-2 text-sm text-gray-600">
                            @php
                                $locations = \App\Models\User::select('kabupaten')
                                    ->whereNotNull('kabupaten')
                                    ->distinct()
                                    ->pluck('kabupaten');
                            @endphp
                            @foreach ($locations as $loc)
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="lokasi[]" value="{{ $loc }}"
                                        {{ in_array($loc, $filters['lokasi'] ?? []) ? 'checked' : '' }}
                                        class="form-checkbox text-blue-600 rounded">
                                    <span>{{ $loc }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="pt-4 border-t border-gray-200">
                        <h3 class="font-semibold text-gray-700 mb-3">Rentang Harga</h3>
                        <div class="flex space-x-2 text-xs">
                            <input type="number" name="harga_min" placeholder="Harga Minimum" 
                                value="{{ $filters['harga_min'] ?? 0 }}"
                                class="w-1/2 border border-gray-300 rounded-lg p-2 text-gray-700 text-center focus:ring-blue-600 focus:border-blue-600">
                            <input type="number" name="harga_max" placeholder="Harga Maksimum" 
                                value="{{ $filters['harga_max'] ?? 50000000 }}"
                                class="w-1/2 border border-gray-300 rounded-lg p-2 text-gray-700 text-center focus:ring-blue-600 focus:border-blue-600">
                        </div>
                    </div>
                    <div class="pt-4 border-t border-gray-200">
                        <h3 class="font-semibold text-gray-700 mb-3">Rating</h3>
                        <label class="flex items-center space-x-2 text-sm text-gray-600">
                            <input type="checkbox" name="rating" value="4" 
                                {{ ($filters['rating'] ?? '') == 4 ? 'checked' : '' }}
                                class="form-checkbox text-blue-600 rounded">
                            <span class="text-yellow-500"> ⭐ </span>
                            <span>4 ke atas</span>
                        </label>
                    </div>
                    <div class="pt-6 space-y-3">
                        <button type="submit"
                            class="w-full bg-blue-600 text-white font-semibold py-3 rounded-lg hover:bg-blue-700 transition duration-150 shadow-md">
                                Terapkan Filter
                        </button>
                        <a href="{{ route('katalog') }}"
                            class="block w-full text-center bg-gray-200 text-gray-700 font-semibold py-3 rounded-lg hover:bg-gray-300 transition duration-150">
                            Reset Filter
                        </a>
                    </div>
                </form>
            </div>
            <div class="flex-grow">
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">{{ $currentCategory ?? 'Semua Produk' }}</h1>
                    <p class="text-gray-500 text-sm">Menampilkan {{ number_format($totalProducts, 0, ',', '.') }} produk</p>
                </div>
                {{-- Daftar Produk --}}
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
                    @foreach ($products as $product)
                        <a href="{{ route('product.detail', ['id' => $product->id]) }}" class="bg-white rounded-lg shadow hover:shadow-lg transition border border-gray-100 overflow-hidden product-card">
                            {{-- Gambar Produk --}}
                            @if ($product->image_path)
                                <img src="{{ $product->image_path }}" class="w-full h-40 object-cover" alt="{{ $product->name }}">
                            @else
                                <div class="w-full h-40 bg-gray-200 flex items-center justify-center text-gray-500">
                                    Tidak ada gambar
                                </div>
                            @endif
                            {{-- Konten --}}
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
                                {{-- Rating --}}
                                <div class="flex items-center text-xs">
                                    <span class="font-semibold text-yellow-500 mr-1"> ⭐  {{ number_format($product->rating, 1) }}</span>
                                    <span class="text-gray-500">
                                        ({{ number_format($product->total_ulasan, 0, ',', '.') }})
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="mt-6">
                    {{ $products->links() }}
                </div>
                <div class="mt-8 flex justify-center lg:justify-end">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </main>
</body>

@include('layouts.footer')

<!-- SCRIPT LIVE SEARCH (UMUM, GA GANGGU YG LAIN) -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    const input    = document.getElementById("header-search-input");
    const dropdown = document.getElementById("live-search-results");
    const form     = document.getElementById("header-search-form");

    if (!input || !dropdown || !form) return;

    function clearDropdown() {
        dropdown.innerHTML = "";
        dropdown.classList.add("hidden");
    }

    // LIVE UPDATE SAAT NGETIK
    input.addEventListener("keyup", () => {
        const q = input.value.trim();

        if (q.length === 0) {
            clearDropdown();
            return;
        }

        fetch(`/search?ajax=1&q=${encodeURIComponent(q)}`)
            .then(res => res.json())
            .then(data => {
                dropdown.innerHTML = "";
                dropdown.classList.remove("hidden");

                if (!data || data.length === 0) {
                    dropdown.innerHTML =
                        '<div class="p-3 text-gray-500 text-sm">Tidak ada hasil</div>';
                    return;
                }

                data.forEach(item => {
                    const a = document.createElement("a");
                    a.href = `/product/${item.id}/detail`;
                    a.className = "block px-4 py-2 hover:bg-gray-100 text-sm text-gray-700";
                    a.textContent = item.name;
                    dropdown.appendChild(a);
                });
            })
            .catch(() => {
                clearDropdown();
            });
    });

    // ENTER -> submit ke halaman /search, tapi kalau kosong balik ke beranda
    form.addEventListener("submit", (e) => {
        const q = input.value.trim();

        if (q === "") {
            e.preventDefault();
            clearDropdown();
            window.location.href = "/";
        }
    });

    // klik di luar -> nutup dropdown
    document.addEventListener("click", (e) => {
        if (!form.contains(e.target)) {
            clearDropdown();
        }
    });
});
</script>
</html>