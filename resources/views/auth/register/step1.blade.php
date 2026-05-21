<!DOCTYPE html>
<html class="light" lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Daftar sebagai Penjual | QuadMarket</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#0f3343",
                        "background-light": "#f6f7f8",
                        "background-dark": "#131b1f",
                        "accent": "#00d1c1",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "0.5rem",
                        lg: "1rem",
                        xl: "1.5rem",
                        full: "9999px",
                    },
                },
            },
        }
    </script>

    <style>
        body {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
    </style>
</head>

<body class="font-display bg-background-light dark:bg-background-dark text-gray-800 dark:text-gray-200">
    <div class="relative flex min-h-screen w-full flex-col group/design-root">
        <div class="flex-grow w-full max-w-7xl mx-auto lg:p-8">

            <div
                class="flex flex-col lg:flex-row bg-white dark:bg-background-dark lg:rounded-xl shadow-sm overflow-hidden min-h-[90vh]">
                <div
                    class="relative lg:w-1/2 bg-primary text-white p-8 lg:p-12 flex flex-col justify-center order-2 lg:order-1">

                    <!-- Logo & Heading -->
                    <div class="absolute top-8 left-8 flex items-center gap-3">
                        <div class="size-6 text-white">
                            <svg fill="currentColor" viewBox="0 0 48 48"
                                xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_6_535)">
                                    <path clip-rule="evenodd"
                                        d="M47.2426 24L24 47.2426L0.757355 24L24 0.757355L47.2426 24ZM12.2426 21H35.7574L24 9.24264L12.2426 21Z"
                                        fill-rule="evenodd"></path>
                                </g>
                                <defs>
                                    <clipPath id="clip0_6_535">
                                        <rect width="48" height="48" fill="white"></rect>
                                    </clipPath>
                                </defs>
                            </svg>
                        </div>

                        <h2 class="text-xl font-bold tracking-tight">
                            Katalog Produk Market Place
                        </h2>
                    </div>

                    <!-- Text -->
                    <div class="z-10 mt-24 lg:mt-0">
                        <h1 class="text-4xl lg:text-5xl font-black tracking-tighter leading-tight mb-6">
                            Mulai Perjalanan Bisnis Anda Bersama Kami.
                        </h1>
                        <p class="text-lg text-white/80">
                            Bergabunglah dengan ribuan penjual sukses lainnya dan jangkau lebih banyak pelanggan di
                            platform kami.
                        </p>
                    </div>

                    <!-- Overlay -->
                    <div class="absolute inset-0 bg-primary opacity-80 mix-blend-multiply"></div>

                    <!-- Background Image -->
                    <img class="absolute inset-0 w-full h-full object-cover"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuDoWTaxBvBxTaFXBKsWkw4GeDKQ1-LHZQTQ0-SPzj-TLVDzdezUPhBQIKF_nTpbkjZbhi4jFY7quMFiL3S7lgxsho7lLIbwMO_HkX7rIIhSI8tCrOOUfdcthgUzfdNDO1_kDg6TWyYIzNfJO6PQA9rPtcohh9MT8eZ7P19VpxUCMSh4svvM34vY9-V_-9XwjWXrSfmm5CN8aV269-GIKwu5viPtqywotGYYDG6ext9N_8AsvgYiF3WoOspQ2Z8F7_sZ9rA5rTJB7A"
                        alt="Discussion in modern office" />
                </div>


                <div
                    class="lg:w-1/2 p-6 sm:p-8 lg:p-12 flex flex-col justify-center order-1 lg:order-2 bg-background-light dark:bg-background-dark">

                    <div class="w-full max-w-md mx-auto">

                        <!-- Heading -->
                        <div class="mb-8">
                            <p class="text-4xl font-black tracking-tighter text-gray-900 dark:text-white">
                                Daftar sebagai Penjual
                            </p>
                            <p class="text-gray-500 dark:text-gray-400 mt-2">
                                Langkah 1: Informasi Toko & Kontak
                            </p>
                        </div>

                        <!-- Progress -->
                        <div class="mb-8">
                            <div class="flex justify-between items-center mb-2">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Langkah 1 dari 3
                                </p>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    33%
                                </p>
                            </div>
                            <div class="rounded-full bg-gray-200 dark:bg-gray-700 h-2">
                                <div class="h-2 rounded-full bg-accent" style="width: 33%;"></div>
                            </div>
                        </div>

                        <!-- FORM -->
                        <form action="{{ route('register.step1.post') }}" class="space-y-8" method="POST">
                            @csrf 
                            <div>
                                <label class="flex flex-col">
                                    <p class="text-sm font-medium pb-1">
                                        Nama Toko
                                    </p>
                                    <input type="text" name="nama_toko"
                                        class="w-full rounded-lg border @error('nama_toko') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 h-12 px-4 shadow-sm placeholder:text-gray-400 dark:placeholder:text-gray-500 text-gray-900 dark:text-white focus:ring-2 focus:ring-accent focus:border-accent"
                                        placeholder="Masukkan nama toko Anda"
                                        value="{{ old('nama_toko', $registrationData['nama_toko'] ?? '') }}" />
                                </label>
                                @error('nama_toko')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="flex flex-col">
                                    <p class="text-sm font-medium pb-1">
                                        Deskripsi Singkat
                                    </p>
                                    <textarea name="deskripsi"
                                        class="w-full rounded-lg border @error('deskripsi') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 min-h-28 p-4 shadow-sm placeholder:text-gray-400 dark:placeholder:text-gray-500 text-gray-900 dark:text-white focus:ring-2 focus:ring-accent focus:border-accent"
                                        placeholder="Jelaskan sedikit tentang toko Anda">{{ old('deskripsi', $registrationData['deskripsi'] ?? '') }}</textarea>
                                </label>
                                @error('deskripsi')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="flex flex-col">
                                    <p class="text-sm font-medium pb-1">
                                        Nama PIC
                                    </p>
                                    <input type="text" name="nama_pic"
                                        class="w-full rounded-lg border @error('nama_pic') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 h-12 px-4 shadow-sm placeholder:text-gray-400 dark:placeholder:text-gray-500 text-gray-900 dark:text-white focus:ring-2 focus:ring-accent focus:border-accent"
                                        placeholder="Nama lengkap contact person"
                                        value="{{ old('nama_pic', $registrationData['nama_pic'] ?? '') }}" />
                                </label>
                                @error('nama_pic')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid sm:grid-cols-2 gap-6">
                                <div>
                                    <label class="flex flex-col">
                                        <p class="text-sm font-medium pb-1">
                                            No. HP PIC
                                        </p>
                                        <input type="tel" name="hp_pic"
                                            class="w-full rounded-lg border @error('hp_pic') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 h-12 px-4 shadow-sm placeholder:text-gray-400 dark:placeholder:text-gray-500 text-gray-900 dark:text-white focus:ring-2 focus:ring-accent focus:border-accent"
                                            placeholder="0812xxxxxxxx"
                                            value="{{ old('hp_pic', $registrationData['hp_pic'] ?? '') }}" />
                                    </label>
                                    @error('hp_pic')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="flex flex-col">
                                        <p class="text-sm font-medium pb-1">
                                            Email PIC
                                        </p>
                                        <input type="email" name="email_pic"
                                            class="w-full rounded-lg border @error('email_pic') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 h-12 px-4 shadow-sm placeholder:text-gray-400 dark:placeholder:text-gray-500 text-gray-900 dark:text-white focus:ring-2 focus:ring-accent focus:border-accent"
                                            placeholder="anda@email.com"
                                            value="{{ old('email_pic', $registrationData['email_pic'] ?? '') }}" />
                                    </label>
                                    @error('email_pic')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <button type="submit"
                                    class="w-full bg-primary text-white font-bold py-3 px-4 rounded-lg hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary dark:focus:ring-offset-background-dark transition-colors duration-300 h-14">
                                    Lanjutkan
                                </button>
                            </div>
                        </form>

                        <p class="mt-8 text-center text-sm text-gray-500 dark:text-gray-400">
                            Sudah punya akun?
                            <a href="{{ route('login.pilih') }}"
                                class="font-medium text-primary dark:text-accent hover:opacity-80">
                                Masuk
                            </a>
                        </p>

                    </div>
                </div>

            </div>

        </div>
    </div>
</body>

</html>
