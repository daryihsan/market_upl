<!DOCTYPE html>
<html lang="id">
<head>
    
<meta charset="UTF-8">
    
<meta name="viewport" content="width=device-width,
 initial-scale=1.0">
    <title>Laporan Penjual per Provinsi</title>
    
<style>
        /* Menggunakan font yang kompatibel untuk DomPDF */
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1, h2, h3 { margin: 0; }
        
        .header { text-align: center; margin-bottom: 15px; }
        .subheader { font-size: 14px; margin-top: 4px; }
        .meta { margin-top: 10px; font-size: 11px; margin-bottom: 15px; text-align: center; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 6px 4px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; font-size: 11px; text-transform: uppercase; }
        
        .text-center { text-align: center; }
    </style>
</head>
<body>

    
<div class="header">
        <h2>Laporan Daftar Penjual Berdasarkan Provinsi</h2>
        
<div class="subheader">QuadMarket Marketplace UMKM</div>
    </div>
    
<div class="meta">
        Filter Provinsi: <strong>{{ $provinsi }}</strong><br>
        Tanggal Generate: <strong>{{ $generatedAt->format('d M Y H:i') }}</strong><br>
        Diproses oleh: {{ $processedBy }}
    </div>

    
<table>
        
<thead>
            
<tr>
                <th style="width:30px;">No</th>
                <th>Nama Toko</th>
                <th>Nama PIC</th>
                <th style="width:120px;">Propinsi</th>
            </tr>
        </thead>
        
<tbody>
            @forelse ($sellers as $index => $seller)
                
<tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $seller->nama_toko ?? '-' }}</td>
                    <td>{{ $seller->nama_pic ?? '-' }}</td>
                    <td>{{ $seller->provinsi ?? '-' }}</td>
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