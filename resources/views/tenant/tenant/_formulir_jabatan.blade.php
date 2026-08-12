@php
    $initialNama = old('nama_jabatan', $jabatanItem->nama_jabatan ?? '');
    $initialKode = old('kode_jabatan', $jabatanItem->kode_jabatan ?? '');
@endphp

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:col-span-2">
    <div>
        <label for="nama_jabatan" class="mb-1.5 block text-sm font-semibold text-slate-700">Nama Jabatan <span class="text-rose-500">*</span></label>
        <input type="text" id="nama_jabatan" name="nama_jabatan" required value="{{ $initialNama }}" class="invoice-input" placeholder="Contoh: Kepala Sekolah" autocomplete="off">
        @error('nama_jabatan')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="kode_jabatan" class="mb-1.5 block text-sm font-semibold text-slate-700">Kode Jabatan</label>
        <input type="text" id="kode_jabatan" name="kode_jabatan" value="{{ $initialKode }}" class="invoice-input font-mono" placeholder="BEN, ADM, KEP_SEK, dst" autocomplete="off">
        @error('kode_jabatan')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
</div>
