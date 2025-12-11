@php
    // Data dari PlatformController::dashboard()
    $total_penjual_aktif = $total_penjual_aktif ?? 0;
    $total_penjual_tidak_aktif = $total_penjual_tidak_aktif ?? 0;
    $total_commenters = $total_commenters ?? 0;
    $total_penjual = $total_penjual ?? 0;
    $pending_verifications_count = $pending_verifications_count ?? 0;

    $product_chart_data = $product_chart_data ?? ['labels' => [], 'data' => []];
    $location_chart_data = $location_chart_data ?? [];

    // Persiapan data untuk Chart.js (Location Donut)
    $location_labels = collect($location_chart_data)->pluck('provinsi')->toArray();
    $location_percentages = collect($location_chart_data)->pluck('percentage')->toArray();
    
    // Warna yang menarik dan kontras
    $location_colors = [
        '#007bff', // Biru terang
        '#28a745', // Hijau
        '#ffc107', // Kuning
        '#dc3545', // Merah
        '#6f42c1', // Ungu
        '#6c757d', // Abu-abu (untuk Lainnya)
    ];

    // Persiapan data untuk Chart.js (Product Bar)
    $product_labels = $product_chart_data['labels']->toArray();
    $product_counts = $product_chart_data['data']->toArray();
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Platform - QuadMarket</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F9FAFB;
        }

        /* same as other pages */
        .sidebar {
            width: 250px;
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 0 8px rgba(13, 27, 60, 0.04);
            flex-shrink: 0;
            position: fixed;
            height: 100%;
        }

        .main-content {
            flex-grow: 1;
            padding: 28px;
            margin-left: 250px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border-radius: 8px;
            color: #6b7280;
            transition: all .15s;
        }

        .nav-link:hover {
            background-color: #eef2ff;
            color: #1e3a8a;
        }

        .nav-link.active {
            background-color: #eef2ff;
            color: #1e3a8a;
            font-weight: 600;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(17, 24, 39, 0.04);
            padding: 18px;
        }

        .small-muted {
            color: #6b7280;
            font-size: .9rem;
        }

        .logo-md {
            height: 56px;
        }

        /* same size used across other pages */
    </style>
</head>

