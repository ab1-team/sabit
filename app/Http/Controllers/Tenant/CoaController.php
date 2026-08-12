<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Tenant\BaseSchoolController;

use App\Models\Rekening;
use Illuminate\Http\Request;
use App\Models\Tenant;

class CoaController extends BaseSchoolController
{
    public function index(Tenant $tenant)
    {
        return $this->runInTenant($tenant, function () use ($tenant) {
            $rekenings = Rekening::with('akunLevel3')
                ->orderBy('kode_akun')
                ->get();

            $akunLevel1 = \App\Models\AkunLevel1::with(['akun2' => function ($q) {
                $q->orderBy('kode_akun');
            }])
            ->orderBy('kode_akun')
            ->get();

            // Build map L2.id => L3[]
            $l3ByL2 = \App\Models\AkunLevel3::orderBy('kode_akun')->get()->groupBy('parent_id');

            // Build map L3.id => rekening[]
            $rekeningsByL3 = $rekenings->groupBy('parent_id');

            // Lacak rekening yang sudah di-attach supaya yang orphan tetap tampil
            $attachedRekeningIds = [];

            $akunLevel1->transform(function ($l1) use ($l3ByL2, $rekeningsByL3, &$attachedRekeningIds) {
                $l1->tree = $l1->akun2->map(function ($l2) use ($l3ByL2, $rekeningsByL3, &$attachedRekeningIds) {
                    $l3List = $l3ByL2->get($l2->id, collect())->sortBy('kode_akun')->values();
                    $l2->akun3 = $l3List->map(function ($l3) use ($rekeningsByL3, &$attachedRekeningIds) {
                        $reks = $rekeningsByL3->get($l3->id, collect())->sortBy('kode_akun')->values();
                        $attachedRekeningIds = array_merge($attachedRekeningIds, $reks->pluck('id')->all());
                        $l3->rekenings = $reks;
                        return $l3;
                    });
                    return $l2;
                });
                return $l1;
            });

            // Rekening yang parent_id-nya tidak cocok dengan L3 manapun
            // (mis. data historis) dikumpulkan di section "Lainnya" per L1.
            $orphanRekenings = $rekenings->reject(function ($r) use ($attachedRekeningIds) {
                return in_array($r->id, $attachedRekeningIds, true);
            });

            // Kelompokkan orphan berdasarkan prefix kode_akun (level 1) supaya
            // tampil di section L1 yang sesuai.
            $akunLevel1->transform(function ($l1) use ($orphanRekenings) {
                $l1->orphanRekenings = $orphanRekenings->filter(function ($r) use ($l1) {
                    $prefix = $l1->kode_akun;
                    $pos = strpos($prefix, '.');
                    $topPrefix = $pos !== false ? substr($prefix, 0, $pos) : $prefix;
                    return strpos($r->kode_akun, $topPrefix) === 0;
                })->values();
                return $l1;
            });

            return view('tenant.tenant.coa', [
                'tenant'    => $tenant,
                'rekenings' => $rekenings,
                'akunLevel1' => $akunLevel1,
                'orphanRekenings' => $orphanRekenings,
            ]);
        });
    }

    public function nonaktifkan(Tenant $tenant, Rekening $rekening)
    {
        return $this->runInTenant($tenant, function () use ($rekening, $tenant) {
            $rekening->tgl_nonaktif = now()->toDateString();
            $rekening->save();

            return redirect()->route('tenant.tenant.coa.index', $tenant)
                ->with('success', "Rekening {$rekening->kode_akun} dinonaktifkan");
        });
    }

    public function aktifkan(Tenant $tenant, Rekening $rekening)
    {
        return $this->runInTenant($tenant, function () use ($rekening, $tenant) {
            $rekening->tgl_nonaktif = null;
            $rekening->save();

            return redirect()->route('tenant.tenant.coa.index', $tenant)
                ->with('success', "Rekening {$rekening->kode_akun} diaktifkan");
        });
    }
}


