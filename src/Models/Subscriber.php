<?php

/**
 * Newsletter — Contensio plugin.
 * https://contensio.com
 *
 * @copyright   Copyright (c) 2026 Iosif Gabriel Chimilevschi
 * @license     https://www.gnu.org/licenses/agpl-3.0.txt  AGPL-3.0-or-later
 */

namespace Contensio\Newsletter\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Subscriber extends Model
{
    protected $table = 'newsletter_subscribers';

    protected $fillable = [
        'email',
        'name',
        'token',
        'status',
        'confirmed_at',
        'unsubscribed_at',
        'source',
        'ip_address',
    ];

    protected $casts = [
        'confirmed_at'    => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    const STATUS_PENDING      = 'pending';
    const STATUS_ACTIVE       = 'active';
    const STATUS_UNSUBSCRIBED = 'unsubscribed';

    // ── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeUnsubscribed($query)
    {
        return $query->where('status', self::STATUS_UNSUBSCRIBED);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    public static function generateToken(): string
    {
        do {
            $token = Str::random(48);
        } while (static::where('token', $token)->exists());

        return $token;
    }

    public function confirm(): void
    {
        $this->update([
            'status'       => self::STATUS_ACTIVE,
            'confirmed_at' => now(),
        ]);
    }

    public function unsubscribe(): void
    {
        $this->update([
            'status'           => self::STATUS_UNSUBSCRIBED,
            'unsubscribed_at'  => now(),
        ]);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
}
