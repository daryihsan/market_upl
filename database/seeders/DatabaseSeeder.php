<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\User::create([
            // ini diisi dummy buat seeders soalnya kan 1 db users dan di db diset not null biar penjual harus ngisi
            // DUMMY SEMUA YA, yg kepake jg email sm pass doang buat login admin
            'nama_toko' => 'Admin System',
            'nama_pic' => 'Admin Utama', 
            'no_hp' => '081234567890', 
            'email_pic' => 'admin@example.com', 
            'alamat_pic' => 'Jalan Admin', 
            'rt' => '001',
            'rw' => '001',
            'kelurahan' => 'Admin Jaya',
            'kecamatan' => 'Admin Sentosa',
            'kabupaten' => 'Admin City',
            'provinsi' => 'Admin Land',
            'nik' => '0000000000000001', // NIK unik dummy
            
            'password' => bcrypt('pass123'), 

            'role' => 'admin',
            'status_akun' => 'active', 
            
        ]);
    }
}