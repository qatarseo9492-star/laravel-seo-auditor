<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyzeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tool',
        'url',
        'ip_address',
        'country',
        'successful',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

