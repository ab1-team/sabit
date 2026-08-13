<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;
    protected $table = 'siswa';
    protected $guarded = ['id'];

    protected $casts = [
        'tgl_masuk' => 'date',
        'tanggal_lahir' => 'date',
    ];

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'tahun_akademik', 'nama_tahun');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kode_kelas', 'kode_kelas');
    }

    public function ruang()
    {
        return $this->belongsTo(Ruangan::class, 'ruang', 'kode_ruangan');
    }

    public function anggotaKelas()
    {
        return $this->hasMany(AnggotaKelas::class, 'id_siswa', 'id');
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'siswa_id');
    }

    public function spp()
    {
        return $this->hasManyThrough(Spp::class, AnggotaKelas::class, 'id_siswa', 'anggota_kelas', 'id', 'id');
    }

    public function scopeAktif($q)
    {
        return $q->whereExists(fn ($x) => $x->selectRaw(1)->from('anggota_kelas')
            ->whereColumn('anggota_kelas.id_siswa', 'siswa.id')
            ->where('status', 'aktif'));
    }

    public function scopeNonAktif($q)
    {
        return $q->whereExists(fn ($x) => $x->selectRaw(1)->from('anggota_kelas')
            ->whereColumn('anggota_kelas.id_siswa', 'siswa.id')
            ->where('status', 'nonaktif'))
            ->whereNotExists(fn ($x) => $x->selectRaw(1)->from('anggota_kelas')
                ->whereColumn('anggota_kelas.id_siswa', 'siswa.id')
                ->where('status', 'aktif'));
    }

    public function scopeBlokir($q)
    {
        return $q->whereNotExists(fn ($x) => $x->selectRaw(1)->from('anggota_kelas')
            ->whereColumn('anggota_kelas.id_siswa', 'siswa.id'));
    }
}
