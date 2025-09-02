<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyzeLog extends Model
{
    protected $fillable = [
        'user_id','analyzer','url','ip','country','tokens_used','success',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
