<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminInvoice extends Model
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
        return $this->belongsTo(AdminUser::class, 'user_id');
    }

    public function hasTransaksi()
    {
        return $this->hasOne(AdminTransaksi::class, 'idv');
    }
}
