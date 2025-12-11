<!DOCTYPE html>

<html class="light" lang="id"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Daftar sebagai Penjual | QuadMarket</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              "primary": "#0f3343",
              "accent": "#00D1C1",
              "quad-light-blue": "#4c98e1",
              "background-light": "#f6f7f8",
              "background-dark": "#131b1f",
              "text-primary": "#0F1E42",
              "text-secondary": "#567e8f"
            },
            fontFamily: {
              "display": ["Inter", "sans-serif"]
            },
            borderRadius: {"DEFAULT": "0.5rem", "lg": "1rem", "xl": "1.5rem", "full": "9999px"},
          },
        },
      }
    </script>
<style>
      .material-symbols-outlined {
        font-variation-settings:
        'FILL' 0,
        'wght' 400,
        'GRAD' 0,
        'opsz' 24
      }
    </style>
</head>
<body class="font-display bg-background-light dark:bg-background-dark text-text-primary dark:text-gray-200">
<div class="relative flex min-h-screen w-full flex-col group/design-root overflow-x-hidden">
<div class="layout-container flex h-full grow flex-col">
<!-- <header class="flex justify-center py-0 px-4"> -->
<!-- <a class="flex items-center gap-3" href="#"> -->
<!-- <span class="material-symbols-outlined text-primary text-4xl">store</span> -->
<!-- <span class="text-2xl font-bold tracking-tight text-primary">Katalog Produk Market Place</span> -->
</a>
</header>
<main class="flex flex-1 justify-center py-5 px-4">
    <div class="layout-content-container flex flex-col w-full max-w-3xl flex-1">

        <div class="mb-8 px-4">
            <p class="text-primary/80 dark:text-white/70 text-sm font-medium">Langkah 3 dari 3</p>
            <div class="mt-2 h-2 w-full rounded-full bg-gray-200 dark:bg-gray-700">
                <div class="h-2 rounded-full bg-accent" style="width: 100%;"></div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200/50 bg-white dark:bg-background-dark dark:border-white/10 p-6 sm:p-10 shadow-lg shadow-gray-200/40 dark:shadow-none">

            <div class="mb-8">
                <h1 class="text-3xl font-black tracking-tighter text-primary dark:text-white sm:text-4xl">
                    Registrasi Penjual - Verifikasi Identitas
                </h1>
                <p class="text-text-secondary dark:text-gray-400 text-base mt-2">
                    Lengkapi data diri Anda untuk menyelesaikan pendaftaran.
                </p>
            </div>

            <!-- Form -->
            <form action="{{ route('register.step3.post') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-6">
                @csrf

                <div>
                    <label class="flex flex-col">
                        <p class="text-text-primary dark:text-white text-base font-medium leading-normal pb-2">
                            Nomor KTP Penanggung Jawab (PIC)
                        </p>
                        <input name="nik"
                            class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg 
                                   text-text-primary dark:text-gray-200 dark:bg-background-dark 
                                   focus:outline-0 focus:ring-2 focus:ring-accent/50 
                                   border border-[#d2dfe4] dark:border-gray-700 bg-white h-14 
                                   placeholder:text-text-secondary p-[15px] text-base shadow-sm"
                            placeholder="Masukkan 16 digit nomor KTP Anda"
                            value="{{ old('nik') }}" />
                        @error('nik')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <label class="flex flex-col">
                        <p class="text-text-primary dark:text-white text-base font-medium leading-normal pb-2">Kata Sandi</p>
                        <input name="password"
                            type="password"
                            class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg 
                                   text-text-primary dark:text-gray-200 dark:bg-background-dark 
                                   focus:outline-0 focus:ring-2 focus:ring-accent/50 
                                   border border-[#d2dfe4] dark:border-gray-700 bg-white h-14 
                                   placeholder:text-text-secondary p-[15px] text-base shadow-sm"
                            placeholder="Minimal 8 karakter" />
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </label>

                    <label class="flex flex-col">
                        <p class="text-text-primary dark:text-white text-base font-medium leading-normal pb-2">Konfirmasi Kata Sandi</p>
                        <input name="password_confirmation"
                            type="password"
                            class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg 
                                   text-text-primary dark:text-gray-200 dark:bg-background-dark 
                                   focus:outline-0 focus:ring-2 focus:ring-accent/50 
                                   border border-[#d2dfe4] dark:border-gray-700 bg-white h-14 
                                   placeholder:text-text-secondary p-[15px] text-base shadow-sm"
                            placeholder="Ulangi kata sandi" />
                    </label>
                </div>

                <!-- Upload Section -->
                <div>
                    <h3 class="text-text-primary dark:text-white text-lg font-bold leading-tight pb-2">Unggah Dokumen</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- Foto PIC -->
                        <div class="flex flex-col gap-2">
                            <p class="text-text-primary dark:text-white text-base font-medium">Upload Foto PIC</p>
                            <div class="flex items-center justify-center w-full">
                                <label id="dropzone-label-pic"
                                    class="flex flex-col items-center justify-center w-full h-48 
                                           border-2 border-[#d2dfe4] dark:border-gray-600 border-dashed
                                           rounded-lg cursor-pointer bg-white dark:bg-background-dark 
                                           hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                                    for="dropzone-file-pic">

                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <span id="file-icon-pic"
                                            class="material-symbols-outlined text-text-secondary text-4xl mb-3">cloud_upload</span>
                                        <p id="file-text-pic" class="mb-2 text-sm text-text-secondary dark:text-gray-400">
                                            <span class="font-semibold">Klik untuk mengunggah</span>
                                        </p>
                                        <p class="text-xs text-text-secondary dark:text-gray-400">atau seret dan lepas</p>
                                    </div>

                                    <input id="dropzone-file-pic" name="foto_pic" type="file" class="hidden" accept="image/*" />
                                </label>
                            </div>
                            @error('foto_pic')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- KTP PIC -->
                        <div class="flex flex-col gap-2">
                            <p class="text-text-primary dark:text-white text-base font-medium">Upload File KTP PIC</p>
                            <div class="flex items-center justify-center w-full">
                                <label id="dropzone-label-ktp"
                                    class="flex flex-col items-center justify-center w-full h-48 
                                           border-2 border-[#d2dfe4] dark:border-gray-600 border-dashed
                                           rounded-lg cursor-pointer bg-white dark:bg-background-dark 
                                           hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                                    for="dropzone-file-ktp">

                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <span id="file-icon-ktp"
                                            class="material-symbols-outlined text-text-secondary text-4xl mb-3">badge</span>
                                        <p id="file-text-ktp" class="mb-2 text-sm text-text-secondary dark:text-gray-400">
                                            <span class="font-semibold">Klik untuk mengunggah</span>
                                        </p>
                                        <p class="text-xs text-text-secondary dark:text-gray-400">atau seret dan lepas</p>
                                    </div>

                                    <input id="dropzone-file-ktp" name="foto_ktp" type="file" class="hidden" accept="image/*,.pdf" />
                                </label>
                            </div>
                            @error('foto_ktp')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex flex-col gap-4">
                    <p class="text-center text-xs text-text-secondary dark:text-gray-400">
                        Dengan menekan tombol Kirim, Anda menyetujui
                        <a class="font-semibold text-quad-light-blue hover:underline" href="#">Syarat & Ketentuan</a>
                        dan 
                        <a class="font-semibold text-quad-light-blue hover:underline" href="#">Kebijakan Privasi</a>.
                    </p>

                    <button class="flex items-center justify-center w-full sm:w-auto rounded-lg bg-primary px-8 py-4 
                                   text-base font-bold text-white shadow-sm shadow-accent/20 hover:bg-primary/90">
                        Kirim
                    </button>

                    <a href="{{ route('register.step2') }}"
                        class="text-sm font-medium text-center text-text-secondary dark:text-gray-400 
                               hover:text-primary dark:hover:text-white transition-colors">
                        &larr; Kembali ke Langkah Sebelumnya
                    </a>
                </div>

            </form>
        </div>

    </div>
</main>

</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

    const handleFileUploadChange = (inputId, textId, iconId, labelId) => {
        const input = document.getElementById(inputId);
        const textElement = document.getElementById(textId);
        const iconElement = document.getElementById(iconId);
        const labelElement = document.getElementById(labelId);

        input.addEventListener('change', (event) => {
            const file = event.target.files[0];
            
            if (file) {
                textElement.innerHTML = `<span class="font-semibold">${file.name}</span>`;
                
                iconElement.innerText = "check_circle";
                iconElement.classList.remove('text-text-secondary');
                iconElement.classList.add('text-accent');

                labelElement.classList.remove('border-dashed');
                labelElement.classList.add('border-solid', 'border-accent');

            } else {
                textElement.innerHTML = '<span class="font-semibold">Klik untuk mengunggah</span>';

                iconElement.classList.remove('text-accent');
                iconElement.classList.add('text-text-secondary');

                // fallback icon
                if (inputId === "dropzone-file-pic") {
                    iconElement.innerText = "cloud_upload";
                } else {
                    iconElement.innerText = "badge";
                }

                labelElement.classList.add('border-dashed');
                labelElement.classList.remove('border-solid', 'border-accent');
            }
        });
    };

    handleFileUploadChange("dropzone-file-pic", "file-text-pic", "file-icon-pic", "dropzone-label-pic");
    handleFileUploadChange("dropzone-file-ktp", "file-text-ktp", "file-icon-ktp", "dropzone-label-ktp");
});
</script>

<!-- Tambahkan style untuk ikon check_circle_fill (karena tidak ada di Material Symbols, kita gunakan check_circle) -->
<style>
    .check_circle_fill::before {
        content: "\e86c"; /* Unicode untuk 'check_circle' di Material Icons */
        font-family: 'Material Symbols Outlined';
    }
    /* Mengubah warna tombol Kirim menjadi warna 'primary' */
    .bg-blue-600 {
        background-color: var(--primary); 
    }
    .hover\:bg-blue-600\/90:hover {
        background-color: #0d2b38; /* Slightly darker primary */
    }
    .focus\:ring-4 {
        box-shadow: 0 0 0 4px rgba(0, 209, 193, 0.3); /* Ring accent */
    }
</style>
</body>
</html>
