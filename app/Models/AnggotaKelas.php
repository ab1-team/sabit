<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnggotaKelas extends Model
{
    use HasFactory;
    protected $table = 'anggota_kelas';
    protected $guarded = ['id'];

    protected $casts = [
        'tgl_masuk' => 'date:Y-m-d',
        'tgl_keluar' => 'date:Y-m-d',
    ];

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'tahun_akademik', 'nama_tahun');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kode_kelas', 'kode_kelas');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id');
    }

    public function spp()
    {
        return $this->hasMany(Spp::class, 'anggota_kelas', 'id');
    }
}
