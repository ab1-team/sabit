<?php

namespace App\Http\Controllers;


use App\Models\Profil;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfilController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $title = "Profil";
        $jabatans = DB::table('jabatan')->orderBy('nama_jabatan')->get(['id', 'nama_jabatan']);
        return view('profil.index', compact('title', 'user', 'jabatans'));
    }

    public function update(Request $request, $id)
    {
        abort_unless((int) $id === (int) auth()->id(), 403);
        $user = User::findOrFail($id);

        $data = $request->only([
            'nama',
            'nik',
            'id_jabatan',
            'email',
            'jenis_kelamin',
            'telepon',
            'alamat',
            'username',
        ]);

        $rules = [
            'nama' => 'required',
            'email' => 'required|email',
            'id_jabatan' => 'nullable|exists:jabatan,id',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'password' => 'nullable|confirmed',
        ];

        $validate = Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {

            if ($user->foto && Storage::disk('public')->exists('users/' . $user->foto)) {
                Storage::disk('public')->delete('users/' . $user->foto);
            }

            $fileName = time() . '_' . $request->file('photo')->getClientOriginalName();
            $request->file('photo')->storeAs('users', $fileName, 'public');
            $data['foto'] = $fileName;
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'msg' => 'Profil berhasil diperbarui'
        ]);
    }
}
