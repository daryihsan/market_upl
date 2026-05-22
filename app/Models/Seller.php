<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// KOREKSI 1: Ganti Illuminate\Database\Eloquent\Model menjadi Authenticatable
use Illuminate\Foundation\Auth\User as Authenticatable; 
// DITAMBAHKAN: Import Notifiable dan MustVerifyEmail
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail; 


// KOREKSI 2: Implementasi Authenticatable, Notifiable, dan MustVerifyEmail
class Seller extends Authenticatable implements MustVerifyEmail
{
    // KOREKSI 3: Tambahkan trait Notifiable
    use HasFactory, Notifiable; 
    
    // Sesuaikan dengan nama tabel
    protected $table = 'sellers'; 

    protected $fillable = [
        'nama_toko',
        'deskripsi_singkat',
        'nama_pic',
        'no_hp_pic',
        'email_pic',
        'alamat_pic',
        'rt',
        'rw',
        'nama_kelurahan',
        'kabupaten_kota',
        'propinsi', // Ganti 'provinsi' di controller menjadi 'propinsi'
        'no_ktp_pic',
        'file_ktp_path',
        'foto_pic_path',
        
        // DITAMBAHKAN: Kolom untuk otentikasi dan verifikasi
        'password', // Wajib untuk login
        'activation_token', // Wajib untuk link verifikasi
        'email_verified_at', // Wajib untuk middleware 'verified'
        
        'status_akun', 
        'deactivated_by_admin',
        'verification_date',
    ];
    
    // DITAMBAHKAN: Kolom untuk disembunyikan
    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime', // DITAMBAHKAN
            'verification_date' => 'datetime',
            'password' => 'hashed', // DITAMBAHKAN
            'deactivated_by_admin' => 'boolean',
        ];
    }
}