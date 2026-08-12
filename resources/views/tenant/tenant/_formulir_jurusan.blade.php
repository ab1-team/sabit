@php
    $initialNama = old('nama', $jurusanItem->nama ?? '');
    $initialKode = old('kode_jurusan', $jurusanItem->kode_jurusan ?? '');
    $initialStatus = old('status', $jurusanItem->status ?? 'aktif');
@endphp

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:col-span-2">
    <div>
        <label for="nama" class="mb-1.5 block text-sm font-semibold text-slate-700">Nama Jurusan <span class="text-rose-500">*</span></label>
        <input type="text" id="nama" name="nama" required value="{{ $initialNama }}" class="invoice-input" placeholder="Contoh: IPA, IPS, Bahasa" autocomplete="off">
        @error('nama')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="kode_jurusan" class="mb-1.5 block text-sm font-semibold text-slate-700">Kode Jurusan <span class="text-rose-500">*</span></label>
        <input type="text" id="kode_jurusan" name="kode_jurusan" required value="{{ $initialKode }}" class="invoice-input font-mono" placeholder="IPA, IPS" autocomplete="off">
        @error('kode_jurusan')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div class="sm:col-span-2">
        <label for="status" class="mb-1.5 block text-sm font-semibold text-slate-700">Status <span class="text-rose-500">*</span></label>
        <select id="status" name="status" required class="invoice-input bg-white">
            <option value="aktif" @selected($initialStatus === 'aktif')>Aktif</option>
            <option value="nonaktif" @selected($initialStatus === 'nonaktif')>Nonaktif</option>
        </select>
        @error('status')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
</div>
