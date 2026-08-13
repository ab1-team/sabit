<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;
    protected $table = 'kelas';
    protected $guarded = ['id'];

    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class, 'kode_kurikulum', 'id');
    }

    public function kurikulumByName()
    {
        return $this->belongsTo(Kurikulum::class, 'kode_kurikulum', 'nama_kurikulum');
    }

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'kode_kelas', 'kode_kelas');
    }

    public function anggotaKelas()
    {
        return $this->hasMany(AnggotaKelas::class, 'kode_kelas', 'kode_kelas');
    }
}
