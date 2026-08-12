@php
    $initialNama = old('nama_tahun', $tahunAkademikItem->nama_tahun ?? '');
    $initialKet = old('keterangan', $tahunAkademikItem->keterangan ?? '');
    $initialStatus = old('status', $tahunAkademikItem->status ?? 'nonaktif');
@endphp

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:col-span-2">
    <div>
        <label for="nama_tahun" class="mb-1.5 block text-sm font-semibold text-slate-700">Nama Tahun <span class="text-rose-500">*</span></label>
        <input type="text" id="nama_tahun" name="nama_tahun" required value="{{ $initialNama }}" class="invoice-input" placeholder="2026/2027" autocomplete="off">
        @error('nama_tahun')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="status" class="mb-1.5 block text-sm font-semibold text-slate-700">Status <span class="text-rose-500">*</span></label>
        <select id="status" name="status" required class="invoice-input bg-white">
            <option value="nonaktif" @selected($initialStatus === 'nonaktif')>Nonaktif</option>
            <option value="aktif" @selected($initialStatus === 'aktif')>Aktif</option>
        </select>
        @error('status')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div class="sm:col-span-2">
        <label for="keterangan" class="mb-1.5 block text-sm font-semibold text-slate-700">Keterangan</label>
        <input type="text" id="keterangan" name="keterangan" value="{{ $initialKet }}" class="invoice-input" placeholder="Opsional" autocomplete="off">
        @error('keterangan')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
</div>
