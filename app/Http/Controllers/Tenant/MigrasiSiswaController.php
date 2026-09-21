<?php

namespace App\Http\Controllers\Tenant;

use App\Exports\MigrasiSiswaTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\MigrasiSiswaImport;
use App\Imports\KodeKelasOnlyImport;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Kurikulum;
use App\Models\Ruangan;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel as ExcelType;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\Failure;

class MigrasiSiswaController extends Controller
{
    public function index(Request $request)
    {
        $tid = $this->currentTenantId($request);

        if (!$tid) {
            return view('tenant.migrasi-siswa', [
                'tahunAkademiks' => collect(),
                'jurusan' => collect(),
                'kelas' => collect(),
                'selectedTahun' => null,
                'selectedJurusan' => null,
                'tenants' => $request->attributes->get('tenants', []),
                'currentTenantId' => null,
                'currentTenant' => null,
                'needsTenant' => true,
            ]);
        }

        $tenancy = app(\Stancl\Tenancy\Tenancy::class);
        $tenantModel = Tenant::find($tid);
        if ($tenantModel) {
            $tenancy->initialize($tenantModel);
        }

        try {
            $tahunAkademiks = DB::table('tahun_akademik')
                ->orderByDesc('status')
                ->orderByDesc('id')
                ->get();

            $jurusan = DB::table('jurusan')
                ->orderBy('nama')
                ->get(['id', 'nama', 'kode_jurusan']);

            $selectedTahun = $request->query('tahun_akademik_id');
            $selectedJurusan = $request->query('jurusan_id');

            $kelas = DB::table('kelas')
                ->orderBy('nama_kelas')
                ->get(['id', 'kode_kelas', 'nama_kelas', 'tingkat']);
        } finally {
            $tenancy->end();
        }

        return view('tenant.migrasi-siswa', [
            'tahunAkademiks' => $tahunAkademiks,
            'jurusan' => $jurusan,
            'kelas' => $kelas,
            'selectedTahun' => $selectedTahun,
            'selectedJurusan' => $selectedJurusan,
            'tenants' => $request->attributes->get('tenants', []),
            'currentTenantId' => $tid,
            'currentTenant' => $request->attributes->get('current_tenant'),
            'needsTenant' => false,
        ]);
    }

