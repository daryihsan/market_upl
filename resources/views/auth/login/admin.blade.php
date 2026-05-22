<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk ke Akun Admin | QuadMarket</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'quad-dark-blue': '#0a1d41',
                        'quad-light-blue': '#4c98e1',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-white">
    <div class="flex h-screen">

        <!-- LEFT SIDE -->
        <div class="hidden lg:flex w-1/2 bg-quad-dark-blue p-16 flex-col justify-center items-start text-white">
            <!-- Logo -->
            <div class="flex items-center">
                <img 
                    src="{{ asset('assets/quadmarket-logo.png') }}"
                    alt="QuadMarket Logo"
                    class="w-29 h-24 ml-20"
                >
            </div>

            <!-- Heading -->
            <h1 class="text-6xl font-bold mb-4 leading-tight">
                Selamat Datang, Admin
            </h1>
            <!-- <p class="text-xl opacity-80">
                Kelola katalog produk Anda dan jangkau lebih banyak pelanggan
            </p> -->
        </div>

        <!-- RIGHT SIDE -->
        <div class="w-full lg:w-1/2 p-8 sm:p-16 flex flex-col justify-center">

            <!-- Section Title -->
            <h2 class="text-4xl font-bold mb-4 text-gray-800">
                Masuk ke Akun Admin
            </h2>
            <!-- <p class="text-gray-500 mb-8">
                Silahkan masukkan detail Anda
            </p> -->

            @if (session('success'))
                <div class="rounded-xl bg-green-50 border border-green-200 text-green-800 px-4 py-3 mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Login Form -->
            <form action="{{ route('login.post.admin') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email_pic" class="block text-sm font-medium text-gray-700">
                        Email Admin
                    </label>
                    <input 
                        type="text"
                        name="email_pic"
                        id="email_pic"
                        value="{{ old('email_pic') }}"
                        placeholder="Masukkan email Admin"
                        required
                        class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-quad-light-blue focus:border-quad-light-blue sm:text-base"
                    >
                    @error('email_pic')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Kata Sandi
                        </label>
                    </div>

                    <div class="mt-1 relative">
                        <input 
                            type="password"
                            name="password"
                            id="password"
                            placeholder="Masukkan kata sandi Admin"
                            required
                            class="block w-full px-4 py-3 pr-10 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-quad-light-blue focus:border-quad-light-blue sm:text-base"
                        >
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer">
                            <!-- Tempat ikon show/hide password -->
                        </div>
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Login Button -->
                <div class="pt-2">
                    <button
                        type="submit"
                        class="w-full py-3 px-4 text-lg font-semibold text-white bg-blue-600 hover:bg-blue-600/90 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-quad-light-blue transition"
                    >
                        Masuk
                    </button>
                </div>
            </form>

        </div>
    </div>
</body>
</html>
