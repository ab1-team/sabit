<?php

namespace App\Utils;

use App\Models\AkunLevel2;
use App\Models\Rekening;
use App\Models\Saldo;
use App\Models\Transaksi;
use DB;

class Keuangan
{
    public static function hitungSaldo($lev3, $tgl_awal = null, $tgl_akhir = null)
    {
        $saldo = 0;

        $rekenings = method_exists($lev3, 'rekeningByPrefix')
            ? $lev3->rekeningByPrefix()
            : $lev3->rek;
        $kodeAkunList = $rekenings->pluck('kode_akun')->all();
        if (empty($kodeAkunList)) {
            return 0;
        }

        $debitQuery = DB::table('transaksi')
            ->whereIn('rekening_debit', $kodeAkunList)
            ->whereNull('deleted_at')
            ->select('rekening_debit as kode_akun');

        $kreditQuery = DB::table('transaksi')
            ->whereIn('rekening_kredit', $kodeAkunList)
            ->whereNull('deleted_at')
            ->select('rekening_kredit as kode_akun');

        if ($tgl_awal && $tgl_akhir) {
            $debitQuery->whereBetween('tanggal_transaksi', [$tgl_awal, $tgl_akhir]);
            $kreditQuery->whereBetween('tanggal_transaksi', [$tgl_awal, $tgl_akhir]);
        }

        $debits = $debitQuery->groupBy('rekening_debit')
            ->selectRaw('rekening_debit as kode_akun, SUM(jumlah) as total')
            ->pluck('total', 'kode_akun');

        $kredits = $kreditQuery->groupBy('rekening_kredit')
            ->selectRaw('rekening_kredit as kode_akun, SUM(jumlah) as total')
            ->pluck('total', 'kode_akun');

        foreach ($rekenings as $rekening) {
            $d = (float) ($debits[$rekening->kode_akun] ?? 0);
            $k = (float) ($kredits[$rekening->kode_akun] ?? 0);
            $saldo_rekening = strtolower((string) $rekening->jenis_mutasi) === 'debet'
                ? $d - $k
                : $k - $d;
            $saldo += $saldo_rekening;
        }

        return $saldo;
    }

    public static function formatSaldo($nilai)
    {
        $formatted = Angka::format(abs($nilai), 2);
        return $nilai < 0 ? '(' . $formatted . ')' : $formatted;
    }

    public function listLabaRugi(string $tgl): array
    {
        $pendapatanQ = Rekening::query()->where('kode_akun', 'LIKE', '4.1.%');
        $bebanQ = Rekening::query()->where(function ($q) {
            $q->where('kode_akun', 'LIKE', '5.1.%')
              ->orWhere(function ($q2) {
                  $q2->where('kode_akun', 'LIKE', '5.2.%')
                     ->where('kode_akun', '!=', '5.2.01.01');
              });
        });
        $bpQ = Rekening::query()->where('kode_akun', '5.2.01.01');
        $penQ = Rekening::query()->where(function ($q) {
            $q->where('kode_akun', 'LIKE', '4.2.%')
              ->orWhere(function ($q2) {
                  $q2->where('kode_akun', 'LIKE', '4.3.%')
                     ->whereNotIn('kode_akun', ['4.3.01.01', '4.3.01.02', '4.3.01.03']);
              });
        });
        $pendlQ = Rekening::query()->whereIn('kode_akun', ['4.3.01.01', '4.3.01.02', '4.3.01.03']);
        $bebQ = Rekening::query()->where('kode_akun', 'LIKE', '5.3.%')
            ->where('kode_akun', '!=', '5.4.01.01');
        $phQ = Rekening::query()->where('kode_akun', 'LIKE', '5.4.%');

        $allKodeAkun = collect()
            ->merge($pendapatanQ->pluck('kode_akun'))
            ->merge($bebanQ->pluck('kode_akun'))
            ->merge($bpQ->pluck('kode_akun'))
            ->merge($penQ->pluck('kode_akun'))
            ->merge($pendlQ->pluck('kode_akun'))
            ->merge($bebQ->pluck('kode_akun'))
            ->merge($phQ->pluck('kode_akun'))
            ->unique()
            ->values()
            ->all();

        if (empty($allKodeAkun)) {
            return [
                'pendapatan' => collect(), 'beban' => collect(), 'bp' => collect(),
                'pen' => collect(), 'pendl' => collect(), 'beb' => collect(), 'ph' => collect(),
            ];
        }

        $debits = DB::table('transaksi')
            ->whereIn('rekening_debit', $allKodeAkun)
            ->whereNull('deleted_at')
            ->where('tanggal_transaksi', '<=', $tgl)
            ->groupBy('rekening_debit')
            ->selectRaw('rekening_debit as kode_akun, SUM(jumlah) as total')
            ->pluck('total', 'kode_akun');

        $kredits = DB::table('transaksi')
            ->whereIn('rekening_kredit', $allKodeAkun)
            ->whereNull('deleted_at')
            ->where('tanggal_transaksi', '<=', $tgl)
            ->groupBy('rekening_kredit')
            ->selectRaw('rekening_kredit as kode_akun, SUM(jumlah) as total')
            ->pluck('total', 'kode_akun');

        $attachSaldo = function ($collection) use ($debits, $kredits) {
            return $collection->map(function ($rek) use ($debits, $kredits) {
                $d = (float) ($debits[$rek->kode_akun] ?? 0);
                $k = (float) ($kredits[$rek->kode_akun] ?? 0);
                $rek->saldo = strtolower((string) $rek->jenis_mutasi) === 'debet' ? $d - $k : $k - $d;
                return $rek;
            });
        };

        return [
            'pendapatan' => $attachSaldo($pendapatanQ->orderBy('kode_akun')->get()),
            'beban'      => $attachSaldo($bebanQ->orderBy('kode_akun')->get()),
            'bp'         => $attachSaldo($bpQ->orderBy('kode_akun')->get()),
            'pen'        => $attachSaldo($penQ->orderBy('kode_akun')->get()),
            'pendl'      => $attachSaldo($pendlQ->orderBy('kode_akun')->get()),
            'beb'        => $attachSaldo($bebQ->orderBy('kode_akun')->get()),
            'ph'         => $attachSaldo($phQ->orderBy('kode_akun')->get()),
        ];
    }

