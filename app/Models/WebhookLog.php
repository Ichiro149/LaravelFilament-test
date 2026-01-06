<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookLog extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_SUCCESS = 'success';

    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'webhook_id',
        'event',
        'payload',
        'response_status',
        'response_body',
        'attempt',
        'status',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'sent_at' => 'datetime',
    ];

    /**
     * Get the webhook that owns this log.
     */
    public function webhook(): BelongsTo
    {
        return $this->belongsTo(Webhook::class);
    }

    /**
     * Mark log as successful.
     */
    public function markAsSuccess(int $status, ?string $body): void
    {
        $this->update([
            'status' => self::STATUS_SUCCESS,
            'response_status' => $status,
            'response_body' => $body ? substr($body, 0, 10000) : null,
            'sent_at' => now(),
        ]);
    }

    /**
     * Mark log as failed.
     */
    public function markAsFailed(?int $status, ?string $error): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'response_status' => $status,
            'error_message' => $error,
            'sent_at' => now(),
        ]);
    }

    /**
     * Check if can retry.
     */
    public function canRetry(): bool
    {
        return $this->status === self::STATUS_FAILED
            && $this->attempt < $this->webhook->max_retries;
    }
}
