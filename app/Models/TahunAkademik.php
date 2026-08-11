<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TahunAkademik extends Model
{
    use HasFactory;
    protected $table = 'tahun_akademik';
    protected $guarded = ['id'];

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'tahun_akademik', 'nama_tahun');
    }

    public function anggotaKelas()
    {
        return $this->hasMany(AnggotaKelas::class, 'tahun_akademik', 'nama_tahun');
    }

    public function aktifkan(): void
    {
        DB::table('tahun_akademik')
            ->where('id', '!=', $this->id)
            ->update(['status' => 'nonaktif']);
        DB::table('tahun_akademik')
            ->where('id', $this->id)
            ->update(['status' => 'aktif']);
        $this->refresh();
    }
}
