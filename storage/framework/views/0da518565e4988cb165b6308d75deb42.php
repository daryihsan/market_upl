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
        Filter Status: <strong><?php echo e($statusLabel); ?></strong><br>
        Tanggal Generate: <strong><?php echo e($generatedAt->format('d M Y H:i')); ?></strong><br>
        Diproses oleh: <?php echo e($processedBy); ?>

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
            <?php $__empty_1 = true; $__currentLoopData = $sellers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $seller): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                
<tr>
                    <td class="text-center"><?php echo e($index + 1); ?></td>
                    <td><?php echo e(str_pad(strstr($seller->email_pic, '@', true), 3, '0', STR_PAD_LEFT)); ?></td>
                    <td><?php echo e($seller->nama_pic ?? '-'); ?></td>
                    <td><?php echo e($seller->nama_toko ?? '-'); ?></td>
                    <td>
                        <?php
                            $status = $seller->status_akun;
                            $label = $status === 'active' ? 'Aktif' : ($status === 'pending' ? 'Pending' : 'Tidak Aktif');
                        ?>
                        <?php echo e($label); ?>

                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                
<tr>
                    <td colspan="6" class="text-center">Tidak ada data penjual.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html><?php /**PATH C:\laragon\www\QuadMarket\resources\views/platform/pdf/laporan_status_penjual.blade.php ENDPATH**/ ?>