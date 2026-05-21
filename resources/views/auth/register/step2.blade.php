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
        placeholder="Masukkan alamat lengkap toko Anda">{{ old('alamat_pic', $registrationData['alamat_pic'] ?? '') }}</textarea>
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
            value="{{ old('rt', $registrationData['rt'] ?? '') }}">
        @error('rt')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block pb-2 text-sm font-medium">RW</label>
        <input type="text" name="rw"
            class="form-input block w-full rounded-lg p-4 bg-background-light/50 dark:bg-white/5"
            placeholder="Contoh: 002"
            value="{{ old('rw', $registrationData['rw'] ?? '') }}">
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
            value="{{ old('kelurahan', $registrationData['kelurahan'] ?? '') }}">
        @error('kelurahan')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label class="block pb-2 text-sm font-medium">Kecamatan</label>
        <input type="text" name="kecamatan"
            class="form-input block w-full rounded-lg p-4 bg-background-light/50 dark:bg-white/5"
            placeholder="Masukkan kecamatan"
            value="{{ old('kecamatan', $registrationData['kecamatan'] ?? '') }}">
        @error('kecamatan')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

<!-- Provinsi + Kabupaten -->
<div class="grid grid-cols-1 gap-6 sm:grid-cols-2">

    <div>
        <label class="block pb-2 text-sm font-medium">Provinsi</label>
        <select name="provinsi" id="provinsi-select"
            class="form-select block w-full rounded-lg p-4 bg-background-light/50 dark:bg-white/5">
            <option value="">Pilih Provinsi</option>
            <option value="Aceh" {{ old('provinsi', $registrationData['provinsi'] ?? '')=='Aceh'?'selected':'' }}>Aceh</option>
            <option value="Sumatera Utara" {{ old('provinsi', $registrationData['provinsi'] ?? '')=='Sumatera Utara'?'selected':'' }}>Sumatera Utara</option>
            <option value="Sumatera Barat" {{ old('provinsi', $registrationData['provinsi'] ?? '')=='Sumatera Barat'?'selected':'' }}>Sumatera Barat</option>
            <option value="Riau" {{ old('provinsi', $registrationData['provinsi'] ?? '')=='Riau'?'selected':'' }}>Riau</option>
            <option value="Jambi" {{ old('provinsi', $registrationData['provinsi'] ?? '')=='Jambi'?'selected':'' }}>Jambi</option>
            <option value="Sumatera Selatan" {{ old('provinsi', $registrationData['provinsi'] ?? '')=='Sumatera Selatan'?'selected':'' }}>Sumatera Selatan</option>
            <option value="Bengkulu" {{ old('provinsi', $registrationData['provinsi'] ?? '')=='Bengkulu'?'selected':'' }}>Bengkulu</option>      
            <option value="Lampung" {{ old('provinsi', $registrationData['provinsi'] ?? '')=='Lampung'?'selected':'' }}>Lampung</option>
            <option value="Bangka Belitung" {{ old('provinsi', $registrationData['provinsi'] ?? '')=='Bangka Belitung'?'selected':'' }}>Bangka Belitung</option>
            <option value="Kepulauan Riau" {{ old('provinsi', $registrationData['provinsi'] ?? '')=='Kepulauan Riau'?'selected':'' }}>Kepulauan Riau</option>
            <option value="DKI Jakarta" {{ old('provinsi', $registrationData['provinsi'] ?? '')=='DKI Jakarta'?'selected':'' }}>DKI Jakarta</option>
            <option value="Jawa Barat" {{ old('provinsi', $registrationData['provinsi'] ?? '')=='Jawa Barat'?'selected':'' }}>Jawa Barat</option>
            <option value="Jawa Tengah" {{ old('provinsi', $registrationData['provinsi'] ?? '')=='Jawa Tengah'?'selected':'' }}>Jawa Tengah</option>
            <option value="DI Yogyakarta" {{ old('provinsi', $registrationData['provinsi'] ?? '')=='DI Yogyakarta'?'selected':'' }}>DI Yogyakarta</option>
            <option value="Jawa Timur" {{ old('provinsi', $registrationData['provinsi'] ?? '')=='Jawa Timur'?'selected':'' }}>Jawa Timur</option>       
            <option value="Banten" {{ old('provinsi', $registrationData['provinsi'] ?? '')=='Banten'?'selected':'' }}>Banten</option>
            <option value="Bali" {{ old('provinsi', $registrationData['provinsi'] ?? '')=='Bali'?'selected':'' }}>Bali</option>
            <option value="Nusa Tenggara Barat" {{ old('provinsi', $registrationData['provinsi'] ?? '')=='Nusa Tenggara Barat'?'selected':'' }}>Nusa Tenggara Barat</option>
            <option value="Nusa Tenggara Timur" {{ old('provinsi', $registrationData['provinsi'] ?? '')=='Nusa Tenggara Timur'?'selected':'' }}>Nusa Tenggara Timur</option> 
            <option value="Kalimantan Barat" {{ old('provinsi', $registrationData['provinsi'] ?? '')=='Kalimantan Barat'?'selected':'' }}>Kalimantan Barat</option>
            <option value="Kalimantan Tengah" {{ old('provinsi', $registrationData['provinsi'] ?? '')=='Kalimantan Tengah'?'selected':'' }}>Kalimantan Tengah</option>       
            <option value="Kalimantan Selatan" {{ old('provinsi', $registrationData['provinsi'] ?? '')=='Kalimantan Selatan'?'selected':'' }}>Kalimantan Selatan</option>
            <option value="Kalimantan Timur" {{ old('provinsi', $registrationData['provinsi'] ?? '')=='Kalimantan Timur'?'selected':'' }}>Kalimantan Timur</option>
            <option value="Kalimantan Utara" {{ old('provinsi', $registrationData['provinsi'] ?? '')=='Kalimantan Utara'?'selected':'' }}>Kalimantan Utara</option>
            <option value="Sulawesi Utara" {{ old('provinsi', $registrationData['provinsi'] ?? '')=='Sulawesi Utara'?'selected':'' }}>Sulawesi Utara</option>
            <option value="Sulawesi Tengah" {{ old('provinsi', $registrationData['provinsi'] ?? '')=='Sulawesi Tengah'?'selected':'' }}>Sulawesi Tengah</option>
            <option value="Sulawesi Selatan" {{ old('provinsi', $registrationData['provinsi'] ?? '')=='Sulawesi Selatan'?'selected':'' }}>Sulawesi Selatan</option>
            <option value="Sulawesi Tenggara" {{ old('provinsi', $registrationData['provinsi'] ?? '')=='Sulawesi Tenggara'?'selected':'' }}>Sulawesi Tenggara</option>
            <option value="Sulawesi Barat" {{ old('provinsi', $registrationData['provinsi'] ?? '')=='Sulawesi Barat'?'selected':'' }}>Sulawesi Barat</option>
            <option value="Gorontalo" {{ old('provinsi', $registrationData['provinsi'] ?? '')=='Gorontalo'?'selected':'' }}>Gorontalo</option>
            <option value="Maluku" {{ old('provinsi', $registrationData['provinsi'] ?? '')=='Maluku'?'selected':'' }}>Maluku</option>
            <option value="Maluku Utara" {{ old('provinsi', $registrationData['provinsi'] ?? '')=='Maluku Utara'?'selected':'' }}>Maluku Utara</option>
            <option value="Papua Barat" {{ old('provinsi', $registrationData['provinsi'] ?? '')=='Papua Barat'?'selected':'' }}>Papua Barat</option>
            <option value="Papua" {{ old('provinsi', $registrationData['provinsi'] ?? '')=='Papua'?'selected':'' }}>Papua</option>       
            </select>
        </div>

        <div>
            <label class="block pb-2 text-sm font-medium">Kabupaten/Kota</label>
            <select name="kabupaten" id="kabupaten-select"
                class="form-select block w-full rounded-lg p-4 bg-background-light/50 dark:bg-white/5" {{ empty(old('kabupaten', $registrationData['kabupaten'] ?? '')) ? 'disabled' : '' }}>
                <option value="">{{ empty(old('kabupaten', $registrationData['kabupaten'] ?? '')) ? 'Pilih Provinsi Dahulu' : 'Pilih Kabupaten/Kota' }}</option>
            </select>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const provinsiSelect = document.getElementById('provinsi-select');
        const kabupatenSelect = document.getElementById('kabupaten-select');

        // Function untuk load kabupaten berdasarkan provinsi
        function loadKabupaten(selectedProvinsi, selectedKabupaten = null) {
            // reset dropdown Kabupaten
            kabupatenSelect.innerHTML = '<option value="">Memuat...</option>';
            kabupatenSelect.disabled = true;

            if (selectedProvinsi) {
                // ganti placeholder di route AJAX dengan nilai provinsi yang dipilih
                const url = '{{ route("ajax.get.kabupaten", ["provinsi" => "PROVINCE_PLACEHOLDER"]) }}'.replace('PROVINCE_PLACEHOLDER', selectedProvinsi);

                // permintaan AJAX (menggunakan Fetch API)
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        // bersihkan dan isi ulang dropdown Kabupaten
                        kabupatenSelect.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
                        if (data.length > 0) {
                            data.forEach(kab => {
                                const option = document.createElement('option');
                                option.value = kab.nama; // 'nama' untuk value
                                option.textContent = kab.nama;
                                
                                // Set selected jika ada selectedKabupaten
                                if (selectedKabupaten && kab.nama === selectedKabupaten) {
                                    option.selected = true;
                                }
                                
                                kabupatenSelect.appendChild(option);
                            });
                            kabupatenSelect.disabled = false;
                        } else {
                            kabupatenSelect.innerHTML = '<option value="">Tidak ada data Kabupaten</option>';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching data:', error);
                        kabupatenSelect.innerHTML = '<option value="">Gagal memuat data</option>';
                    });
            } else {
                // jika provinsi kosong, kembalikan ke kondisi awal
                kabupatenSelect.innerHTML = '<option value="">Pilih Provinsi Dahulu</option>';
                kabupatenSelect.disabled = true;
            }
        }

        // Trigger load kabupaten saat page load jika provinsi sudah dipilih (dari session)
        const selectedProvinsi = provinsiSelect.value;
        const selectedKabupaten = '{{ old("kabupaten", $registrationData["kabupaten"] ?? "") }}';
        
        if (selectedProvinsi) {
            loadKabupaten(selectedProvinsi, selectedKabupaten);
        }

        // Event listener untuk change event
        provinsiSelect.addEventListener('change', function () {
            loadKabupaten(this.value, null);
        });
    });
</script>
</body>
</html>
