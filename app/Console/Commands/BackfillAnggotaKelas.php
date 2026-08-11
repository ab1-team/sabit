<?php

namespace App\Console\Commands;

use App\Models\AnggotaKelas;
use App\Models\Siswa;
use App\Models\TahunAkademik;
use Carbon\Carbon;
use Illuminate\Console\Command;

class BackfillAnggotaKelas extends Command
{
    protected $signature = 'siswa:backfill-anggota-kelas {--dry-run : Tampilkan tanpa menyimpan}';

    protected $description = 'Buat baris anggota_kelas untuk siswa yang belum punya';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');

        $siswa = Siswa::whereDoesntHave('anggotaKelas')->get();

        if ($siswa->isEmpty()) {
            $this->info('Semua siswa sudah punya anggota_kelas.');
            return self::SUCCESS;
        }

        $this->info("Ditemukan {$siswa->count()} siswa tanpa anggota_kelas.");
        $created = 0;

        foreach ($siswa as $s) {
            $tglMasuk = $s->tgl_masuk
                ? Carbon::parse($s->tgl_masuk)
                : Carbon::now();

            $row = [
                'id_siswa'       => $s->id,
                'tahun_akademik' => $this->resolveTahunAkademik($s->tahun_akademik),
                'tingkat'        => (string) ($s->tingkat ?? ''),
                'kode_kelas'     => (string) ($s->kode_kelas ?? ''),
                'tgl_masuk'      => $tglMasuk->format('Y-m-d'),
                'tgl_keluar'     => $tglMasuk->copy()->addYear()->format('Y-m-d'),
                'status'         => ($s->status_siswa ?? 'aktif') === 'aktif' ? 'aktif' : 'nonaktif',
            ];

            if ($dry) {
                $this->line("[dry] siswa#{$s->id} {$s->nama} -> " . json_encode($row));
                continue;
            }

            AnggotaKelas::create($row);
            $created++;
        }

        $this->info($dry ? 'Dry-run selesai.' : "Selesai. {$created} anggota_kelas dibuat.");
        return self::SUCCESS;
    }

    private function resolveTahunAkademik($value): string
    {
        if (is_numeric($value)) {
            $ta = TahunAkademik::find($value);
            if ($ta) {
                return $ta->nama_tahun;
            }
        }
        return (string) $value;
    }
}
