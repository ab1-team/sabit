<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminRekening extends Model
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
