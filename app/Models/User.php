<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nama_toko',
        'deskripsi',
        'nama_pic',
        'no_hp',
        'email_pic',
        'alamat_pic',
        'rt',
        'rw',
        'kelurahan',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'nik',

        // SINKRONISASI KOLOM UPLOAD/PATH SESUAI MIGRASI
        'foto_pic',
        'file_ktp',

        'password',

        // SINKRONISASI KOLOM STATUS & VERIFIKASI
        'activation_token',
        'email_verified_at',
        'status_akun', // DITAMBAHKAN
        'deactivated_by_admin',
        'verification_date', // DITAMBAHKAN
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getEmailAttribute()
    {
        return $this->email_pic;
    }

    public function username()
    {
        return 'email_pic';
    }

    public function getEmailForVerification()
    {
        return $this->email_pic;
    }


    protected $casts = [
        'email_verified_at' => 'datetime',
        'verification_date' => 'datetime',
        'deactivated_by_admin' => 'boolean',
    ];

    public function documents()
    {
        return $this->hasOne(\App\Models\UserDocument::class, 'user_id');
    }
}