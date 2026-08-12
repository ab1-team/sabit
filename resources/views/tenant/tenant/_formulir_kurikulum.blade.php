@php
    $initialNama = old('nama_kurikulum', $kurikulumItem->nama_kurikulum ?? '');
    $initialKode = old('kode_kurikulum', $kurikulumItem->kode_kurikulum ?? '');
    $initialStatus = old('status', $kurikulumItem->status ?? 'aktif');
@endphp

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:col-span-2">
    <div>
        <label for="nama_kurikulum" class="mb-1.5 block text-sm font-semibold text-slate-700">Nama Kurikulum <span class="text-rose-500">*</span></label>
        <input type="text" id="nama_kurikulum" name="nama_kurikulum" required value="{{ $initialNama }}" class="invoice-input" placeholder="Contoh: Kurikulum 2013, Kurikulum Merdeka" autocomplete="off">
        @error('nama_kurikulum')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="kode_kurikulum" class="mb-1.5 block text-sm font-semibold text-slate-700">Kode Kurikulum</label>
        <input type="text" id="kode_kurikulum" name="kode_kurikulum" value="{{ $initialKode }}" class="invoice-input font-mono" placeholder="K13, MERDEKA" autocomplete="off">
        @error('kode_kurikulum')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
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
