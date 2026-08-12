@php
    $initialGedung = old('kode_gedung', $ruanganItem->kode_gedung ?? '');
    $initialKode = old('kode_ruangan', $ruanganItem->kode_ruangan ?? '');
    $initialNama = old('nama_ruangan', $ruanganItem->nama_ruangan ?? '');
    $initialKB = old('kapasitas_belajar', $ruanganItem->kapasitas_belajar ?? '');
    $initialKU = old('kapasitas_ujian', $ruanganItem->kapasitas_ujian ?? '');
    $initialKet = old('keterangan', $ruanganItem->keterangan ?? '');
    $initialStatus = old('status', $ruanganItem->status ?? 'aktif');
@endphp

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:col-span-2">
    <div>
        <label for="kode_gedung" class="mb-1.5 block text-sm font-semibold text-slate-700">Kode Gedung <span class="text-rose-500">*</span></label>
        <input type="text" id="kode_gedung" name="kode_gedung" required value="{{ $initialGedung }}" class="invoice-input font-mono" placeholder="A, B, UTAMA" autocomplete="off">
        @error('kode_gedung')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="kode_ruangan" class="mb-1.5 block text-sm font-semibold text-slate-700">Kode Ruangan <span class="text-rose-500">*</span></label>
        <input type="text" id="kode_ruangan" name="kode_ruangan" required value="{{ $initialKode }}" class="invoice-input font-mono" placeholder="A-101" autocomplete="off">
        @error('kode_ruangan')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div class="sm:col-span-2">
        <label for="nama_ruangan" class="mb-1.5 block text-sm font-semibold text-slate-700">Nama Ruangan <span class="text-rose-500">*</span></label>
        <input type="text" id="nama_ruangan" name="nama_ruangan" required value="{{ $initialNama }}" class="invoice-input" placeholder="Ruang Kelas 1" autocomplete="off">
        @error('nama_ruangan')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="kapasitas_belajar" class="mb-1.5 block text-sm font-semibold text-slate-700">Kapasitas Belajar</label>
        <input type="number" min="0" id="kapasitas_belajar" name="kapasitas_belajar" value="{{ $initialKB }}" class="invoice-input" placeholder="0" autocomplete="off">
        @error('kapasitas_belajar')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="kapasitas_ujian" class="mb-1.5 block text-sm font-semibold text-slate-700">Kapasitas Ujian</label>
        <input type="number" min="0" id="kapasitas_ujian" name="kapasitas_ujian" value="{{ $initialKU }}" class="invoice-input" placeholder="0" autocomplete="off">
        @error('kapasitas_ujian')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div class="sm:col-span-2">
        <label for="keterangan" class="mb-1.5 block text-sm font-semibold text-slate-700">Keterangan</label>
        <textarea id="keterangan" name="keterangan" rows="2" class="invoice-input" placeholder="Opsional">{{ $initialKet }}</textarea>
        @error('keterangan')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div class="sm:col-span-2">
        <label for="status" class="mb-1.5 block text-sm font-semibold text-slate-700">Status <span class="text-rose-500">*</span></label>
        <select id="status" name="status" required class="invoice-input bg-white">
            <option value="aktif" @selected($initialStatus === 'aktif')>Aktif</option>
            <option value="non_aktif" @selected($initialStatus === 'non_aktif')>Nonaktif</option>
        </select>
        @error('status')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
</div>
