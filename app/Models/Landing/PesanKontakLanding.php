<?php

declare(strict_types=1);

namespace App\Models\Landing;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PesanKontakLanding extends Model
{
    protected $table = 'lp_pesan_kontak';

    /**
     * Status workflow pesan kontak.
     */
    public const STATUS_BARU     = 'baru';
    public const STATUS_DIBACA   = 'dibaca';
    public const STATUS_SELESAI  = 'selesai';

    public const STATUSES = [
        self::STATUS_BARU,
        self::STATUS_DIBACA,
        self::STATUS_SELESAI,
    ];

    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'is_read',
        'ip_address',
        'status',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Label status yang ditampilkan ke admin.
     */
    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_DIBACA  => 'Dibaca',
            self::STATUS_SELESAI => 'Selesai',
            default              => 'Baru',
        };
    }

    /**
     * CSS class untuk badge status.
     */
    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_DIBACA  => 'lp-status-badge is-read',
            self::STATUS_SELESAI => 'lp-status-badge is-finished',
            default              => 'lp-status-badge is-new',
        };
    }
}
