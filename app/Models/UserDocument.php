<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDocument extends Model
{
    protected $table = 'user_documents';

    protected $fillable = [
        'user_id',
        'foto_pic',
        'mime_pic',
        'foto_ktp',
        'mime_ktp',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
