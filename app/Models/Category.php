<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // Tambahkan 'slug' ke properti $fillable agar Laravel mengizinkan 
    // pengisian massal (mass assignment) saat menggunakan Category::create() atau $category->update().
    protected $fillable = ['name', 'slug', 'icon_path'];
    
    public $timestamps = false;

    /**
     * Relasi ke produk
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}