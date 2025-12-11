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

        {{-- LIST PRODUK --}}
        @if ($products->count() === 0)
            <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
                Tidak ada produk yang cocok dengan pencarian kamu.
            </div>
        @else
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