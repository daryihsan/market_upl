<header class="shadow-md sticky top-0 bg-white z-50">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex justify-between items-center">

        <!-- KIRI: Logo & Menu -->
        <div class="flex items-center space-x-6">
            <div class="flex items-center space-x-2">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="QuadMarket Logo" class="h-12">
                </a>
            </div>

            <div class="hidden lg:flex items-center space-x-6 text-gray-700">
                <a href="#" class="hover:text-blue-600 flex items-center">
                    Kategori
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 9l-7 7-7-7"></path>
                    </svg>
                </a>
                <a href="#" class="hover:text-blue-600">Produk</a>
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