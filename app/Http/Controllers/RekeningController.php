<?php

namespace App\Http\Controllers;


use App\Models\AkunLevel1;
use App\Models\Rekening;
use Illuminate\Http\Request;

class RekeningController extends Controller
{
    public function tree()
    {
        $akun1 = AkunLevel1::with([
            'akun2',
            'akun2.akun3',
            'akun2.akun3.rek',
        ])->orderBy('kode_akun')->get();

        return view('rekening.tree', [
            'title' => 'Chart of Accounts',
            'akun1' => $akun1,
        ]);
    }

    public function nonaktifkan(Request $request, Rekening $rekening)
    {
        $rekening->update([
            'tgl_nonaktif' => now()->toDateString(),
        ]);

        return response()->json([
            'success' => true,
            'msg'     => 'Rekening dinonaktifkan',
        ]);
    }

    public function aktifkan(Rekening $rekening)
    {
        $rekening->update(['tgl_nonaktif' => null]);

        return response()->json([
            'success' => true,
            'msg'     => 'Rekening diaktifkan kembali',
        ]);
    }
}