    public function template(Request $request)
    {
        $filename = 'template-migrasi-siswa-' . date('Ymd_His') . '.xlsx';

        return Excel::download(new MigrasiSiswaTemplateExport, $filename, ExcelType::XLSX);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'tahun_akademik_id' => 'required|integer',
            'status' => 'nullable|in:aktif,nonaktif,blokir',
            'new_kelas' => 'sometimes|array',
            'new_kelas.*.kode_kelas' => 'required_with:new_kelas|string|max:50',
            'new_kelas.*.nama_kelas' => 'required_with:new_kelas|string|max:120',
            'new_kelas.*.tingkat' => 'required_with:new_kelas|string|max:20',
            'new_kelas.*.kode_kurikulum' => 'required_with:new_kelas',
            'new_ruangan' => 'sometimes|array',
            'new_ruangan.*.kode_ruangan' => 'required_with:new_ruangan|string|max:50',
            'new_ruangan.*.nama_ruangan' => 'required_with:new_ruangan|string|max:120',
            'new_ruangan.*.kode_gedung' => 'nullable|string|max:50',
            'new_jurusan' => 'sometimes|array',
            'new_jurusan.*.kode_jurusan' => 'required_with:new_jurusan|string|max:50',
            'new_jurusan.*.nama' => 'required_with:new_jurusan|string|max:120',
        ], [
            'file.required' => 'File Excel wajib diunggah.',
            'file.mimes' => 'Format file harus .xlsx, .xls, atau .csv.',
            'file.max' => 'Ukuran file maksimal 10 MB.',
            'tahun_akademik_id.required' => 'Tahun akademik wajib dipilih.',
        ]);

        $tid = $this->currentTenantId($request);
        if (!$tid) {
            return response()->json([
                'ok' => false,
                'message' => 'Sekolah belum dipilih. Pilih sekolah terlebih dahulu.',
            ], 422);
        }

        $tenancy = app(\Stancl\Tenancy\Tenancy::class);
        $tenantModel = Tenant::find($tid);
        if (!$tenantModel) {
            return response()->json([
                'ok' => false,
                'message' => 'Tenant tidak ditemukan.',
            ], 404);
        }

        try {
            $tenancy->initialize($tenantModel);

            try {
                $newKelas = $request->input('new_kelas', []);
                if (!empty($newKelas)) {
                    $existingCodes = Kelas::whereIn('kode_kelas', array_column($newKelas, 'kode_kelas'))
                        ->pluck('kode_kelas')
                        ->all();
                    foreach ($newKelas as $row) {
                        if (in_array($row['kode_kelas'], $existingCodes, true)) {
                            continue;
                        }
                        try {
                            Kelas::create([
                                'kode_kelas' => $row['kode_kelas'],
                                'nama_kelas' => $row['nama_kelas'],
                                'tingkat' => $row['tingkat'],
                                'kode_kurikulum' => $row['kode_kurikulum'],
                            ]);
                        } catch (\Throwable $e) {
                            return response()->json([
                                'ok' => false,
                                'message' => "Gagal membuat kelas '{$row['kode_kelas']}': " . $e->getMessage(),
                            ], 422);
                        }
                    }
                }

                $newRuangan = $request->input('new_ruangan', []);
                if (!empty($newRuangan)) {
                    $existingCodes = Ruangan::whereIn('kode_ruangan', array_column($newRuangan, 'kode_ruangan'))
                        ->pluck('kode_ruangan')
                        ->all();
                    foreach ($newRuangan as $row) {
                        if (in_array($row['kode_ruangan'], $existingCodes, true)) {
                            continue;
                        }
                        try {
                            Ruangan::create([
                                'kode_gedung' => $row['kode_gedung'] ?? '-',
                                'kode_ruangan' => $row['kode_ruangan'],
                                'nama_ruangan' => $row['nama_ruangan'],
                                'kapasitas_belajar' => '-',
                                'kapasitas_ujian' => '-',
                                'keterangan' => '-',
                                'status' => 'aktif',
                            ]);
                        } catch (\Throwable $e) {
                            return response()->json([
                                'ok' => false,
                                'message' => "Gagal membuat ruangan '{$row['kode_ruangan']}': " . $e->getMessage(),
                            ], 422);
                        }
                    }
                }

                $newJurusan = $request->input('new_jurusan', []);
                if (!empty($newJurusan)) {
                    $existingCodes = Jurusan::whereIn('kode_jurusan', array_column($newJurusan, 'kode_jurusan'))
                        ->pluck('kode_jurusan')
                        ->all();
                    foreach ($newJurusan as $row) {
                        if (in_array($row['kode_jurusan'], $existingCodes, true)) {
                            continue;
                        }
                        try {
                            Jurusan::create([
                                'kode_jurusan' => $row['kode_jurusan'],
                                'nama' => $row['nama'],
                                'status' => 'aktif',
                            ]);
                        } catch (\Throwable $e) {
                            return response()->json([
                                'ok' => false,
                                'message' => "Gagal membuat jurusan '{$row['kode_jurusan']}': " . $e->getMessage(),
                            ], 422);
                        }
                    }
                }

                $import = new MigrasiSiswaImport(
                    tahunAkademikId: (int) $request->input('tahun_akademik_id'),
                    statusDefault: (string) ($request->input('status') ?? 'aktif'),
                    tanggalMasukDefault: now()->format('Y-m-d'),
                    userId: auth()->id(),
                );

                Excel::import($import, $request->file('file'));

                $inserted = $import->getInserted();
                $updated = $import->getUpdated();
                $failed = $import->getFailed();
                $failures = $import->getFailures();

                $message = "Import selesai. {$inserted} siswa baru, {$updated} diperbarui";
                if ($failed > 0) {
                    $message .= ", {$failed} gagal.";
                } else {
                    $message .= ".";
                }

                return response()->json([
                    'ok' => true,
                    'message' => $message,
                    'summary' => [
                        'inserted' => $inserted,
                        'updated' => $updated,
                        'failed' => $failed,
                    ],
                    'failures' => $failures,
                ]);
            } finally {
                $tenancy->end();
            }
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Gagal memproses file: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function preview(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ], [
            'file.required' => 'File Excel wajib diunggah.',
            'file.mimes' => 'Format file harus .xlsx, .xls, atau .csv.',
            'file.max' => 'Ukuran file maksimal 10 MB.',
        ]);

        $tid = $this->currentTenantId($request);
        if (!$tid) {
            return response()->json([
                'ok' => false,
                'message' => 'Sekolah belum dipilih. Pilih sekolah terlebih dahulu.',
            ], 422);
        }

        $tenancy = app(\Stancl\Tenancy\Tenancy::class);
        $tenantModel = Tenant::find($tid);
        if (!$tenantModel) {
            return response()->json([
                'ok' => false,
                'message' => 'Tenant tidak ditemukan.',
            ], 404);
        }

