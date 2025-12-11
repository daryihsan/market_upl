<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<title>Laporan Status Penjual</title>
    
<style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1, h2, h3 { margin: 0; }
        
        .header { text-align: center; margin-bottom: 15px; }
        .subheader { font-size: 14px; margin-top: 4px; }
        .meta { margin-top: 10px; font-size: 11px; margin-bottom: 15px; text-align: center; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 6px 4px; }
        th { background-color: #f0f0f0; font-weight: bold; font-size: 11px; text-transform: uppercase; }
        
        .text-center { text-align: center; }
    </style>
</head>
<body>

    
<div class="header">
        <h2>Laporan Status Penjual</h2>
        
<div class="subheader">QuadMarket Marketplace UMKM</div>
    </div>
    
<div class="meta">
        Filter Status: <strong>{{ $statusLabel }}</strong><br>
        Tanggal Generate: <strong>{{ $generatedAt->format('d M Y H:i') }}</strong><br>
        Diproses oleh: {{ $processedBy }}
    </div>

    
<table>
        
<thead>
            
<tr>
                <th style="width:30px;">No</th>
                <th>Nama User</th>
                <th>Nama PIC</th>
                <th>Nama Toko</th>
                <th style="width:100px;">Status</th>
            </tr>
        </thead>
        
<tbody>
            @forelse ($sellers as $index => $seller)
                
<tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ str_pad(strstr($seller->email_pic, '@', true), 3, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $seller->nama_pic ?? '-' }}</td>
                    <td>{{ $seller->nama_toko ?? '-' }}</td>
                    <td>
                        @php
                            $status = $seller->status_akun;
                            $label = $status === 'active' ? 'Aktif' : ($status === 'pending' ? 'Pending' : 'Tidak Aktif');
                        @endphp
                        {{ $label }}
                    </td>
                </tr>
            @empty
                
<tr>
                    <td colspan="6" class="text-center">Tidak ada data penjual.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>