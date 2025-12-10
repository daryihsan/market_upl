<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            // Foreign Key ke produk yang diulas
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); 
            
            // Data pengunjung (SRS-MartPlace-06)
            $table->string('full_name');
            $table->string('email_address');
            $table->string('phone_number');
            
            $table->string('provinsi')->nullable(); // <-- TAMBAH INI (Poin 1)

            // Data ulasan (SRS-MartPlace-06)
            $table->unsignedTinyInteger('rating'); // Skala 1 sampai 5
            $table->text('review_text');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};