<body>
    <div class="flex min-h-screen">
        {{-- SIDEBAR (unchanged functionality) --}}
        <aside class="sidebar">
            <div class="p-6 border-b mb-4">
                <h3 class="font-bold text-lg text-gray-800">Admin Menu</h3>
            </div>
            <nav class="space-y-2">
                <a href="{{ route('platform.dashboard') }}" class="nav-link active"><i
                        class="fas fa-chart-line mr-3"></i> Dashboard</a>
                <a href="{{ route('platform.verifikasi.list') }}" class="nav-link"><i
                        class="fas fa-check-circle mr-3"></i> Verifikasi Penjual</a>
                <a href="{{ route('platform.laporan') }}" class="nav-link"><i class="fas fa-file-alt mr-3"></i>
                    Laporan</a>
                <a href="{{ route('platform.categories.index') }}" class="nav-link"><i class="fas fa-tags mr-3"></i>
                    Manajemen Kategori</a>
            </nav>

            <div class="absolute bottom-0 w-full pr-4 border-t p-4 bg-white">
                <a href="#" class="nav-link">
                    <i class="fas fa-cog mr-3"></i>
                    <span>Pengaturan</span>
                </a>
                <a href="#" class="nav-link">
                    <i class="fas fa-question-circle mr-3"></i>
                    <span>Bantuan</span>
                </a>
            </div>
        </aside>

        {{-- MAIN --}}
        
        <main class="main-content">
            {{-- Topbar: logo + title + date (consistent with other pages) --}}
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-blue-900">Dashboard Platform</h1>
                    <p class="text-sm text-gray-500 mt-1">Ringkasan visual untuk metrik utama</p>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <img src="{{ url('assets/images/logo.png') }}" alt="QuadMarket" class="h-20">
                        <div class="text-sm text-gray-500 mt-1">{{ now()->format('d M Y') }}</div>
                    </div>
                </div>
            </div>

            {{-- Grid area: top row cards --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                {{-- Seller status card --}}
                <div class="card lg:col-span-1">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold text-slate-900">Status Penjual</h3>
                        <span class="small-muted">Aktif / Tidak Aktif</span>
                    </div>

                    <div class="flex items-center gap-6">
                        <div style="width:180px; height:180px;">
                            <canvas id="sellerDonut"></canvas>
                        </div>

                        <div class="flex-1">
                            <div class="mb-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span class="w-3 h-3 rounded-full" style="background:#16a34a;"></span>
                                        <div>
                                            <div class="text-sm text-gray-600">Aktif</div>
                                            <div class="text-xl font-bold text-slate-900">
                                                {{ number_format($total_penjual_aktif) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span class="w-3 h-3 rounded-full" style="background:#ef4444;"></span>
                                        <div>
                                            <div class="text-sm text-gray-600">Tidak Aktif</div>
                                            <div class="text-xl font-bold text-slate-900">
                                                {{ number_format($total_penjual_tidak_aktif) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Reviews line chart --}}
                <div class="card lg:col-span-2">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold text-slate-900">Interaksi Pengunjung (Rating/Komentar)</h3>

                        <form method="GET" action="{{ route('platform.dashboard') }}" id="reviewForm"
                            class="flex items-center gap-2">
                            {{-- preserve selected categories --}}
                            <input type="hidden" name="top_categories" value="{{ $selected_top_categories }}">

                            <select name="review_period" onchange="document.getElementById('reviewForm').submit()"
                                class="border rounded px-3 py-1 bg-white text-sm">
                                <option value="7" {{ $selected_review_period == 7 ? 'selected' : '' }}>7 Hari</option>
                                <option value="14" {{ $selected_review_period == 14 ? 'selected' : '' }}>14 Hari</option>
                                <option value="30" {{ $selected_review_period == 30 ? 'selected' : '' }}>30 Hari</option>
                                <option value="90" {{ $selected_review_period == 90 ? 'selected' : '' }}>90 Hari</option>
                            </select>
                        </form>
                    </div>


                    <div>
                        {{-- chart --}}
                        <canvas id="reviewsLine" style="height:220px;"></canvas>
                    </div>
                </div>
            </div>

            {{-- second row: kategori & provinsi --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Kategori card --}}
                <div class="card">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold text-slate-900">Sebaran Jumlah Produk Berdasarkan Kategori</h3>
                        <div class="small-muted">Menampilkan:
                            {{ $selected_top_categories === 'all' ? 'Semua' : 'Top ' . $selected_top_categories }}</div>
                    </div>

                    {{-- dropdown inside card --}}
                    <div class="mb-3 flex items-center gap-3">
                        <label class="small-muted">Tampilkan:</label>
                        <form method="GET" action="{{ route('platform.dashboard') }}" id="categoryForm" class="inline">
                            {{-- preserve review_period --}}
                            <input type="hidden" name="review_period" value="{{ $selected_review_period }}">
                            <select name="top_categories" onchange="document.getElementById('categoryForm').submit()"
                                class="border rounded px-3 py-1 bg-white">
                                <option value="5" {{ $selected_top_categories == '5' ? 'selected' : '' }}>Top 5</option>
                                <option value="10" {{ $selected_top_categories == '10' ? 'selected' : '' }}>Top 10
                                </option>
                                <option value="all" {{ $selected_top_categories == 'all' ? 'selected' : '' }}>Semua
                                </option>
                            </select>
                        </form>
                    </div>

                    {{-- responsive container: if many categories, allow taller card + scroll --}}
                    <div class="{{ count($category_labels) > 12 ? 'max-h-[520px] overflow-y-auto' : '' }}">
                        <canvas id="categoryBar" style="min-height:240px;"></canvas>
                    </div>
                </div>

                {{-- Provinsi card --}}
                <div class="card">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold text-slate-900">Distribusi Toko per Provinsi</h3>
                        <div class="small-muted">Semua provinsi dinamis</div>
                    </div>

                    @php
                        $provCount = count($provinsi_labels);
                    @endphp

                    {{-- layout switch: side list (<=6) or below list (>6) --}}
                        @if($provCount <= 6)
                            <div class="flex gap-6 items-center">
                                <div style="width:55%;">
                                    <canvas id="provinsiDoughnut" style="height:260px;"></canvas>
                                </div>
                                <div style="width:45%;">
                                    <ul class="text-sm space-y-2">
                                        @foreach($provinsi_labels as $i => $prov)
                                            <li class="flex items-center justify-between">
                                                <span class="truncate">{{ $prov }}</span>
                                                <span class="font-semibold">{{ $provinsi_counts[$i] ?? 0 }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @else
                            {{-- many provinces -> donut centered, list below --}}
                            <div class="flex justify-center mb-4">
                                <div style="width:260px;">
                                    <canvas id="provinsiDoughnut" style="height:260px;"></canvas>
                                </div>
                            </div>
                            <div class="overflow-y-auto max-h-64">
                                <ul class="text-sm space-y-2">
                                    @foreach($provinsi_labels as $i => $prov)
                                        <li class="flex items-center justify-between border-b py-2">
                                            <span class="truncate">{{ $prov }}</span>
                                            <span class="font-semibold">{{ $provinsi_counts[$i] ?? 0 }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                </div>
            </div>
        </main>
    </div>

    {{-- Pass PHP data to JS --}}
    <script>
        const sellerData = {
            active: {{ json_encode($total_penjual_aktif) }},
            inactive: {{ json_encode($total_penjual_tidak_aktif) }}
        };

        const reviewsLabels = {!! json_encode($reviews_labels) !!};
        const reviewsCounts = {!! json_encode($reviews_counts) !!};

        const categoryLabels = {!! json_encode($category_labels) !!};
        const categoryCounts = {!! json_encode($category_counts) !!};

        const provinsiLabels = {!! json_encode($provinsi_labels) !!};
        const provinsiCounts = {!! json_encode($provinsi_counts) !!};
    </script>

    {{-- Charts render --}}
    <script>
        // Seller donut
        (function () {
            const ctx = document.getElementById('sellerDonut').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Aktif', 'Tidak Aktif'],
                    datasets: [{
                        data: [sellerData.active, sellerData.inactive],
                        backgroundColor: ['#16a34a', '#ef4444'],
                        borderWidth: 0
                    }]
                },
                options: {
                    cutout: '72%',
                    plugins: { legend: { display: false }, tooltip: { callbacks: { label: function (ctx) { return ctx.label + ': ' + ctx.parsed; } } } },
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        })();

        // Reviews line
        (function () {
            const ctx = document.getElementById('reviewsLine').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: reviewsLabels,
                    datasets: [{
                        label: 'Jumlah Interaksi',
                        data: reviewsCounts,
                        tension: 0.25, fill: true,
                        backgroundColor: 'rgba(99,102,241,0.06)',
                        borderColor: 'rgba(99,102,241,0.95)',
                        pointRadius: 3, pointHoverRadius: 5
                    }]
                },
                options: {
                    scales: { x: { grid: { display: false } }, y: { beginAtZero: true, ticks: { precision: 0 } } },
                    plugins: { legend: { display: false }, tooltip: { mode: 'index', intersect: false } },
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        })();

        // Category horizontal bar
        (function () {
            const ctx = document.getElementById('categoryBar').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: categoryLabels,
                    datasets: [{
                        label: 'Jumlah Produk',
                        data: categoryCounts,
                        borderRadius: 8, barThickness: 16,
                        backgroundColor: 'rgba(59,130,246,0.95)'
                    }]
                },
                options: {
                    indexAxis: 'y',
                    scales: { x: { beginAtZero: true, ticks: { precision: 0 } }, y: { ticks: { autoSkip: false } } },
                    plugins: { legend: { display: false }, tooltip: { callbacks: { label: function (ctx) { return ctx.dataset.label + ': ' + ctx.parsed.x; } } } },
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        })();

        // Provinsi donut
        (function () {
            const ctx = document.getElementById('provinsiDoughnut').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: provinsiLabels,
                    datasets: [{
                        data: provinsiCounts,
                        backgroundColor: (function () {
                            const palette = ['#3b82f6', '#10b981', '#f97316', '#ef4444', '#8b5cf6', '#06b6d4', '#f59e0b', '#ec4899', '#06b6d4', '#60a5fa'];
                            let out = [];
                            for (let i = 0; i < provinsiLabels.length; i++) { out.push(palette[i % palette.length]); }
                            return out;
                        })(),
                        borderWidth: 0
                    }]
                },
                options: {
                    cutout: '65%',
                    plugins: { legend: { display: false }, tooltip: { callbacks: { label: function (ctx) { return ctx.label + ': ' + ctx.parsed; } } } },
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        })();
    </script>

    {{-- fontawesome --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"
        crossorigin="anonymous"></script>
</body>

</html>