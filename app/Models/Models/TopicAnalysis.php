<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopicAnalysis extends Model
{
    protected $fillable = [
        'urls_list', 'urls_signature', 'analysis_result', 'openai_metadata',
    ];

    protected $casts = [
        'urls_list' => 'array',
        'analysis_result' => 'array',
        'openai_metadata' => 'array',
    ];
}
