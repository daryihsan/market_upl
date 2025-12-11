<!DOCTYPE html>
<html lang="id">
<head>
    
<meta charset="UTF-8">
    
<meta name="viewport" content="width=device-width,
 initial-scale=1.0">
    <title>Laporan Produk Lengkap</title>
    
<style>
        /* Menggunakan font yang kompatibel untuk DomPDF */
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1, h2, h3 { margin: 0; }
        
        .header { text-align: center; margin-bottom: 15px; }
        .subheader { font-size: 14px; margin-top: 4px; }
        .meta { margin-top: 10px; font-size: 11px; margin-bottom: 15px; text-align: center; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; table-layout: fixed; }
        th, td { border: 1px solid #000; padding: 6px 4px; text-align: left; word-wrap: break-word; }
        th { background-color: #f0f0f0; font-weight: bold; font-size: 11px; text-transform: uppercase; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .w-30 { width: 30px; }
        .w-70 { width: 70px; }
        .w-100 { width: 100px; }
        .w-150 { width: 150px; }
    </style>
</head>
<body>

    
<div class="header">
        <h2>Laporan Daftar Produk Berdasarkan Rating</h2>
        
<div class="subheader">QuadMarket Marketplace UMKM</div>
    </div>
    
<div class="meta">
        Filter Kategori: <strong>{{ $categoryLabel ?? 'Semua Kategori' }}</strong><br>
        Filter Rating Minimum: <strong>{{ $ratingLabel ?? 'Semua Rating' }}</strong><br>
        Tanggal Generate: <strong>{{ $generatedAt->format('d M Y H:i') }}</strong><br>
        Diproses oleh: {{ $processedBy }}
    </div>
    
<table>
        
<thead>
            
<tr>
                <th class="w-10">No</th>
                <th class="w-150">Produk</th>
                <th class="w-100">Kategori</th>
                <th class="w-100">Harga</th>
                <th class="w-70">Rating</th>
                <th class="w-70">Nama Toko</th>
                <th>Propinsi</th>
            </tr>
        </thead>
        
<tbody>
            @forelse ($products as $index => $product)
                
<tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category->name ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td class="text-center">{{ number_format($product->rating ?? 0, 1) }}</td>
                    <td>{{ $product->user->nama_toko ?? 'N/A' }}</td>
                    <td>{{ $product->user->provinsi ?? 'N/A' }}</td>
                </tr>
            @empty
                
<tr>
                    <td colspan="8" class="text-center">Tidak ada data produk.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>