    public function saldoKas($tgl_akhir)
    {
        $tanggal = explode('-', $tgl_akhir);
        $thn = $tanggal[0];

        $range_awal  = "$thn-01-01";
        $range_akhir = $tgl_akhir;

        $rekeningKas = Rekening::query()
            ->where(function ($q) {
                $q->where('kode_akun', 'like', '1.1.01%')
                  ->orWhere('kode_akun', 'like', '1.1.02%');
            })
            ->pluck('kode_akun')
            ->all();

        if (empty($rekeningKas)) {
            return 0;
        }

        $total_debit = (float) DB::table('transaksi')
            ->whereIn('rekening_debit', $rekeningKas)
            ->whereNull('deleted_at')
            ->whereBetween('tanggal_transaksi', [$range_awal, $range_akhir])
            ->sum('jumlah');

        $total_kredit = (float) DB::table('transaksi')
            ->whereIn('rekening_kredit', $rekeningKas)
            ->whereNull('deleted_at')
            ->whereBetween('tanggal_transaksi', [$range_awal, $range_akhir])
            ->sum('jumlah');

        return $total_debit - $total_kredit;
    }

    public function romawi($angka)
    {
        $map = [1=>'I',2=>'II',3=>'III',4=>'IV',5=>'V',6=>'VI',7=>'VII',8=>'VIII',9=>'IX',10=>'X'];
        return $map[$angka] ?? $angka;
    }

    public function komSaldo($rek)
    {
        $awal_debit = 0;
        $awal_kredit = 0;
        $saldo_debit = 0;
        $saldo_kredit = 0;

        foreach ($rek->kom_saldo as $kom) {
            if ($kom->bulan == 0) {
                $awal_debit += (float) $kom->debit;
                $awal_kredit += (float) $kom->kredit;
            } else {
                $saldo_debit += (float) $kom->debit;
                $saldo_kredit += (float) $kom->kredit;
            }
        }

        $lev1 = (int) $rek->lev1;
        if ($lev1 === 1 || $lev1 === 5) {
            return ($awal_debit - $awal_kredit) + ($saldo_debit - $saldo_kredit);
        }
        return ($awal_kredit - $awal_debit) + ($saldo_kredit - $saldo_debit);
    }

    public static function hitungSaldoCALK($rekening, $tgl_awal = null, $tgl_akhir = null)
    {
        $total_debit = $rekening->transaksiDebit()
            ->when($tgl_awal && $tgl_akhir, fn($q) => $q->whereBetween('tanggal_transaksi', [$tgl_awal, $tgl_akhir]))
            ->whereNull('deleted_at')
            ->sum('jumlah');

        $total_kredit = $rekening->transaksiKredit()
            ->when($tgl_awal && $tgl_akhir, fn($q) => $q->whereBetween('tanggal_transaksi', [$tgl_awal, $tgl_akhir]))
            ->whereNull('deleted_at')
            ->sum('jumlah');

        $saldo_rekening = strtolower((string) $rekening->jenis_mutasi) === 'debet'
            ? $total_debit - $total_kredit
            : $total_kredit - $total_debit;

        return $saldo_rekening;
    }

    public static function formatSaldoCALK($nilai)
    {
        $formatted = Angka::format(abs($nilai), 2);
        return $nilai < 0 ? '(' . $formatted . ')' : $formatted;
    }
}
