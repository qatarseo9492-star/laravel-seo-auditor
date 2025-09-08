<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// Explicit imports (harmless even within the same namespace)
use App\Models\UserLimit;
use App\Models\AnalyzeLog;
use App\Models\OpenAiUsage;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * Mass-assignable attributes
     */
    protected $fillable = [
        'name', 'email', 'password', 'is_admin', 'is_banned',
        'last_seen_at', 'last_ip', 'last_country',
    ];

    /**
     * Hidden attributes (security best-practice)
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casts
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_seen_at'      => 'datetime',
        'is_admin'          => 'boolean',
        'is_banned'         => 'boolean',
        // NOTE: If your Laravel version supports it and you want automatic hashing:
        // 'password'       => 'hashed',
    ];

    /* =========================
     | Relationships
     |=========================*/
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

    /* =========================
     | Scopes (handy filters)
     |=========================*/
    public function scopeOnlineWithin($query, int $minutes = 5)
    {
        return $query->where('last_seen_at', '>=', now()->subMinutes($minutes));
    }

    public function scopeAdmin($query)
    {
        return $query->where('is_admin', true);
    }

    public function scopeBanned($query)
    {
        return $query->where('is_banned', true);
    }

    /* ======================================
     | Convenience helpers for user limits
     |======================================*/
    /**
     * Ensure a UserLimit row exists for this user (idempotent).
     */
    public function ensureLimit(int $defaultDaily = 200): UserLimit
    {
        return $this->limit()->firstOrCreate(
            ['user_id' => $this->id],
            ['daily_limit' => $defaultDaily, 'is_enabled' => true]
        );
    }

    /**
     * Return the user's daily limit (default if not set).
     */
    public function dailyLimit(int $fallback = 200): int
    {
        $limit = $this->limit;
        return $limit ? (int) $limit->daily_limit : $fallback;
    }

    /**
     * Whether limits are enabled for the user (defaults to true if missing).
     */
    public function isLimitEnabled(): bool
    {
        $limit = $this->limit;
        return $limit ? (bool) $limit->is_enabled : true;
    }

    /**
     * Enable limits for the user (creates row if needed).
     */
    public function enableLimits(?string $reason = null, int $defaultDaily = 200): void
    {
        $limit = $this->ensureLimit($defaultDaily);
        $limit->is_enabled = true;
        if ($reason !== null) {
            $limit->reason = $reason;
        }
        $limit->save();
    }

    /**
     * Disable limits for the user (creates row if needed).
     */
    public function disableLimits(?string $reason = null, int $defaultDaily = 200): void
    {
        $limit = $this->ensureLimit($defaultDaily);
        $limit->is_enabled = false;
        if ($reason !== null) {
            $limit->reason = $reason;
        }
        $limit->save();
    }
}
