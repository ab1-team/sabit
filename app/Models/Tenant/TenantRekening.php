<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantRekening extends Model
{
    use HasFactory;

    protected $connection = 'central';

    protected $table = 'admin_rekening';
    protected $guarded = ['id'];

    protected $fillable = [
        'tenant_id',
        'kd_rekening',
        'nama_rekening',
        'pasangan',
    ];

    protected $casts = [
        'pasangan' => 'decimal:2',
    ];
}

