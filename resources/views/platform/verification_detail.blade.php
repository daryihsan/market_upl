<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Detail Verifikasi Penjual</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        body { background:#F9FAFB; font-family:Inter, sans-serif; }
        .sidebar { width:250px; background:#fff; padding:20px; box-shadow:0 0 8px rgba(13,27,60,.04); position:fixed; height:100%; }
        .main { margin-left:250px; padding:30px; }
        .nav-link { padding:12px 16px; border-radius:8px; display:flex; align-items:center; color:#6b7280; transition:.15s; }
        .nav-link:hover { background:#eef2ff; color:#1e3a8a; }
        .nav-link.active { background:#eef2ff; color:#1e3a8a; font-weight:600; }
        .card { background:white; border-radius:12px; padding:22px; margin-bottom:24px;
                box-shadow:0 6px 18px rgba(17,24,39,0.04); }
        .label { font-size:0.85rem; color:#6b7280; }
        .value { font-weight:500; color:#111827; }
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
            <a href="{{ route('platform.dashboard') }}" class="nav-link">
                <i class="fas fa-chart-line mr-3"></i> Dashboard
            </a>

            <a href="{{ route('platform.verifikasi.list') }}" class="nav-link active">
                <i class="fas fa-check-circle mr-3"></i> Verifikasi Penjual
            </a>

            <a href="{{ route('platform.laporan') }}" class="nav-link">
                <i class="fas fa-file-alt mr-3"></i> Laporan
            </a>

            <a href="{{ route('platform.categories.index') }}" class="nav-link">
                <i class="fas fa-tags mr-3"></i> Manajemen Kategori
            </a>
        </nav>

        <div class="absolute bottom-0 w-full pr-4 border-t p-4 bg-white">
            <a href="#" class="nav-link"><i class="fas fa-cog mr-3"></i> Pengaturan</a>
            <a href="#" class="nav-link"><i class="fas fa-question-circle mr-3"></i> Bantuan</a>
        </div>
    </aside>

    {{-- MAIN --}}
    <main class="main">

        {{-- HEADER --}}
        <div class="flex items-start justify-between mb-8">
            <div>
                <a href="{{ route('platform.verifikasi.list') }}" class="text-sm text-gray-600 hover:text-gray-800">
                    ← Kembali ke Daftar
                </a>
                <h1 class="text-2xl font-bold text-blue-900 mt-2">Detail Verifikasi Penjual</h1>
                <p class="text-gray-500 text-sm mt-1">Data lengkap pendaftar yang harus diverifikasi.</p>
            </div>

            <img src="{{ url('assets/images/logo.png') }}" class="h-20">
        </div>

        {{-- ========================= --}}
        {{-- 2 KOLOM: RINGKASAN + ALAMAT --}}
        {{-- ========================= --}}
        <div class="card">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

                {{-- RINGKASAN --}}
                <div class="space-y-4">
                    <h2 class="font-semibold text-lg text-slate-900 mb-1">Ringkasan Toko</h2>

                    <div>
                        <p class="label">Nama Toko</p>
                        <p class="value">{{ $seller->nama_toko }}</p>
                    </div>

                    <div>
                        <p class="label">Nama PIC</p>
                        <p class="value">{{ $seller->nama_pic }}</p>
                    </div>

                    <div>
                        <p class="label">Email PIC</p>
                        <p class="value">{{ $seller->email_pic }}</p>
                    </div>

                    <div>
                        <p class="label">No HP</p>
                        <p class="value">{{ $seller->no_hp }}</p>
                    </div>
                </div>

                {{-- ALAMAT --}}
                <div class="space-y-4">
                    <h2 class="font-semibold text-lg text-slate-900 mb-1">Alamat & Identitas</h2>

                    <div>
                        <p class="label">Alamat Lengkap</p>
                        <p class="value">
                            {{ $seller->alamat_pic }}, RT {{ $seller->rt }} / RW {{ $seller->rw }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">

                        <div>
                            <p class="label">Kelurahan</p>
                            <p class="value">{{ $seller->kelurahan }}</p>
                        </div>

                        <div>
                            <p class="label">Kecamatan</p>
                            <p class="value">{{ $seller->kecamatan }}</p>
                        </div>

                        <div>
                            <p class="label">Kabupaten</p>
                            <p class="value">{{ $seller->kabupaten }}</p>
                        </div>

                        <div>
                            <p class="label">Provinsi</p>
                            <p class="value">{{ $seller->provinsi }}</p>
                        </div>

                        <div class="col-span-2">
                            <p class="label">NIK</p>
                            <p class="value">{{ $seller->nik }}</p>
                        </div>

                    </div>
                </div>

            </div>
        </div>


        {{-- ========================= --}}
        {{-- DOKUMEN --}}
        {{-- ========================= --}}
        <div class="card">
            <h2 class="font-semibold text-lg text-slate-900 mb-4">Dokumen Penjual</h2>

            <div class="space-y-5">

                {{-- FOTO PIC --}}
                <div class="flex justify-between items-center">
                    <div>
                        <p class="label">Foto PIC</p>
                        <p class="value truncate max-w-[240px]">
                            {{ basename($seller->foto_pic ?? '') }}
                        </p>
                    </div>

                    @if($seller->foto_pic)
                        <button onclick="showImageModal('{{ route('local.file', ['path' => $seller->foto_pic]) }}', 'Foto PIC')"
                                class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                            Lihat
                        </button>

                    @endif
                </div>

                {{-- FOTO KTP --}}
                <div class="flex justify-between items-center">
                    <div>
                        <p class="label">Foto / File KTP</p>
                        <p class="value truncate max-w-[240px]">
                            {{ basename($seller->file_ktp ?? '') }}
                        </p>
                    </div>

                    @if($seller->file_ktp)
                        <button onclick="showImageModal('{{ route('local.file', ['path' => $seller->file_ktp]) }}', 'Foto KTP')"
                                class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                            Lihat
                        </button>

                    @endif
                </div>

            </div><div class="card">
    <h2 class="font-semibold text-lg text-slate-900 mb-4">Dokumen Penjual</h2>

    <div class="space-y-5">

        @php
            // Hardcode fallback local paths
            $picPath = $seller->foto_pic ?: 'seller_docs/foto_pic/M3VkH6nIr5ppTb05EfVh98uJS97WbeqOf82OK5um.png';
            $ktpPath = $seller->file_ktp ?: 'seller_docs/foto_ktp/J9DIVrtfucksX31ZYUVkHsA9P1AcmSca7Sb3sSbg.png';
        @endphp

        {{-- FOTO PIC --}}
        <div class="flex justify-between items-center">
            <div>
                <p class="label">Foto PIC</p>
                <p class="value truncate max-w-[240px]">
                    {{ basename($picPath) }}
                </p>
            </div>

            <button onclick="showImageModal('{{ route('local.file', ['path' => $picPath]) }}', 'Foto PIC')"
                    class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                Lihat
            </button>
        </div>

        {{-- FOTO KTP --}}
        <div class="flex justify-between items-center">
            <div>
                <p class="label">Foto / File KTP</p>
                <p class="value truncate max-w-[240px]">
                    {{ basename($ktpPath) }}
                </p>
            </div>

            <button onclick="showImageModal('{{ route('local.file', ['path' => $ktpPath]) }}', 'Foto KTP')"
                    class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                Lihat
            </button>
        </div>

    </div>
</div>

        </div>

        {{-- ========================= --}}
        {{-- TOMBOL AKSI --}}
        {{-- ========================= --}}
        <div class="card">
            <form action="{{ route('platform.verifikasi.process', $seller->id) }}" method="POST">
                @csrf

                <div class="flex gap-4">
                    <button name="action" value="approve"
                            class="flex-1 bg-green-600 text-white py-2 rounded-md hover:bg-green-700 transition">
                        Setujui
                    </button>

                    <button type="button" id="rejectBtn"
                            class="flex-1 bg-red-600 text-white py-2 rounded-md hover:bg-red-700 transition">
                        Tolak
                    </button>
                </div>

                {{-- BOX REJECT --}}
                <div id="rejectBox" class="hidden mt-4">
                    <textarea name="alasan" rows="3"
                              class="w-full border rounded-md p-2 text-sm focus:ring focus:ring-indigo-200"
                              placeholder="Alasan penolakan (opsional)..."></textarea>

                    <div class="flex gap-3 mt-3">
                        <button name="action" value="reject"
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                            Kirim Penolakan
                        </button>

                        <button type="button" id="cancelReject"
                                class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300 transition">
                            Batal
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </main>
</div>


{{-- ========================= --}}
{{-- MODAL GAMBAR (TAILWIND SMOOTH) --}}
{{-- ========================= --}}
<div id="imageModal" class="hidden fixed inset-0 bg-black/60 z-50 items-center justify-center transition">
    <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full mx-4 overflow-hidden animate-fadeIn">
        <div class="flex justify-between items-center p-3 border-b">
            <h4 id="imageTitle" class="font-semibold text-sm"></h4>
            <button onclick="closeImageModal()" class="text-gray-500 text-lg hover:text-gray-800">&times;</button>
        </div>
        <div class="p-4 flex justify-center bg-gray-50">
            <img id="imagePreview" src="" class="max-h-[72vh] object-contain rounded-lg shadow">
        </div>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from { opacity:0; transform:scale(.95); }
        to { opacity:1; transform:scale(1); }
    }
    .animate-fadeIn { animation: fadeIn .25s ease-out; }
</style>

<script>
    // Reject logic
    document.getElementById('rejectBtn').onclick = () =>
        document.getElementById('rejectBox').classList.remove('hidden');

    document.getElementById('cancelReject').onclick = () =>
        document.getElementById('rejectBox').classList.add('hidden');

    // Image modal
    function openImageModal(src, title) {
        document.getElementById('imagePreview').src = src;
        document.getElementById('imageTitle').innerText = title;
        const m = document.getElementById('imageModal');
        m.classList.remove('hidden');
        m.classList.add('flex');
    }

    function closeImageModal() {
        const m = document.getElementById('imageModal');
        m.classList.add('hidden');
        m.classList.remove('flex');
        document.getElementById('imagePreview').src = '';
    }
</script>

</body>
</html>
