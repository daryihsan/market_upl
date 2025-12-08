<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            
            // Data Toko
            $table->string('nama_toko');
            $table->text('deskripsi')->nullable();

            // Data PIC
            $table->string('nama_pic');
            $table->string('no_hp');
            $table->string('email_pic')->unique();
            $table->text('alamat_pic');
            $table->string('rt', 10);
            $table->string('rw', 10);
            $table->string('kelurahan');
            $table->string('kecamatan');
            $table->string('kabupaten');
            $table->string('provinsi');

            // Identitas
            $table->string('nik')->unique();
            $table->string('foto_pic')->nullable();
            $table->string('file_ktp')->nullable();

            // Verifikasi
            $table->string('activation_token')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->enum('status_akun', ['pending', 'active', 'rejected'])->default('pending');
            $table->dateTime('verification_date')->nullable();

            // Keamanan
            $table->string('password');
            $table->rememberToken();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