try {
                $tenancy->initialize($tenantModel);

                try {
                    $rows = Excel::toArray(new KodeKelasOnlyImport, $request->file('file'));
                    $allRows = collect($rows[0] ?? []);

                    $kodeKelasList = $allRows
                        ->map(fn ($r) => trim((string) ($r['kode_kelas'] ?? '')))
                        ->filter()
                        ->unique()
                        ->values()
                        ->all();

                    $kodeRuanganList = $allRows
                        ->map(fn ($r) => trim((string) ($r['kode_ruangan'] ?? '')))
                        ->filter()
                        ->unique()
                        ->values()
                        ->all();

                    $kodeJurusanList = $allRows
                        ->map(fn ($r) => trim((string) ($r['kode_jurusan'] ?? '')))
                        ->filter()
                        ->unique()
                        ->values()
                        ->all();

                    if (empty($kodeKelasList) && empty($kodeRuanganList) && empty($kodeJurusanList)) {
                        return response()->json([
                            'ok' => true,
                            'all_kode_kelas' => [],
                            'missing_kode_kelas' => [],
                            'all_kode_ruangan' => [],
                            'missing_kode_ruangan' => [],
                            'all_kode_jurusan' => [],
                            'missing_kode_jurusan' => [],
                            'kurikulum_options' => $this->kurikulumOptions(),
                        ]);
                    }

                    $kelasTingkat = !empty($kodeKelasList)
                        ? Kelas::whereIn('kode_kelas', $kodeKelasList)
                            ->get(['kode_kelas', 'nama_kelas', 'tingkat', 'kode_kurikulum'])
                            ->keyBy('kode_kelas')
                        : collect();

                    $missingKelas = [];
                    foreach ($kodeKelasList as $kode) {
                        if (!$kelasTingkat->has($kode)) {
                            $parsed = $this->parseKodeKelas($kode);
                            $missingKelas[] = [
                                'kode_kelas' => $kode,
                                'nama_kelas' => $kode,
                                'tingkat' => $parsed['tingkat'] ?? '',
                                'kode_kurikulum' => $parsed['kode_kurikulum'] ?? '',
                                'jumlah_siswa' => (int) $allRows
                                    ->filter(fn ($r) => trim((string) ($r['kode_kelas'] ?? '')) === $kode)
                                    ->count(),
                            ];
                        }
                    }

                    $ruanganTingkat = !empty($kodeRuanganList)
                        ? Ruangan::whereIn('kode_ruangan', $kodeRuanganList)
                            ->get(['kode_ruangan', 'nama_ruangan'])
                            ->keyBy('kode_ruangan')
                        : collect();

                    $missingRuangan = [];
                    foreach ($kodeRuanganList as $kode) {
                        if (!$ruanganTingkat->has($kode)) {
                            $missingRuangan[] = [
                                'kode_ruangan' => $kode,
                                'nama_ruangan' => $kode,
                                'kode_gedung' => '',
                                'jumlah_siswa' => (int) $allRows
                                    ->filter(fn ($r) => trim((string) ($r['kode_ruangan'] ?? '')) === $kode)
                                    ->count(),
                            ];
                        }
                    }

                    $jurusanTingkat = !empty($kodeJurusanList)
                        ? Jurusan::whereIn('kode_jurusan', $kodeJurusanList)
                            ->get(['kode_jurusan', 'nama'])
                            ->keyBy('kode_jurusan')
                        : collect();

                    $missingJurusan = [];
                    foreach ($kodeJurusanList as $kode) {
                        if (!$jurusanTingkat->has($kode)) {
                            $missingJurusan[] = [
                                'kode_jurusan' => $kode,
                                'nama' => $kode,
                                'jumlah_siswa' => (int) $allRows
                                    ->filter(fn ($r) => trim((string) ($r['kode_jurusan'] ?? '')) === $kode)
                                    ->count(),
                            ];
                        }
                    }

                    return response()->json([
                        'ok' => true,
                        'all_kode_kelas' => $kodeKelasList,
                        'missing_kode_kelas' => $missingKelas,
                        'all_kode_ruangan' => $kodeRuanganList,
                        'missing_kode_ruangan' => $missingRuangan,
                        'all_kode_jurusan' => $kodeJurusanList,
                        'missing_kode_jurusan' => $missingJurusan,
                        'kurikulum_options' => $this->kurikulumOptions(),
                    ]);
                } finally {
                    $tenancy->end();
                }
            } catch (\Throwable $e) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Gagal membaca file: ' . $e->getMessage(),
                ], 500);
            }
    }

    public function previewQuickKurikulum(Request $request)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            return response()->json(['ok' => false, 'message' => 'Hanya menerima AJAX.'], 400);
        }

        $tid = $this->currentTenantId($request);
        if (!$tid) {
            return response()->json([
                'ok' => false,
                'message' => 'Sekolah belum dipilih.',
            ], 422);
        }

        $tenantModel = Tenant::find($tid);
        if (!$tenantModel) {
            return response()->json([
                'ok' => false,
                'message' => 'Tenant tidak ditemukan.',
            ], 404);
        }

        $tenancy = app(\Stancl\Tenancy\Tenancy::class);
        try {
            $tenancy->initialize($tenantModel);

            $data = $request->validate([
                'nama_kurikulum' => ['required', 'string', 'max:191'],
                'kode_kurikulum' => ['nullable', 'string', 'max:50'],
                'status'         => ['nullable', 'in:aktif,nonaktif'],
            ], [
                'nama_kurikulum.required' => 'Nama kurikulum wajib diisi.',
            ]);

            $data['status'] = $data['status'] ?? 'aktif';

            if (!empty($data['kode_kurikulum'])) {
                $dup = Kurikulum::where('kode_kurikulum', $data['kode_kurikulum'])->exists();
                if ($dup) {
                    return response()->json([
                        'ok' => false,
                        'message' => "Kode kurikulum '{$data['kode_kurikulum']}' sudah dipakai.",
                    ], 422);
                }
            }

            $k = Kurikulum::create($data);

            return response()->json([
                'ok' => true,
                'message' => "Kurikulum {$k->nama_kurikulum} ditambah.",
                'data' => [
                    'id' => $k->id,
                    'kode_kurikulum' => $k->kode_kurikulum,
                    'nama_kurikulum' => $k->nama_kurikulum,
                    'label' => $k->nama_kurikulum . ($k->kode_kurikulum ? ' (' . $k->kode_kurikulum . ')' : ''),
                    'value' => (string) ($k->kode_kurikulum ?? $k->id),
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'ok' => false,
                'message' => collect($e->errors())->flatten()->first() ?? 'Data tidak valid.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Gagal menambah kurikulum: ' . $e->getMessage(),
            ], 500);
        } finally {
            $tenancy->end();
        }
    }

    public function previewQuickJurusan(Request $request)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            return response()->json(['ok' => false, 'message' => 'Hanya menerima AJAX.'], 400);
        }

        $tid = $this->currentTenantId($request);
        if (!$tid) {
            return response()->json(['ok' => false, 'message' => 'Sekolah belum dipilih.'], 422);
        }

        $tenantModel = Tenant::find($tid);
        if (!$tenantModel) {
            return response()->json(['ok' => false, 'message' => 'Tenant tidak ditemukan.'], 404);
        }

        $tenancy = app(\Stancl\Tenancy\Tenancy::class);
        try {
            $tenancy->initialize($tenantModel);

            $data = $request->validate([
                'kode_jurusan' => ['required', 'string', 'max:50'],
                'nama'         => ['required', 'string', 'max:120'],
                'status'       => ['nullable', 'in:aktif,nonaktif'],
            ], [
                'kode_jurusan.required' => 'Kode jurusan wajib diisi.',
                'nama.required'         => 'Nama jurusan wajib diisi.',
            ]);

            $data['status'] = $data['status'] ?? 'aktif';

            $dup = Jurusan::where('kode_jurusan', $data['kode_jurusan'])->exists();
            if ($dup) {
                return response()->json([
                    'ok' => false,
                    'message' => "Kode jurusan '{$data['kode_jurusan']}' sudah dipakai.",
                ], 422);
            }

            $j = Jurusan::create($data);

            return response()->json([
                'ok' => true,
                'message' => "Jurusan {$j->nama} ditambah.",
                'data' => [
                    'id' => $j->id,
                    'kode_jurusan' => $j->kode_jurusan,
                    'nama' => $j->nama,
                    'label' => $j->nama . ' (' . $j->kode_jurusan . ')',
                    'value' => $j->kode_jurusan,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'ok' => false,
                'message' => collect($e->errors())->flatten()->first() ?? 'Data tidak valid.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Gagal menambah jurusan: ' . $e->getMessage(),
            ], 500);
        } finally {
            $tenancy->end();
        }
    }

    private function kurikulumOptions(): array
    {
        return Kurikulum::orderBy('nama_kurikulum')
            ->get(['id', 'kode_kurikulum', 'nama_kurikulum'])
            ->map(function ($k) {
                $label = $k->nama_kurikulum;
                if (!empty($k->kode_kurikulum)) {
                    $label .= ' (' . $k->kode_kurikulum . ')';
                }
                return [
                    'value' => (string) ($k->kode_kurikulum ?? $k->id),
                    'label' => $label,
                ];
            })
            ->all();
    }

    private function parseKodeKelas(string $kode): array
    {
        $parts = preg_split('/[-._\s]+/', $kode) ?: [];
        if (empty($parts)) {
            return [];
        }
        // Tingkat disimpan dalam bentuk romawi (X, XI, XII) atau angka 1..12.
        // Token pertama sebelum delimiter (sebelum '-', '.', '_', whitespace) diperlakukan sebagai kandidat tingkat.
        // diprioritaskan: romawi (X/XI/XII dst.) di atas angka — agar konsisten dengan DB.
        $romawiMap = ['I' => 'I', 'II' => 'II', 'III' => 'III', 'IV' => 'IV', 'V' => 'V', 'VI' => 'VI', 'VII' => 'VII', 'VIII' => 'VIII', 'IX' => 'IX', 'X' => 'X', 'XI' => 'XI', 'XII' => 'XII'];
        $tingkat = null;
        $kodeKurikulum = null;
        foreach ($parts as $p) {
            $up = strtoupper(trim($p));
            if (isset($romawiMap[$up])) {
                $tingkat = $romawiMap[$up];
                break;
            }
        }
        if ($tingkat === null) {
            foreach ($parts as $p) {
                $up = strtoupper(trim($p));
                if (is_numeric($up) && (int) $up >= 1 && (int) $up <= 12) {
                    $tingkat = $up;
                    break;
                }
            }
        }
        foreach ($parts as $p) {
            $up = strtoupper(trim($p));
            if ($up === '') continue;
            if (isset($romawiMap[$up])) continue;
            if (is_numeric($up) && (int) $up >= 1 && (int) $up <= 12) continue;
            $kodeKurikulum = $up;
            break;
        }
        return array_filter(['tingkat' => $tingkat, 'kode_kurikulum' => $kodeKurikulum], fn ($v) => $v !== null);
    }

    private function currentTenantId(Request $request): ?string
    {
        return $request->attributes->get('current_tenant_id');
    }
}

