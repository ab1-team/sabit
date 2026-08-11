<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class TenantInvoice extends Model
{
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
}

