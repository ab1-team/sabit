<?php

namespace App\Http\Controllers\Tenant;

use App\Exports\MigrasiSiswaTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\MigrasiSiswaImport;
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

            $kelas = collect();
            if ($selectedTahun) {
                $kelas = DB::table('kelas')
                    ->where('tahun_akademik_id', $selectedTahun)
                    ->when($selectedJurusan, fn($q) => $q->where('jurusan_id', $selectedJurusan))
                    ->orderBy('nama_kelas')
                    ->get(['id', 'nama_kelas', 'tingkat']);
            }
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

    private function currentTenantId(Request $request): ?string
    {
        return $request->attributes->get('current_tenant_id');
    }
}
