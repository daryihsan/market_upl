@php
use Illuminate\Support\Str;
// Tidak ada variabel spesifik yang dibutuhkan dari controller untuk halaman statis ini,
// tapi kita bisa tetap menggunakan struktur Blade dan Tailwind yang konsisten.
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang QuadMarket</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        html, body {
            font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", "Liberation Sans", sans-serif;
        }
        /* Style untuk konsistensi */
        .category-card, .product-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            cursor: pointer;
        }
    </style>
</head>
<body class="bg-gray-50">
    
    {{-- Memanggil Header (Asumsi 'layouts.header' ada dan berisi navigasi) --}}
    @include('layouts.header')

    <div class="bg-gray-50 font-inter">

        {{-- HERO SECTION (disamakan gaya dengan hero beranda, tanpa gambar) --}}
        <section class="pt-24 pb-28 bg-white border-b text-center flex flex-col items-center justify-center">
            <div class="max-w-7xl mx-auto px-4">
                <h1 class="text-4xl sm:text-5xl font-bold text-gray-800 mb-4">
                    Tentang QuadMarket
                </h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    QuadMarket hadir sebagai platform belanja online yang menyediakan jutaan produk terbaik 
                    dengan pengalaman belanja yang mudah, cepat, dan aman.
                </p>
            </div>
        </section>

        {{-- VISI MISI --}}
        <section class="py-20">
            <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-2 gap-12">

                <div>
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Visi Kami</h2>
                    <p class="text-gray-600 leading-relaxed text-base">
                        Menjadi marketplace terpercaya yang memudahkan semua orang menemukan berbagai
                        kebutuhan dengan harga terbaik, layanan terbaik, dan proses transaksi yang nyaman.
                    </p>
                </div>

                <div>
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Misi Kami</h2>
                    <ul class="text-gray-600 space-y-3 leading-relaxed text-base list-disc list-inside ml-4">
                        <li>Memberikan akses ke berbagai kategori produk berkualitas.</li>
                        <li>Menyediakan platform aman bagi penjual dan pembeli.</li>
                        <li>Menghadirkan proses pencarian dan pembayaran yang cepat.</li>
                        <li>Membantu UMKM berkembang melalui penjualan online.</li>
                    </ul>
                </div>

            </div>
        </section>

        {{-- KEUNGGULAN --}}
        <section class="py-20 bg-white border-y">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <h2 class="text-3xl font-semibold text-gray-800 mb-10">Mengapa Memilih QuadMarket?</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    
                    <div class="p-6 bg-gray-50 rounded-xl shadow-sm hover:shadow-md transition">
                        <i class="fas fa-boxes text-blue-600 text-3xl mb-3"></i>
                        <h3 class="font-semibold text-xl mb-2">Produk Lengkap</h3>
                        <p class="text-gray-600">
                            Jelajahi berbagai kategori, mulai dari elektronik, fashion, kecantikan, hingga perabot rumah.
                        </p>
                    </div>

                    <div class="p-6 bg-gray-50 rounded-xl shadow-sm hover:shadow-md transition">
                        <i class="fas fa-shield-alt text-blue-600 text-3xl mb-3"></i>
                        <h3 class="font-semibold text-xl mb-2">Transaksi Aman</h3>
                        <p class="text-gray-600">
                            Sistem pembayaran terjamin dengan perlindungan transaksi yang memastikan keamanan pengguna.
                        </p>
                    </div>

                    <div class="p-6 bg-gray-50 rounded-xl shadow-sm hover:shadow-md transition">
                        <i class="fas fa-smile text-blue-600 text-3xl mb-3"></i>
                        <h3 class="font-semibold text-xl mb-2">Pengalaman Nyaman</h3>
                        <p class="text-gray-600">
                            Antarmuka yang sederhana, proses pencarian cepat, dan pelayanan pelanggan yang responsif.
                        </p>
                    </div>

                </div>
            </div>
        </section>

        {{-- FINAL MESSAGE --}}
        <section class="py-20">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <h2 class="text-3xl font-semibold text-gray-800 mb-4">
                    Kami Hadir Untuk Anda
                </h2>
                <p class="text-gray-600 max-w-3xl mx-auto leading-relaxed text-base">
                    Di QuadMarket, kami percaya bahwa belanja online harus menjadi pengalaman yang menyenangkan. 
                    Karena itu, kami terus berinovasi dalam teknologi, kualitas layanan, dan kemudahan penggunaan agar 
                    setiap pelanggan merasa puas dan nyaman.
                </p>
            </div>
        </section>

    </div>

    {{-- Memanggil Footer --}}
    @include('layouts.footer')

    {{-- Tidak perlu script pencarian karena ini halaman statis About Us --}}

</body>
</html>