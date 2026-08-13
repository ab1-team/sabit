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

        $created = 0;
        $found = 0;
        $firstBatch = true;

        Siswa::whereDoesntHave('anggotaKelas')
            ->select(['id', 'nama', 'tgl_masuk', 'tahun_akademik', 'tingkat', 'kode_kelas', 'status_siswa'])
            ->chunkById(500, function ($siswa) use ($dry, &$created, &$found, &$firstBatch) {
                if ($firstBatch) {
                    $this->info("Memproses siswa tanpa anggota_kelas dalam chunk...");
                    $firstBatch = false;
                }
                foreach ($siswa as $s) {
                    $found++;
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
            });

        if ($found === 0) {
            $this->info('Semua siswa sudah punya anggota_kelas.');
        } else {
            $this->info($dry ? 'Dry-run selesai.' : "Selesai. {$created} anggota_kelas dibuat dari {$found} siswa.");
        }
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
