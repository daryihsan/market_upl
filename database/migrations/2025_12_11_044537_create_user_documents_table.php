<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            // menyimpan base64 string -> gunakan longText
            $table->longText('foto_pic')->nullable();
            $table->string('mime_pic', 50)->nullable();

            $table->longText('foto_ktp')->nullable();
            $table->string('mime_ktp', 50)->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_documents');
    }
};
