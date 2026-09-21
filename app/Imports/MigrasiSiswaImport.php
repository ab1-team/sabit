<?php

namespace App\Imports;

use App\Models\AnggotaKelas;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Ruangan;
use App\Models\Siswa;
use App\Models\Spp;
use App\Services\SiswaService;
use App\Utils\Angka;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class MigrasiSiswaImport implements
    ToCollection,
    WithHeadingRow,
    WithChunkReading,
    WithEvents,
    SkipsOnError
{
    use Importable, SkipsErrors;

    private int $inserted = 0;
    private int $updated = 0;
    private int $failed = 0;
    private array $failures = [];

    public function __construct(
        protected int $tahunAkademikId,
        protected string $statusDefault = 'aktif',
        ?string $tanggalMasukDefault = null,
        protected ?int $userId = null,
        protected ?SiswaService $service = null,
    ) {
        $this->tanggalMasukDefault = $tanggalMasukDefault ?: now()->format('Y-m-d');
        $this->service = $service ?? app(SiswaService::class);
    }

    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            return;
        }

        $tahunAkademik = DB::table('tahun_akademik')->where('id', $this->tahunAkademikId)->first();
        if (!$tahunAkademik) {
            throw new \RuntimeException('Tahun akademik tidak ditemukan.');
        }
        $namaTahun = $tahunAkademik->nama_tahun;

        $kodeKelasList = $rows->pluck('kode_kelas')
            ->map(fn ($v) => trim((string) $v))
            ->filter()
            ->unique()
            ->all();

        $kelasMap = Kelas::whereIn('kode_kelas', $kodeKelasList)->get()->keyBy('kode_kelas');

        $nisnList = $rows->pluck('nisn')
            ->map(fn ($v) => trim((string) $v))
            ->filter()
            ->unique()
            ->all();

        $existingMap = Siswa::whereIn('nisn', $nisnList)->get()->keyBy('nisn');

        foreach ($rows as $rowIndex => $row) {
            $rowNum = $rowIndex + 2;
            $row = $this->normalize($row->toArray());

            try {
                DB::transaction(function () use ($row, $rowNum, $namaTahun, $kelasMap, $existingMap) {
                    $this->processRow($row, $rowNum, $namaTahun, $kelasMap, $existingMap);
                });
            } catch (\Throwable $e) {
                $this->failed++;
                $this->failures[] = [
                    'row' => $rowNum,
                    'nama' => $row['nama'] ?? '-',
                    'errors' => [$e->getMessage()],
                ];
                Log::warning('MigrasiSiswa row failed', [
                    'row' => $rowNum,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    private function processRow(array $row, int $rowNum, string $namaTahun, $kelasMap, $existingMap): void
    {
        $required = ['nik', 'nama', 'jenis_kelamin', 'nipd', 'nisn', 'no_kk', 'tanggal_lahir', 'kode_kelas', 'kode_ruangan'];
        foreach ($required as $f) {
            if (empty($row[$f])) {
                throw new \RuntimeException("Kolom '{$f}' wajib diisi.");
            }
        }

        $jk = strtoupper(trim((string) $row['jenis_kelamin']));
        if (!in_array($jk, ['L', 'P'])) {
            throw new \RuntimeException("Jenis kelamin harus 'L' atau 'P'.");
        }

        $tanggalLahir = $this->parseDate($row['tanggal_lahir']);
        if (!$tanggalLahir) {
            throw new \RuntimeException('Format tanggal_lahir tidak valid (gunakan YYYY-MM-DD).');
        }

        $kodeKelas = trim((string) $row['kode_kelas']);
        $kelas = $kelasMap[$kodeKelas] ?? null;
        if (!$kelas) {
            throw new \RuntimeException("Kode kelas '{$kodeKelas}' tidak ditemukan di tabel kelas.");
        }
        $tingkat = $kelas->tingkat ?? null;

        $kodeRuangan = trim((string) $row['kode_ruangan']);
        if (!Ruangan::where('kode_ruangan', $kodeRuangan)->exists()) {
            throw new \RuntimeException("Kode ruangan '{$kodeRuangan}' tidak ditemukan di tabel ruangan.");
        }

        $kodeJurusan = trim((string) ($row['kode_jurusan'] ?? ''));
        if ($kodeJurusan !== '' && !Jurusan::where('kode_jurusan', $kodeJurusan)->exists()) {
            throw new \RuntimeException("Kode jurusan '{$kodeJurusan}' tidak ditemukan di tabel jurusan.");
        }
        if ($kodeJurusan === '') {
            $kodeJurusan = null;
        }

        $statusAwal = strtolower(trim((string) ($row['status_awal'] ?? 'baru')));
        if (!in_array($statusAwal, ['baru', 'pindahan'], true)) {
            throw new \RuntimeException("status_awal harus 'baru' atau 'pindahan'.");
        }

        $tglMasuk = $this->parseDate($row['tgl_masuk'] ?? null);
        if (!$tglMasuk) {
            $tglMasuk = $this->tanggalMasukDefault;
        }

        $sppInput = Angka::parseInt($row['spp_nominal'] ?? 0);
        $sppNominal = $sppInput > 0 ? $sppInput : $this->service->resolveDefaultSppNominal($namaTahun);

        $nisn = trim((string) $row['nisn']);
        $existing = $existingMap[$nisn] ?? null;

        $password = trim((string) ($row['password'] ?? ''));
        if ($password === '') {
            $password = $row['nipd'];
        }
        $hashedPassword = Hash::needsRehash($password) ? Hash::make($password) : $password;

        $statusSiswa = in_array($this->statusDefault, ['aktif', 'nonaktif', 'blokir'])
            ? $this->statusDefault
            : 'aktif';

        $payload = [
            'nik' => trim((string) $row['nik']),
            'nama' => trim((string) $row['nama']),
            'jenis_kelamin' => $jk,
            'nipd' => trim((string) $row['nipd']),
            'nisn' => $nisn,
            'no_kk' => trim((string) $row['no_kk']),
            'tempat_lahir' => $this->valOrDash($row, 'tempat_lahir'),
            'tanggal_lahir' => $tanggalLahir,
            'agama' => $this->valOrDash($row, 'agama'),
            'password' => $hashedPassword,
            'alamat' => $this->valOrDash($row, 'alamat'),
            'rt' => $this->valOrDash($row, 'rt'),
            'rw' => $this->valOrDash($row, 'rw'),
            'dusun' => $this->valOrDash($row, 'dusun'),
            'kelurahan' => $this->valOrDash($row, 'kelurahan'),
            'kecamatan' => $this->valOrDash($row, 'kecamatan'),
            'kode_pos' => $this->valOrDash($row, 'kode_pos'),
            'kebutuhan_khusus' => $this->valOrDash($row, 'kebutuhan_khusus'),
            'jenis_tinggal' => $this->normalizeJenisTinggal($row['jenis_tinggal'] ?? 'orang_tua'),
            'alat_transportasi' => $this->valOrDash($row, 'alat_transportasi'),
            'hp' => $this->valOrDash($row, 'hp'),
            'email' => $this->valOrDash($row, 'email'),
            'tahun_akademik' => $namaTahun,
            'status_awal' => $statusAwal,
            'status_siswa' => $statusSiswa,
            'kode_kelas' => $kodeKelas,
            'kode_jurusan' => $kodeJurusan,
            'ruang' => $kodeRuangan,
            'tingkat' => $tingkat,
            'nama_ayah' => $this->valOrDash($row, 'nama_ayah'),
            'tahun_lahir_ayah' => $this->valOrDash($row, 'tahun_lahir_ayah'),
            'pendidikan_ayah' => $this->valOrDash($row, 'pendidikan_ayah'),
            'pekerjaan_ayah' => $this->valOrDash($row, 'pekerjaan_ayah'),
            'penghasilan_ayah' => $this->valOrDash($row, 'penghasilan_ayah'),
            'kebutuhan_khusus_ayah' => $this->valOrDash($row, 'kebutuhan_khusus_ayah'),
            'no_telepon_ayah' => $this->valOrDash($row, 'no_telepon_ayah'),
            'nama_ibu' => $this->valOrDash($row, 'nama_ibu'),
            'tahun_lahir_ibu' => $this->valOrDash($row, 'tahun_lahir_ibu'),
            'pendidikan_ibu' => $this->valOrDash($row, 'pendidikan_ibu'),
            'pekerjaan_ibu' => $this->valOrDash($row, 'pekerjaan_ibu'),
            'penghasilan_ibu' => $this->valOrDash($row, 'penghasilan_ibu'),
            'kebutuhan_khusus_ibu' => $this->valOrDash($row, 'kebutuhan_khusus_ibu'),
            'no_telepon_ibu' => $this->valOrDash($row, 'no_telepon_ibu'),
            'nama_wali' => $this->valOrDash($row, 'nama_wali'),
            'tahun_lahir_wali' => $this->valOrDash($row, 'tahun_lahir_wali'),
            'pendidikan_wali' => $this->valOrDash($row, 'pendidikan_wali'),
            'pekerjaan_wali' => $this->valOrDash($row, 'pekerjaan_wali'),
            'penghasilan_wali' => $this->valOrDash($row, 'penghasilan_wali'),
            'kebutuhan_khusus_wali' => $this->valOrDash($row, 'kebutuhan_khusus_wali'),
            'no_telepon_wali' => $this->valOrDash($row, 'no_telepon_wali'),
            'skhun' => '-',
            'penerima_kps' => '-',
            'no_kps' => '-',
            'foto' => 'default.png',
            'tgl_masuk' => $tglMasuk,
            'id_user' => $this->userId ?? auth()->id() ?? 0,
        ];

        if ($existing) {
            $existing->fill($payload);
            $existing->save();
            $siswa = $existing;
            $this->updated++;
        } else {
            $siswa = Siswa::create($payload);
            $this->inserted++;
        }

        $anggota = AnggotaKelas::firstOrCreate([
            'id_siswa' => $siswa->id,
            'tahun_akademik' => $namaTahun,
            'kode_kelas' => $kodeKelas,
        ], [
            'tingkat' => $tingkat,
            'spp_nominal' => $sppNominal > 0 ? (string) $sppNominal : null,
            'tgl_masuk' => $tglMasuk,
            'tgl_keluar' => Carbon::parse($tglMasuk)->addYear()->format('Y-m-d'),
            'status' => 'aktif',
        ]);

        $this->generateSppBulanan($anggota, $sppNominal, $tglMasuk);
    }

    private function generateSppBulanan(AnggotaKelas $anggota, int $nominal, ?string $tglMasuk = null): void
    {
        $tglMasuk = $tglMasuk ?: ($anggota->tgl_masuk instanceof \DateTimeInterface ? $anggota->tgl_masuk->format('Y-m-d') : (string) $anggota->tgl_masuk);
        $tahunMasuk = Carbon::parse($tglMasuk)->year;
        $mulai = Carbon::create($tahunMasuk, 7, 1);
        $akhir = $mulai->copy()->addYear()->subDay();

        $existingTanggal = Spp::where('anggota_kelas', $anggota->id)
            ->whereBetween('tanggal', [$mulai->format('Y-m-d'), $akhir->format('Y-m-d')])
            ->pluck('tanggal')
            ->map(fn ($d) => substr((string) $d, 0, 10))
            ->flip()
            ->all();

        $rows = [];
        $nominalStr = (string) $nominal;
        while ($mulai->lte($akhir)) {
            $tgl = $mulai->format('Y-m-d');
            if (!isset($existingTanggal[$tgl])) {
                $rows[] = [
                    'anggota_kelas' => $anggota->id,
                    'tanggal' => $tgl,
                    'kode' => $mulai->format('ym') . $anggota->id_siswa,
                    'nominal' => $nominalStr,
                    'status' => 'B',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            $mulai->addMonth();
        }

        if (!empty($rows)) {
            DB::table('spp')->insertOrIgnore($rows);
        }
    }

    private function normalize(array $row): array
    {
        $out = [];
        foreach ($row as $k => $v) {
            $key = Str::snake(strtolower(trim((string) $k)));
            $key = preg_replace('/\s+/', '_', $key);
            $out[$key] = is_string($v) ? trim($v) : $v;
        }
        return $out;
    }

    private function valOrDash(array $row, string $key, string $default = '-'): string
    {
        $v = $row[$key] ?? null;
        if ($v === null || $v === '' || $v === '-') {
            return $default;
        }
        return (string) $v;
    }

    private function normalizeJenisTinggal($v): string
    {
        $allowed = ['orang_tua', 'asrama', 'kost', 'wali'];
        $v = strtolower(trim((string) $v));
        return in_array($v, $allowed, true) ? $v : 'orang_tua';
    }

    private function parseDate($v): ?string
    {
        if (empty($v)) {
            return null;
        }
        if ($v instanceof \DateTimeInterface) {
            return Carbon::instance($v)->format('Y-m-d');
        }
        $v = trim((string) $v);
        foreach (['Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y'] as $fmt) {
            try {
                return Carbon::createFromFormat($fmt, $v)->format('Y-m-d');
            } catch (\Throwable $e) {
            }
        }
        try {
            return Carbon::parse($v)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function registerEvents(): array
    {
        return [
            AfterImport::class => function (AfterImport $event) {
                Log::info('MigrasiSiswa selesai', [
                    'inserted' => $this->inserted,
                    'updated' => $this->updated,
                    'failed' => $this->failed,
                ]);
            },
        ];
    }

    public function getInserted(): int { return $this->inserted; }
    public function getUpdated(): int { return $this->updated; }
    public function getFailed(): int { return $this->failed; }
    public function getFailures(): array { return $this->failures; }
}
