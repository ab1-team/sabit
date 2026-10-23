<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class TenantInvoice extends Model
{
    public const STATUS_PAID   = 'paid';
    public const STATUS_UNPAID = 'unpaid';

    /**
     * Katalog jenis pembayaran invoice yang berlaku di konsol pusat.
     * Sumber tunggal untuk validasi controller maupun form.
     */
    public const JENIS_PEMBAYARAN = [
        'Biaya Lisensi Instalasi',
        'Biaya Perpanjangan Maintenance dan Server',
        'Biaya Bimbingan Teknis',
        'Biaya Migrasi Ulang',
        'Biaya Aktivasi WA Gateway',
    ];

    protected $connection = 'central';

    protected $table = 'admin_invoice';

    protected $fillable = [
        'tenant_id',
        'jenis_pembayaran',
        'tgl_invoice',
        'tgl_lunas',
        'status',
        'jumlah',
        'user_id',
    ];

    protected $casts = [
        'tgl_invoice' => 'date',
        'tgl_lunas'   => 'date',
        'jumlah'      => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(TenantAdminUser::class, 'user_id');
    }

    public function hasTransaksi()
    {
        return $this->hasOne(TenantTransaksi::class, 'idv');
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * Invoice yang belum dibayar dan sudah melewati tanggal jatuh tempo.
     */
    public function isOverdue(): bool
    {
        return ! $this->isPaid()
            && $this->tgl_lunas !== null
            && $this->tgl_lunas->lt(today());
    }

    /**
     * Sisa hari menuju jatuh tempo (negatif = sudah lewat).
     */
    public function daysToDue(): ?int
    {
        if ($this->tgl_lunas === null) {
            return null;
        }

        return (int) today()->diffInDays($this->tgl_lunas, false);
    }
}
