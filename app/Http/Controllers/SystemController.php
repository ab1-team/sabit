<?php

namespace App\Http\Controllers;


use App\Models\Spp;
use App\Models\Siswa;
use App\Models\Transaksi;
use App\Models\AnggotaKelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SystemController extends Controller
{
    public function GenerateTunggakan(Request $request)
    {
        $jobId = (string) $request->query('job', uniqid('gen_', true));
        $cachePrefix = 'piutang_done:' . (tenant('id') ?? 'central') . ':' . $jobId;

        $now = Carbon::now();
        $bulanLalu = $now->copy()->subMonthNoOverflow();
        $bulanTarget = (int) $bulanLalu->format('m');
        $tahunTarget = (int) $bulanLalu->format('Y');

        $sppBelumLunas = Spp::where('status', 'B')
            ->whereYear('tanggal', $tahunTarget)
            ->whereMonth('tanggal', $bulanTarget)
            ->orderBy('tanggal', 'asc')
            ->get()
            ->groupBy('anggota_kelas');

        if ($sppBelumLunas->isEmpty()) {
            cache()->put($cachePrefix, [
                'inserted' => 0,
                'skipped' => 0,
                'bulan' => \App\Utils\Tanggal::namaBulanNew($bulanTarget) . ' ' . $tahunTarget,
            ], now()->addHour());

            return response()->json(['ok' => true, 'inserted' => 0, 'skipped' => 0]);
        }

        $akIds = $sppBelumLunas->keys()->all();
        $akMap = AnggotaKelas::whereIn('id', $akIds)
            ->where('status', 'aktif')
            ->get()
            ->keyBy('id');

        $siswaIds = $akMap->pluck('id_siswa')->unique()->all();

        $existing = Transaksi::whereIn('siswa_id', $siswaIds)
            ->whereIn('kode_spp', $sppBelumLunas->flatten()->pluck('kode'))
            ->where('rekening_debit', '1.1.03.01')
            ->where('rekening_kredit', '1.1.04.01')
            ->whereNull('deleted_at')
            ->get()
            ->mapWithKeys(fn($t) => [$t->siswa_id . '|' . $t->kode_spp => true])
            ->all();

        $inserted = 0;
        $skipped = 0;
        $bulanLabel = \App\Utils\Tanggal::namaBulanNew($bulanTarget) . ' ' . $tahunTarget;
        $nowDate = $now->format('Y-m-d');
        $userId = auth()->user()->id ?? null;

        DB::transaction(function () use ($sppBelumLunas, $akMap, $existing, $bulanLabel, $bulanTarget, $tahunTarget, $nowDate, $userId, &$inserted, &$skipped) {
            $rows = [];
            foreach ($sppBelumLunas as $akId => $spps) {
                $ak = $akMap->get($akId);
                if (!$ak) continue;
                $siswaId = $ak->id_siswa;
                foreach ($spps as $spp) {
                    if (isset($existing[$siswaId . '|' . $spp->kode])) {
                        $skipped++;
                        continue;
                    }
                    $rows[] = [
                        'tanggal_transaksi' => $nowDate,
                        'idtp' => null,
                        'invoice' => 0,
                        'rekening_debit' => '1.1.03.01',
                        'rekening_kredit' => '1.1.04.01',
                        'kode_spp' => $spp->kode,
                        'siswa_id' => $siswaId,
                        'jumlah' => $spp->nominal,
                        'keterangan' => 'Tagihan SPP bulan ' . $bulanLabel,
                        'urutan' => '0',
                        'user_id' => $userId,
                        'created_at' => $nowDate,
                        'updated_at' => $nowDate,
                    ];
                    $inserted++;
                }
            }
            if ($rows) {
                DB::table('transaksi')->insert($rows);
            }
        });

        cache()->put($cachePrefix, [
            'inserted' => $inserted,
            'skipped' => $skipped,
            'bulan' => $bulanLabel,
        ], now()->addHour());

        if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'inserted' => $inserted,
                'skipped' => $skipped,
            ]);
        }

        return "<!doctype html><meta charset=utf-8><title>Generate Selesai</title>
<style>body{font-family:system-ui;background:#0f172a;color:#e2e8f0;display:flex;align-items:center;justify-content:center;height:100vh;margin:0}
.box{background:#1e293b;padding:24px 28px;border-radius:12px;text-align:center;min-width:280px}
.ok{color:#22c55e;font-size:36px}</style>
<div class=box><div class=ok>&#10003;</div><h3>Generate Selesai</h3><p>Dibuat: <b>{$inserted}</b> &middot; Dilewati: <b>{$skipped}</b></p><p style=color:#94a3b8;font-size:13px>Tab ini akan tertutup otomatis...</p>
<script>setTimeout(()=>window.close(),1500);</script>";
    }

    public function piutangStatus(Request $request)
    {
        $jobId = (string) $request->query('job', '');
        $cachePrefix = 'piutang_done:' . (tenant('id') ?? 'central') . ':' . $jobId;
        $data = cache()->get($cachePrefix);

        return response()->json([
            'done' => (bool) $data,
            'data' => $data,
        ]);
    }
}

