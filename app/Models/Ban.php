<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ban extends Model
{
    protected $fillable = [
        'type',
        'value',
        'user_id',
        'reason',
        'admin_comment',
        'public_message',
        'expires_at',
        'banned_by',
        'is_active',
        'unbanned_by',
        'unbanned_at',
        'unban_reason',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'unbanned_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Ban reasons
    public const REASONS = [
        'spam' => 'Spam / Advertising',
        'fraud' => 'Fraud',
        'abuse' => 'Abusive Behavior',
        'fake_account' => 'Fake Account',
        'multiple_accounts' => 'Multiple Accounts',
        'payment_fraud' => 'Payment Fraud',
        'terms_violation' => 'Terms of Service Violation',
        'security_threat' => 'Security Threat',
        'bot_activity' => 'Bot Activity',
        'other' => 'Other',
    ];

    // Ban types
    public const TYPES = [
        'account' => 'Account Ban',
        'ip' => 'IP Ban',
        'fingerprint' => 'Device Ban',
    ];

    // Ban durations
    public const DURATIONS = [
        'permanent' => 'Permanent',
        '1_hour' => '1 Hour',
        '6_hours' => '6 Hours',
        '24_hours' => '24 Hours',
        '3_days' => '3 Days',
        '7_days' => '7 Days',
        '14_days' => '14 Days',
        '30_days' => '30 Days',
        '90_days' => '90 Days',
        '180_days' => '180 Days',
        '365_days' => '1 Year',
        'custom' => 'Custom',
    ];

    // Отношения
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bannedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'banned_by');
    }

    public function unbannedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'unbanned_by');
    }

    public function accessAttempts(): HasMany
    {
        return $this->hasMany(BanAccessAttempt::class);
    }

    // Скоупы
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now());
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeForAccount(Builder $query, int $userId): Builder
    {
        return $query->where('type', 'account')
            ->where('user_id', $userId);
    }

    public function scopeForIp(Builder $query, string $ip): Builder
    {
        return $query->where('type', 'ip')
            ->where('value', $ip);
    }

    public function scopeForFingerprint(Builder $query, string $fingerprint): Builder
    {
        return $query->where('type', 'fingerprint')
            ->where('value', $fingerprint);
    }

    // Методы
    public function isExpired(): bool
    {
        if (! $this->is_active) {
            return true;
        }

        if ($this->expires_at === null) {
            return false;
        }

        return $this->expires_at->isPast();
    }

    public function isPermanent(): bool
    {
        return $this->expires_at === null;
    }

    public function getRemainingTime(): ?string
    {
        if ($this->isPermanent()) {
            return 'Permanent';
        }

        if ($this->isExpired()) {
            return 'Expired';
        }

        return $this->expires_at->diffForHumans(['parts' => 2]);
    }

    public function unban(int $unbannedBy, ?string $reason = null): void
    {
        $this->update([
            'is_active' => false,
            'unbanned_by' => $unbannedBy,
            'unbanned_at' => now(),
            'unban_reason' => $reason,
        ]);
    }

    public function logAccessAttempt(?int $userId, string $ip, ?string $fingerprint, ?string $userAgent, ?string $url): void
    {
        $this->accessAttempts()->create([
            'user_id' => $userId,
            'ip_address' => $ip,
            'fingerprint' => $fingerprint,
            'user_agent' => $userAgent,
            'url' => $url,
            'attempted_at' => now(),
        ]);
    }

    // Статические методы для проверки банов
    public static function checkAccountBan(int $userId): ?self
    {
        return static::active()
            ->forAccount($userId)
            ->first();
    }

    public static function checkIpBan(string $ip): ?self
    {
        return static::active()
            ->forIp($ip)
            ->first();
    }

    public static function checkFingerprintBan(string $fingerprint): ?self
    {
        return static::active()
            ->forFingerprint($fingerprint)
            ->first();
    }

    public static function checkAllBans(?int $userId, string $ip, ?string $fingerprint): ?self
    {
        // Проверяем бан по аккаунту
        if ($userId) {
            $accountBan = static::checkAccountBan($userId);
            if ($accountBan) {
                return $accountBan;
            }
        }

        // Проверяем бан по IP
        $ipBan = static::checkIpBan($ip);
        if ($ipBan) {
            return $ipBan;
        }

        // Проверяем бан по fingerprint
        if ($fingerprint) {
            $fingerprintBan = static::checkFingerprintBan($fingerprint);
            if ($fingerprintBan) {
                return $fingerprintBan;
            }
        }

        return null;
    }

    public static function banAccount(
        int $userId,
        string $reason,
        ?string $adminComment = null,
        ?string $publicMessage = null,
        ?\DateTime $expiresAt = null,
        ?int $bannedBy = null
    ): self {
        return static::create([
            'type' => 'account',
            'value' => (string) $userId,
            'user_id' => $userId,
            'reason' => $reason,
            'admin_comment' => $adminComment,
            'public_message' => $publicMessage,
            'expires_at' => $expiresAt,
            'banned_by' => $bannedBy,
            'is_active' => true,
        ]);
    }

    public static function banIp(
        string $ip,
        string $reason,
        ?int $userId = null,
        ?string $adminComment = null,
        ?string $publicMessage = null,
        ?\DateTime $expiresAt = null,
        ?int $bannedBy = null
    ): self {
        return static::create([
            'type' => 'ip',
            'value' => $ip,
            'user_id' => $userId,
            'reason' => $reason,
            'admin_comment' => $adminComment,
            'public_message' => $publicMessage,
            'expires_at' => $expiresAt,
            'banned_by' => $bannedBy,
            'is_active' => true,
        ]);
    }

    public static function banFingerprint(
        string $fingerprint,
        string $reason,
        ?int $userId = null,
        ?string $adminComment = null,
        ?string $publicMessage = null,
        ?\DateTime $expiresAt = null,
        ?int $bannedBy = null
    ): self {
        return static::create([
            'type' => 'fingerprint',
            'value' => $fingerprint,
            'user_id' => $userId,
            'reason' => $reason,
            'admin_comment' => $adminComment,
            'public_message' => $publicMessage,
            'expires_at' => $expiresAt,
            'banned_by' => $bannedBy,
            'is_active' => true,
        ]);
    }

    // Автоматическая деактивация истёкших банов
    public static function deactivateExpiredBans(): int
    {
        return static::expired()
            ->update(['is_active' => false]);
    }
}
