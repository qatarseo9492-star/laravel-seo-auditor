<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        // 🔹 new fields from migration
        'role',
        'daily_quota',
        'monthly_quota',
        'banned_at',
        'last_seen_at',
        'last_ip',
        'last_country',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'banned_at'         => 'datetime',
        'last_seen_at'      => 'datetime',
    ];

    /**
     * 🔹 Relationship: user → analyze logs
     */
    public function analyzeLogs()
    {
        return $this->hasMany(\App\Models\AnalyzeLog::class);
    }

    /**
     * 🔹 Relationship: user → OpenAI usage
     */
    public function openAiUsage()
    {
        return $this->hasMany(\App\Models\OpenAiUsage::class);
    }

    /**
     * 🔹 Check if user is banned
     */
    public function isBanned(): bool
    {
        return !is_null($this->banned_at);
    }

    /**
     * 🔹 Check if user has admin role
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
