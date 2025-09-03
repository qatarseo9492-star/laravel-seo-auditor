<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpenAiUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'analyze_log_id',
        'model',
        'prompt_tokens',
        'completion_tokens',
        'total_tokens',
        'cost',
    ];

    public function log()
    {
        return $this->belongsTo(AnalyzeLog::class);
    }
}
