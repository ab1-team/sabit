<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('siswa')) {
            Schema::create('siswa', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('nik', 16);
                $table->string('nama');
                $table->enum('jenis_kelamin', ['L', 'P'])->comment('L/P');
                $table->string('nipd')->comment('Bisa diisi NIS');
                $table->string('nisn');
                $table->string('no_kk', 16);
                $table->string('tempat_lahir');
                $table->date('tanggal_lahir');
                $table->string('agama');
                $table->string('password');
                $table->string('alamat');
                $table->string('rt');
                $table->string('rw');
                $table->string('dusun');
                $table->string('kelurahan');
                $table->string('kecamatan');
                $table->string('kode_pos');
                $table->string('kebutuhan_khusus')->comment('Ya/Tidak');
                $table->enum('jenis_tinggal', ['orang_tua', 'asrama', 'kost', 'wali'])->default('orang_tua');
                $table->string('alat_transportasi');
                $table->string('hp');
                $table->string('email');
                $table->string('skhun');
                $table->string('penerima_kps');
                $table->string('no_kps');
                $table->string('foto')->nullable();
                $table->date('tgl_masuk')->nullable();
                $table->string('tahun_akademik');
                $table->enum('status_awal', ['baru', 'pindahan'])->default('baru')->comment('Baru / Pindahan');
                $table->enum('status_siswa', ['aktif', 'nonaktif', 'blokir'])->default('aktif');
                $table->string('kode_kelas')->comment('Diterima di kelas ...');
                $table->string('kode_jurusan')->nullable();
                $table->string('ruang');
                $table->string('tingkat')->nullable();
                $table->string('nama_ayah');
                $table->string('tahun_lahir_ayah');
                $table->string('pendidikan_ayah');
                $table->string('pekerjaan_ayah');
                $table->string('penghasilan_ayah');
                $table->string('kebutuhan_khusus_ayah');
                $table->string('no_telepon_ayah');
                $table->string('nama_ibu');
                $table->string('tahun_lahir_ibu');
                $table->string('pendidikan_ibu');
                $table->string('pekerjaan_ibu');
                $table->string('penghasilan_ibu');
                $table->string('kebutuhan_khusus_ibu');
                $table->string('no_telepon_ibu');
                $table->string('nama_wali');
                $table->string('tahun_lahir_wali');
                $table->string('pendidikan_wali');
                $table->string('pekerjaan_wali');
                $table->string('penghasilan_wali');
                $table->string('kebutuhan_khusus_wali');
                $table->string('no_telepon_wali');
                $table->string('id_user');
                $table->timestamps();
            });

            DB::statement('ALTER TABLE `siswa` AUTO_INCREMENT = 1565');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};
