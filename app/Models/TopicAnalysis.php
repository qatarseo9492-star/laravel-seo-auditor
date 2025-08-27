<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TopicAnalysis extends Model
{
    use HasFactory;

    protected $table = 'topic_analyses';

    protected $fillable = [
        'urls_list',
        'urls_signature',
        'analysis_result',
        'openai_metadata',
    ];

    protected $casts = [
        'urls_list' => 'array',
        'analysis_result' => 'array',
        'openai_metadata' => 'array',
    ];
}
