<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spp extends Model
{
    use HasFactory;
    protected $table = 'spp';
    protected $guarded = ['id'];

    protected $casts = [
        'tanggal' => 'date',
        'tgl_lunas' => 'date',
        'nominal' => 'integer',
    ];

    public function markLunas(string $tglBayar): void
    {
        $this->forceFill(['status' => 'L', 'tgl_lunas' => $tglBayar])->save();
    }

    public function batalLunas(): void
    {
        $this->forceFill(['status' => 'B', 'tgl_lunas' => null])->save();
    }

    public static function bulanLunasBySiswa(int $idSiswa): int
    {
        return self::query()
            ->whereHas('anggotaKelas', function ($q) use ($idSiswa) {
                $q->where('id_siswa', $idSiswa);
            })
            ->where('status', 'L')
            ->count();
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'kode_spp', 'kode');
    }

    public function anggotaKelas()
    {
        return $this->belongsTo(AnggotaKelas::class, 'anggota_kelas', 'id');
    }

    public function getAnggotaKelas()
    {
        return $this->anggotaKelas();
    }
}
