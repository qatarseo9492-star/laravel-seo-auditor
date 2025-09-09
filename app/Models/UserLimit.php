<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLimit extends Model
{
    protected $table = 'user_limits';

    protected $fillable = [
        'user_id',
        'daily_limit',
        'is_enabled',
        'reason',
    ];

    // Ensure booleans come back as bools if stored as tinyint
    protected $casts = [
        'is_enabled' => 'boolean',
        'daily_limit' => 'integer',
    ];
}
