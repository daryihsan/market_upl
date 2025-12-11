<!DOCTYPE html>
<html class="light" lang="id">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Daftar sebagai Penjual | QuadMarket</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>

<style>
.form-select {
    background-image: url(https://lh3.googleusercontent.com/aida-public/AB6AXuBWpSdYhLkc54YGDd1mgM495eO7KA-YEqbeowGcCTG80_Mnid6wDglCtzyHFo5yqkKPL4qx-McPU0vHlFSbrdDGdSHajmkZEgBswITV14AmQIssrpUZsfGj4-kuFsl5wUY2w7Hb47-C_I3VUenAIFy7biPFD8HxmZz3TRu8SkxTpn-JtaywE94Iavi9Q9F4dK_gcK45_CRn3qiLqVHzf73cioW5bwquWRAXFj9qXtqw3c-0ZUXKlt4bXfBhDOWxeTUIb7jrTpv7lA);
    background-position: right 0.5rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    padding-right: 2.5rem;
}
</style>

<script id="tailwind-config">
tailwind.config = {
    darkMode: "class",
    theme: {
        extend: {
            colors: {
                "primary": "#0f3343",
                "accent": "#00D1C1",
                "background-light": "#f6f7f8",
                "background-dark": "#131b1f",
                "form-border": "#E0E0E0"
            },
            fontFamily: { "display": ["Inter", "sans-serif"] },
        },
    },
}
</script>
</head>

<body class="font-display bg-background-light dark:bg-background-dark text-[#0f3343] dark:text-white/90">
<div class="relative flex min-h-screen w-full flex-col items-center justify-center p-4 sm:p-6 lg:p-8">
<div class="w-full max-w-3xl">

<!-- Progress -->
<div class="mb-8 px-4">
    <p class="text-primary/80 dark:text-white/70 text-sm font-medium">Langkah 2 dari 3</p>
    <div class="mt-2 h-2 w-full rounded-full bg-gray-200 dark:bg-gray-700">
        <div class="h-2 rounded-full bg-accent" style="width: 66%;"></div>
    </div>
</div>

<!-- Card -->
<div class="rounded-xl border border-gray-200/50 bg-white dark:bg-background-dark dark:border-white/10 p-6 sm:p-10 shadow-lg shadow-gray-200/40 dark:shadow-none">

<div class="mb-8 text-center">
    <h1 class="text-3xl font-black tracking-tighter text-primary dark:text-white sm:text-4xl">Lengkapi Alamat Toko Anda</h1>
    <p class="mt-2 text-base text-primary/60 dark:text-white/60">Pastikan alamat yang Anda masukkan sudah benar.</p>
</div>

<form action="{{ route('register.step2.post') }}" method="POST" class="space-y-6">
@csrf

<!-- Alamat -->
<div>
    <label class="block text-sm font-medium text-primary dark:text-white/80 pb-2">Alamat Lengkap</label>
    <textarea
        name="alamat_pic"
        rows="4"
        class="form-input block w-full resize-none rounded-lg border-form-border bg-background-light/50 dark:bg-white/5 dark:border-white/20 p-4 text-base focus:border-accent focus:ring-accent"
        placeholder="Masukkan alamat lengkap toko Anda">{{ old('alamat_pic') }}</textarea>
    @error('alamat_pic')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<!-- RT RW -->
<div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
    <div>
        <label class="block pb-2 text-sm font-medium">RT</label>
        <input type="text" name="rt"
            class="form-input block w-full rounded-lg p-4 bg-background-light/50 dark:bg-white/5"
            placeholder="Contoh: 001"
            value="{{ old('rt') }}">
        @error('rt')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block pb-2 text-sm font-medium">RW</label>
        <input type="text" name="rw"
            class="form-input block w-full rounded-lg p-4 bg-background-light/50 dark:bg-white/5"
            placeholder="Contoh: 002"
            value="{{ old('rw') }}">
        @error('rw')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

<!-- Kelurahan + Kecamatan -->
<div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
    <div>
        <label class="block pb-2 text-sm font-medium">Kelurahan</label>
        <input type="text" name="kelurahan"
            class="form-input block w-full rounded-lg p-4 bg-background-light/50 dark:bg-white/5"
            placeholder="Masukkan kelurahan"
            value="{{ old('kelurahan') }}">
        @error('kelurahan')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label class="block pb-2 text-sm font-medium">Kecamatan</label>
        <input type="text" name="kecamatan"
            class="form-input block w-full rounded-lg p-4 bg-background-light/50 dark:bg-white/5"
            placeholder="Masukkan kecamatan"
            value="{{ old('kecamatan') }}">
        @error('kecamatan')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

<!-- Provinsi + Kabupaten -->
<div class="grid grid-cols-1 gap-6 sm:grid-cols-2">

    <div>
        <label class="block pb-2 text-sm font-medium">Provinsi</label>
        <select name="provinsi"
            class="form-select block w-full rounded-lg p-4 bg-background-light/50 dark:bg-white/5">
            <option value="">Pilih Provinsi</option>
            <option value="DKI Jakarta" {{ old('provinsi')=='DKI Jakarta'?'selected':'' }}>DKI Jakarta</option>
            <option value="Jawa Barat" {{ old('provinsi')=='Jawa Barat'?'selected':'' }}>Jawa Barat</option>
            <option value="Jawa Tengah" {{ old('provinsi')=='Jawa Tengah'?'selected':'' }}>Jawa Tengah</option>
            
        </select>
        @error('provinsi')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block pb-2 text-sm font-medium">Kabupaten/Kota</label>
        <select name="kabupaten"
            class="form-select block w-full rounded-lg p-4 bg-background-light/50 dark:bg-white/5">
            <option value="">Pilih Kabupaten/Kota</option>
            <option value="Jakarta Pusat" {{ old('kabupaten')=='Jakarta Pusat'?'selected':'' }}>Jakarta Pusat</option>
            <option value="Bandung" {{ old('kabupaten')=='Bandung'?'selected':'' }}>Bandung</option>
            <option value="Semarang" {{ old('kabupaten')=='Semarang'?'selected':'' }}>Semarang</option>
        </select>
        @error('kabupaten')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

<!-- Button -->
<div class="flex flex-col-reverse sm:flex-row sm:justify-between items-center pt-4 gap-4">
    <a href="{{ route('register.step1') }}"
        class="text-sm font-medium text-primary/70 dark:text-white/60 hover:text-primary dark:hover:text-white">
        Kembali
    </a>

    <button type="submit"
        class="flex items-center justify-center w-full sm:w-auto rounded-lg bg-primary px-8 py-4 text-base font-bold text-background-light shadow-sm shadow-accent/20 hover:bg-primary/90">
        Lanjutkan
        <span class="material-symbols-outlined ml-2 text-xl">arrow_forward</span>
    </button>
</div>

</form>
</div>
</div>
</div>
</body>
</html>
