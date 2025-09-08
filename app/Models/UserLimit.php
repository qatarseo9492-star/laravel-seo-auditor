<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserLimit extends Model
{
    use HasFactory;

    protected $table = 'user_limits';

    protected $fillable = [
        'user_id',
        'daily_limit',
        'is_enabled',
        'reset_at',
        'reason',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'reset_at'   => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Scopes */
    public function scopeEnabled($q)         { return $q->where('is_enabled', true); }
    public function scopeForUser($q, $id)    { return $q->where('user_id', $id); }

    /** Helpers */
    public function enable(?string $reason=null): void  { $this->is_enabled = true;  $this->reason = $reason; $this->save(); }
    public function disable(?string $reason=null): void { $this->is_enabled = false; $this->reason = $reason; $this->save(); }
    public function isEnabled(): bool { return (bool) $this->is_enabled; }
}
