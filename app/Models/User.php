<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'is_admin', 'is_banned', 
        'last_seen_at', 'last_ip', 'last_country'
    ];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'is_admin' => 'boolean',
        'is_banned' => 'boolean',
    ];

    public function limit()
    {
        return $this->hasOne(UserLimit::class);
    }

    public function logs()
    {
        return $this->hasMany(AnalyzeLog::class);
    }
    
    public function usage()
    {
        return $this->hasMany(OpenAiUsage::class);
    }
}
