<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AkunLevel3 extends Model
{
    use HasFactory;
    protected $table = 'akun_level3';
    protected $guarded = [];

  public function rek()
{
    // Relasi rekening sub-akun di bawah L3 ini. parent_id di rekening
    // menyimpan id akun_level3 (=1), bukan id rekening header.
    // Override ini me-load rekening berdasarkan prefix kode_akun.
    return $this->hasMany(Rekening::class, 'parent_id', 'id')
        ->whereRaw('1=0'); // never used; replaced by rekeningByPrefix() eager-load
}

/**
 * Daftar rekening sub-akun di bawah L3 ini berdasarkan prefix kode_akun.
 * Dipakai di Keuangan::hitungSaldo() dan view laporan.
 */
public function rekeningByPrefix()
{
    $prefix = $this->kode_akun;
    $groupPrefix = preg_replace('/\.00$/', '.', $prefix);
    return Rekening::query()
        ->where(function ($q) use ($prefix, $groupPrefix) {
            $q->where('kode_akun', $prefix)
              ->orWhere('kode_akun', 'like', $groupPrefix . '%');
        })
        ->orderBy('kode_akun', 'ASC')
        ->get();
}

}
