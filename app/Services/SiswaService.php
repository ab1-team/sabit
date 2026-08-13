<?php

namespace App\Services;

use App\Models\AnggotaKelas;
use App\Models\Siswa;
use App\Models\Spp;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SiswaService
{
    public function createWithKelasDanSpp(array $data, int $defaultSppNominal = 0): Siswa
    {
        [$kodeKelas, $tingkat] = $this->splitKelas($data['kelas']);

        $data = $this->sanitize($data);
        $tahunAkademik = $this->resolveTahunAkademikNama($data);
        $nisn = trim((string) ($data['nisn'] ?? ''));

        return DB::transaction(function () use ($data, $kodeKelas, $tingkat, $tahunAkademik, $nisn, $defaultSppNominal) {
            if (Siswa::where('nisn', $nisn)->exists()) {
                throw new \RuntimeException('NISN sudah terdaftar.');
            }

            $get = fn(string $k, $default = '-') => array_key_exists($k, $data) && $data[$k] !== null && $data[$k] !== ''
                ? $data[$k]
                : $default;

            $nominalInput = $this->normalizeNominal($get('spp_nominal', 0));
            $nominal = $nominalInput > 0 ? $nominalInput : $defaultSppNominal;

            $siswa = Siswa::create([
            'nipd'                  => $get('nipd', '-'),
            'password'              => $get('password'),
            'nisn'                  => $get('nisn'),
            'nik'                   => $get('nik', '-'),
            'email'                 => $get('email', '-'),
            'tahun_akademik'        => $get('tahun_akademik'),
            'tgl_masuk'             => $get('tanggal_masuk'),
            'ruang'                 => $get('ruangan'),
            'id_user'               => auth()->id(),
            'nama'                  => $get('nama'),
            'tempat_lahir'          => $get('tempat_lahir', '-'),
            'tanggal_lahir'         => $get('tanggal_lahir') ?: null,
            'jenis_kelamin'         => $get('jenis_kelamin'),
            'agama'                 => $get('agama', '-'),
            'kecamatan'             => $get('kecamatan', '-'),
            'kelurahan'             => $get('kelurahan', '-'),
            'dusun'                 => $get('dusun', '-'),
            'rt'                    => $get('rt', '-'),
            'rw'                    => $get('rw', '-'),
            'alamat'                => $get('alamat', '-'),
            'kode_pos'              => $get('kode_pos', '-'),
            'status_awal'           => $get('status_awal'),
            'status_siswa'          => $get('status_siswa'),
            'foto'                  => $get('foto', 'default.png'),
            'kebutuhan_khusus'      => $get('kebutuhan_khusus', '-'),
            'jenis_tinggal'         => $get('jenis_tinggal', 'orang_tua'),
            'alat_transportasi'     => $get('transportasi', '-'),
            'hp'                    => $get('hp') ?? $get('telepon') ?? '-',
            'kode_kelas'            => $kodeKelas,
            'skhun'                 => $get('skhun', '-'),
            'penerima_kps'          => $get('penerima_kps', '-'),
            'no_kps'                => $get('no_kps', '-'),
            'no_kk'                 => $get('no_kk', '-'),
            'tingkat'               => $tingkat,
            'nama_ayah'             => $get('nama_ayah', '-'),
            'tahun_lahir_ayah'      => $get('tahun_lahir_ayah', '-'),
            'pendidikan_ayah'       => $get('pendidikan_ayah', '-'),
            'pekerjaan_ayah'        => $get('pekerjaan_ayah', '-'),
            'penghasilan_ayah'      => $get('penghasilan_ayah', '-'),
            'kebutuhan_khusus_ayah' => $get('kebutuhan_khusus_ayah', '-'),
            'no_telepon_ayah'       => $get('no_telp_ayah', '-'),
            'nama_ibu'              => $get('nama_ibu', '-'),
            'tahun_lahir_ibu'       => $get('tahun_lahir_ibu', '-'),
            'pendidikan_ibu'        => $get('pendidikan_ibu', '-'),
            'pekerjaan_ibu'         => $get('pekerjaan_ibu', '-'),
            'penghasilan_ibu'       => $get('penghasilan_ibu', '-'),
            'kebutuhan_khusus_ibu'  => $get('kebutuhan_khusus_ibu', '-'),
            'no_telepon_ibu'        => $get('no_telp_ibu', '-'),
            'nama_wali'             => $get('nama_wali', '-'),
            'tahun_lahir_wali'      => $get('tahun_lahir_wali', '-'),
            'pendidikan_wali'       => $get('pendidikan_wali', '-'),
            'pekerjaan_wali'        => $get('pekerjaan_wali', '-'),
            'penghasilan_wali'      => $get('penghasilan_wali', '-'),
            'kebutuhan_khusus_wali' => $get('kebutuhan_khusus_wali', '-'),
            'no_telepon_wali'       => $get('no_telp_wali', '-'),
        ]);

            $anggota = $this->attachKelas($siswa, $kodeKelas, $tingkat, $data, $nominal, $tahunAkademik);
            $this->generateSppBulanan($anggota, $nominal, $data);

            return $siswa;
        });
    }

    public function attachKelas(Siswa $siswa, string $kodeKelas, string $tingkat, array $data, int $nominal = 0, ?string $tahunAkademik = null): AnggotaKelas
    {
        return AnggotaKelas::firstOrCreate([
            'id_siswa'       => $siswa->id,
            'tahun_akademik' => $tahunAkademik ?? $this->resolveTahunAkademikNama($data),
            'kode_kelas'     => $kodeKelas,
        ], [
            'tingkat'        => $tingkat,
            'spp_nominal'    => $nominal > 0 ? (string) $nominal : null,
            'tgl_masuk'      => $data['tanggal_masuk'],
            'tgl_keluar'     => Carbon::parse($data['tanggal_masuk'])->addYear()->format('Y-m-d'),
            'status'         => 'aktif',
        ]);
    }

    /**
     * Normalisasi placeholder "-", "0", string kosong → null untuk field nullable.
     * Field wajib sudah tervalidasi, tapi teks "-" yang lolos kita simpan apa adanya.
     */
    public function sanitize(array $data): array
    {
        // Semua teks: "-", "0", string kosong → null. Service lalu isi default '-'.
        $textNullable = [
            'nipd','no_kk','nik','kecamatan','kelurahan','dusun','rt','rw','alamat',
            'kode_pos','kebutuhan_khusus','transportasi','hp','email',
            'skhun','penerima_kps','no_kps',
            'nama_ayah','tahun_lahir_ayah','pendidikan_ayah','pekerjaan_ayah',
            'penghasilan_ayah','kebutuhan_khusus_ayah','no_telp_ayah',
            'nama_ibu','tahun_lahir_ibu','pendidikan_ibu','pekerjaan_ibu',
            'penghasilan_ibu','kebutuhan_khusus_ibu','no_telp_ibu',
            'nama_wali','tahun_lahir_wali','pendidikan_wali','pekerjaan_wali',
            'penghasilan_wali','kebutuhan_khusus_wali','no_telp_wali',
            'tempat_lahir','agama',
            'jenis_tinggal','spp_nominal',
        ];

        foreach ($textNullable as $k) {
            if (!array_key_exists($k, $data)) continue;
            $v = is_string($data[$k]) ? trim($data[$k]) : $data[$k];
            if ($v === '' || $v === '-' || $v === '0') {
                $data[$k] = null;
            } else {
                $data[$k] = $v;
            }
        }

        return $data;
    }

    public function generateSppBulanan(AnggotaKelas $anggota, int $nominal, array $data): void
    {
        $tahunMasuk = Carbon::parse($data['tanggal_masuk'])->year;
        $mulai = Carbon::create($tahunMasuk, 7, 1);
        $akhir = $mulai->copy()->addYear()->subDay();

        $rows = [];
        while ($mulai->lte($akhir)) {
            $rows[] = [
                'anggota_kelas' => $anggota->id,
                'tanggal'       => $mulai->format('Y-m-d'),
                'kode'          => $mulai->format('ym') . $anggota->id_siswa,
                'nominal'       => (string) $nominal,
                'status'        => 'B',
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
            $mulai->addMonth();
        }

        if (empty($rows)) return;

        DB::table('spp')->insertOrIgnore($rows);
    }

    public function splitKelas(string $kelas): array
    {
        return explode('|', $kelas) + [null, null];
    }

    public function normalizeNominal($value): int
    {
        return \App\Utils\Angka::parseInt($value);
    }

    public function resolveTahunAkademikNama(array $data): string
    {
        if (is_numeric($data['tahun_akademik'])) {
            $ta = \App\Models\TahunAkademik::find($data['tahun_akademik']);
            if ($ta) {
                return $ta->nama_tahun;
            }
        }
        return (string) $data['tahun_akademik'];
    }
}
