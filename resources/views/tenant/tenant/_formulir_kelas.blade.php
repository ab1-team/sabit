@php
    $initialKode = old('kode_kelas', $kelasItem->kode_kelas ?? '');
    $initialNama = old('nama_kelas', $kelasItem->nama_kelas ?? '');
    $initialTingkat = old('tingkat', $kelasItem->tingkat ?? '');
    $initialKurikulum = old('kode_kurikulum', $kelasItem->kode_kurikulum ?? '');
@endphp

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:col-span-2">
    <div>
        <label for="kode_kelas" class="mb-1.5 block text-sm font-semibold text-slate-700">Kode Kelas <span class="text-rose-500">*</span></label>
        <input type="text" id="kode_kelas" name="kode_kelas" required value="{{ $initialKode }}" class="invoice-input" placeholder="Contoh: X-IPA-1" autocomplete="off">
        @error('kode_kelas')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="nama_kelas" class="mb-1.5 block text-sm font-semibold text-slate-700">Nama Kelas <span class="text-rose-500">*</span></label>
        <input type="text" id="nama_kelas" name="nama_kelas" required value="{{ $initialNama }}" class="invoice-input" placeholder="Contoh: X IPA 1" autocomplete="off">
        @error('nama_kelas')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="tingkat" class="mb-1.5 block text-sm font-semibold text-slate-700">Tingkat <span class="text-rose-500">*</span></label>
        <input type="text" id="tingkat" name="tingkat" required value="{{ $initialTingkat }}" class="invoice-input" placeholder="Contoh: X, XI, XII" autocomplete="off">
        @error('tingkat')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="kode_kurikulum" class="mb-1.5 block text-sm font-semibold text-slate-700">Kurikulum <span class="text-rose-500">*</span></label>
        <select id="kode_kurikulum" name="kode_kurikulum" required class="invoice-input select2 bg-white" data-placeholder="Pilih kurikulum">
            <option value="">Pilih kurikulum</option>
            @foreach ($kurikulums as $kur)
                <option value="{{ $kur->id }}" @selected((string) $initialKurikulum === (string) $kur->id)>{{ $kur->nama_kurikulum }}</option>
            @endforeach
        </select>
        @error('kode_kurikulum')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
</div>
