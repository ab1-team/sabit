<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        return (int) DB::table('spp')
            ->join('anggota_kelas', 'anggota_kelas.id', '=', 'spp.anggota_kelas')
            ->where('anggota_kelas.id_siswa', $idSiswa)
            ->where('spp.status', 'L')
            ->count();
    }

    public static function bulanLunasBySiswaBulk(array $idsSiswa): array
    {
        if (empty($idsSiswa)) return [];
        $rows = DB::table('spp')
            ->join('anggota_kelas', 'anggota_kelas.id', '=', 'spp.anggota_kelas')
            ->whereIn('anggota_kelas.id_siswa', $idsSiswa)
            ->where('spp.status', 'L')
            ->groupBy('anggota_kelas.id_siswa')
            ->selectRaw('anggota_kelas.id_siswa, COUNT(*) as total')
            ->pluck('total', 'id_siswa')
            ->all();
        return array_map('intval', $rows);
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'kode_spp', 'kode');
    }

    public function anggotaKelas()
    {
        return $this->belongsTo(AnggotaKelas::class, 'anggota_kelas', 'id');
    }
}
