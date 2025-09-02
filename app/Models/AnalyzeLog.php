<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyzeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'analyzer',
        'url',
        'ip',
        'country',
        'tokens_used',
        'success',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
