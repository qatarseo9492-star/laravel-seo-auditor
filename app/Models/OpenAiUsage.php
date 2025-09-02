<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpenAiUsage extends Model
{
    protected $table = 'openai_usage';

    protected $fillable = [
        'user_id','model','prompt_tokens','completion_tokens','total_tokens','cost_usd','meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'cost_usd' => 'decimal:4',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
