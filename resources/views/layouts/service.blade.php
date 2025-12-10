@php
use Illuminate\Support\Str;
// Tidak ada variabel spesifik dari controller yang dibutuhkan untuk halaman statis ini.
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan Pelanggan QuadMarket</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- ICON FONT AWESOME --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    
    <style>
        html, body {
            font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", "Liberation Sans", sans-serif;
        }
        .faq-item {
            border-bottom: 1px solid #e5e7eb;
        }
        .faq-question {
            cursor: pointer;
            user-select: none;
        }
        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-in-out, padding 0.3s ease-in-out;
        }
        .faq-answer.active {
            max-height: 300px; /* Nilai besar yang cukup menampung jawaban */
            padding-bottom: 1.5rem;
        }
    </style>
</head>
<body class="bg-gray-50">
    
    {{-- Memanggil Header --}}
    @include('layouts.header')

    <div class="bg-gray-50 font-inter">

        {{-- HERO SECTION --}}
        <section class="pt-24 pb-28 bg-white text-gray-800 border-b flex flex-col items-center justify-center">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <i class="fas fa-headset text-6xl mb-4"></i>
                <h1 class="text-4xl sm:text-5xl font-bold mb-3">
                    Layanan Pelanggan
                </h1>
                <p class="text-lg opacity-90 max-w-2xl mx-auto">
                    Kami siap membantu Anda. Temukan jawaban cepat di FAQ atau hubungi tim support kami.
                </p>
            </div>
        </section>

        {{-- KONTAK CEPAT & FAQ --}}
        <section class="py-16">
            <div class="max-w-7xl mx-auto px-4 grid lg:grid-cols-3 gap-10">

                {{-- SIDEBAR KONTAK CEPAT --}}
                <div class="lg:col-span-1">
                    <div class="bg-white p-6 rounded-xl shadow-lg sticky top-5">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3">Hubungi Kami</h2>

                        <div class="space-y-4">
                            
                            {{-- CHAT LANGSUNG --}}
                            <div class="flex items-start space-x-4 p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                                <i class="fas fa-comment-dots text-blue-600 text-2xl mt-1"></i>
                                <div>
                                    <h3 class="font-semibold text-gray-800">Live Chat</h3>
                                    <p class="text-sm text-gray-600">Respons tercepat (08:00 - 22:00 WIB)</p>
                                    <button class="text-blue-600 text-sm font-medium mt-1 hover:underline">08213354678</button>
                                </div>
                            </div>
                            
                            {{-- EMAIL --}}
                            <div class="flex items-start space-x-4 p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                                <i class="fas fa-envelope text-blue-600 text-2xl mt-1"></i>
                                <div>
                                    <h3 class="font-semibold text-gray-800">Email Support</h3>
                                    <p class="text-sm text-gray-600">Kirim pertanyaan mendetail Anda.</p>
                                    <a href="mailto:support@quadmarket.id" class="text-blue-600 text-sm font-medium mt-1 hover:underline">support@quadmarket.id</a>
                                </div>
                            </div>

                            <!-- {{-- TELEPON --}}
                            <div class="flex items-start space-x-4 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <i class="fas fa-phone-alt text-gray-600 text-2xl mt-1"></i>
                                <div>
                                    <h3 class="font-semibold text-gray-800">Call Center</h3>
                                    <p class="text-sm text-gray-600">Tersedia 24 Jam.</p>
                                    <a href="tel:+6221XXXXXXX" class="text-blue-600 text-sm font-medium mt-1 hover:underline">(021) XXX-XXXX</a>
                                </div>
                            </div> -->

                        </div>
                    </div>
                </div>

                {{-- FAQ LIST --}}
                <div class="lg:col-span-2">
                    <h2 class="text-3xl font-bold text-gray-800 mb-8">Pertanyaan yang Sering Diajukan (FAQ)</h2>

                    <div id="faq-list" class="bg-white rounded-xl shadow-lg overflow-hidden">
                        
                        {{-- ITEM 1: AKUN & TRANSAKSI --}}
                        <div class="faq-item">
                            <div class="faq-question flex justify-between items-center p-5 font-semibold text-gray-800 hover:bg-gray-50 transition" data-target="faq-answer-1">
                                Bagaimana cara mengganti kata sandi akun saya?
                                <i class="fas fa-chevron-down text-gray-400 text-sm transition-transform duration-300"></i>
                            </div>
                            <div id="faq-answer-1" class="faq-answer px-5 text-gray-600">
                                <p>Anda dapat mengganti kata sandi melalui menu Profil > Pengaturan Akun > Ganti Kata Sandi. Pastikan kata sandi baru Anda terdiri dari minimal 8 karakter yang mencakup huruf besar, huruf kecil, dan angka untuk keamanan maksimal.</p>
                            </div>
                        </div>

                        {{-- ITEM 2: PEMBAYARAN --}}
                        <div class="faq-item">
                            <div class="faq-question flex justify-between items-center p-5 font-semibold text-gray-800 hover:bg-gray-50 transition" data-target="faq-answer-2">
                                Metode pembayaran apa saja yang tersedia di QuadMarket?
                                <i class="fas fa-chevron-down text-gray-400 text-sm transition-transform duration-300"></i>
                            </div>
                            <div id="faq-answer-2" class="faq-answer px-5 text-gray-600">
                                <p>Kami menerima berbagai metode pembayaran, termasuk transfer bank (BCA, Mandiri, BRI, BNI), kartu kredit/debit, e-wallet (Gopay, OVO, Dana), dan pembayaran melalui gerai retail terdekat.</p>
                            </div>
                        </div>

                        {{-- ITEM 3: PENGIRIMAN --}}
                        <div class="faq-item">
                            <div class="faq-question flex justify-between items-center p-5 font-semibold text-gray-800 hover:bg-gray-50 transition" data-target="faq-answer-3">
                                Berapa lama estimasi waktu pengiriman barang?
                                <i class="fas fa-chevron-down text-gray-400 text-sm transition-transform duration-300"></i>
                            </div>
                            <div id="faq-answer-3" class="faq-answer px-5 text-gray-600">
                                <p>Estimasi pengiriman bergantung pada lokasi penjual dan alamat tujuan. Umumnya: 
                                <ul>
                                    <li>- Pulau Jawa: 2-4 hari kerja</li>
                                    <li>- Luar Pulau Jawa: 4-7 hari kerja</li>
                                </ul>
                                Anda dapat melacak status pesanan Anda secara real-time melalui halaman detail pesanan.</p>
                            </div>
                        </div>
                        
                        {{-- ITEM 4: PENGEMBALIAN --}}
                        <div class="faq-item">
                            <div class="faq-question flex justify-between items-center p-5 font-semibold text-gray-800 hover:bg-gray-50 transition" data-target="faq-answer-4">
                                Bagaimana prosedur pengembalian barang yang rusak atau tidak sesuai?
                                <i class="fas fa-chevron-down text-gray-400 text-sm transition-transform duration-300"></i>
                            </div>
                            <div id="faq-answer-4" class="faq-answer px-5 text-gray-600">
                                <p>Ajukan permohonan pengembalian (retur) melalui halaman detail pesanan maksimal 2 hari setelah barang diterima. Sertakan bukti foto/video unboxing. Tim kami akan memverifikasi dalam 1x24 jam. Mohon lihat Kebijakan Retur kami untuk detail lengkap.</p>
                            </div>
                        </div>

                        {{-- ITEM 5: PENJUAL --}}
                        <div class="faq-item">
                            <div class="faq-question flex justify-between items-center p-5 font-semibold text-gray-800 hover:bg-gray-50 transition" data-target="faq-answer-5">
                                Saya ingin menjadi Penjual, bagaimana cara mendaftar?
                                <i class="fas fa-chevron-down text-gray-400 text-sm transition-transform duration-300"></i>
                            </div>
                            <div id="faq-answer-5" class="faq-answer px-5 text-gray-600">
                                <p>Anda bisa mendaftar sebagai Penjual dengan mengklik tombol "Daftar sebagai Penjual" di halaman profil atau navigasi utama. Anda akan diminta mengisi data toko dan verifikasi identitas (KTP/NPWP).</p>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </section>

    </div>

    {{-- Memanggil Footer --}}
    @include('layouts.footer')

    {{-- SCRIPT FAQ COLLAPSE/EXPAND --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const faqQuestions = document.querySelectorAll('.faq-question');

            faqQuestions.forEach(question => {
                question.addEventListener('click', () => {
                    const targetId = question.getAttribute('data-target');
                    const answer = document.getElementById(targetId);
                    const icon = question.querySelector('i');

                    // Tutup semua jawaban lain
                    document.querySelectorAll('.faq-answer.active').forEach(openAnswer => {
                        if (openAnswer.id !== targetId) {
                            openAnswer.classList.remove('active');
                            openAnswer.previousElementSibling.querySelector('i').classList.remove('rotate-180');
                        }
                    });

                    // Toggle jawaban yang diklik
                    answer.classList.toggle('active');

                    // Toggle ikon panah
                    if (answer.classList.contains('active')) {
                        icon.classList.add('rotate-180');
                    } else {
                        icon.classList.remove('rotate-180');
                    }
                });
            });
        });
    </script>
</body>
</html>