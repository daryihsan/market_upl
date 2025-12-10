<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 
        'full_name', 
        'email_address', 
        'phone_number', 
        'province',
        'rating', 
        'review_text'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}