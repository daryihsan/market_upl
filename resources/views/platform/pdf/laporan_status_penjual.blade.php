<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Status Penjual</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1, h2, h3 { margin: 0; }
        .header { text-align: center; margin-bottom: 10px; }
        .subheader { font-size: 12px; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 6px 4px; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .meta { margin-top: 5px; font-size: 11px; }
    </style>
</head>
<body>
<div class="header">
    <h2>Laporan Status Penjual</h2>
    <div class="subheader">QuadMarket – Marketplace UMKM</div>
</div>

<div class="meta">
    Status: <strong>{{ $statusLabel }}</strong><br>
    Tanggal Generate: {{ $generatedAt->format('d M Y H:i') }}<br>
    Diproses oleh: {{ $processedBy }}
</div>

<table>
    <thead>
    <tr>
        <th style="width:30px;">No</th>
        <th>Nama Penjual</th>
        <th>Email</th>
        <th>Status</th>
        <th style="width:100px;">Tanggal Bergabung</th>
    </tr>
    </thead>
    <tbody>
    @forelse ($sellers as $index => $seller)
        <tr>
            <td align="center">{{ $index + 1 }}</td>
            <td>{{ $seller->nama_toko ?? '-' }}</td>
            <td>{{ $seller->email_pic ?? $seller->email }}</td>
            <td>
                @php
                    $status = $seller->status_akun;
                    $label  = $status === 'active' ? 'Aktif' :
                              ($status === 'pending' ? 'Tidak Aktif' : 'Tidak Aktif');
                @endphp
                {{ $label }}
            </td>
            <td>{{ optional($seller->created_at)->format('d M Y') }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="5" align="center">Tidak ada data penjual.</td>
        </tr>
    @endforelse
    </tbody>
</table>
</body>
</html>