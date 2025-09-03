<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLimit extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_limits';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'daily_limit',
        'searches_today',
        'monthly_limit',
        'searches_this_month',
    ];

    /**
     * Get the user that owns the limit.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
