<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BanAccessAttempt extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'ban_id',
        'user_id',
        'ip_address',
        'fingerprint',
        'user_agent',
        'url',
        'attempted_at',
    ];

    protected $casts = [
        'attempted_at' => 'datetime',
    ];

    public function ban(): BelongsTo
    {
        return $this->belongsTo(Ban::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
