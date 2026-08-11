<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantTransaksi extends Model
{
    use HasFactory;

    protected $connection = 'central';

    protected $table = 'admin_transaksi';
    protected $guarded = ['idt'];

    protected $primaryKey = 'idt';
    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'tgl_transaksi',
        'rekening_debit',
        'rekening_kredit',
        'idv',
        'keterangan_transaksi',
        'jumlah',
        'urutan',
        'id_user',
    ];

    protected $casts = [
        'tgl_transaksi' => 'date',
        'jumlah'       => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(TenantInvoice::class, 'idv');
    }

    public function user()
    {
        return $this->belongsTo(TenantAdminUser::class, 'id_user');
    }

    public function rekeningDebit()
    {
        return $this->belongsTo(TenantRekening::class, 'rekening_debit', 'kd_rekening');
    }

    public function rekeningKredit()
    {
        return $this->belongsTo(TenantRekening::class, 'rekening_kredit', 'kd_rekening');
    }
}

