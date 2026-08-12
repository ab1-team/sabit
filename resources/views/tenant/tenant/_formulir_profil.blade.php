@php
    $initialNama = old('nama', $profilItem->nama ?? '');
    $initialEmail = old('email', $profilItem->email ?? '');
    $initialTelpon = old('telpon', $profilItem->telpon ?? '');
    $initialJatuhTempo = old('jatuh_tempo', $profilItem->jatuh_tempo ?? 10);
    $initialAlamat = old('alamat', $profilItem->alamat ?? '');
@endphp

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <div>
        <label for="nama" class="mb-1.5 block text-sm font-semibold text-slate-700">Nama Sekolah <span class="text-rose-500">*</span></label>
        <input type="text" id="nama" name="nama" required value="{{ $initialNama }}" class="invoice-input" placeholder="Contoh: SMA Negeri 1" autocomplete="off">
        @error('nama')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="email" class="mb-1.5 block text-sm font-semibold text-slate-700">Email</label>
        <input type="email" id="email" name="email" value="{{ $initialEmail }}" class="invoice-input" placeholder="email@sekolah.sch.id" autocomplete="off">
        @error('email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="telpon" class="mb-1.5 block text-sm font-semibold text-slate-700">Telepon</label>
        <input type="text" id="telpon" name="telpon" value="{{ $initialTelpon }}" class="invoice-input" placeholder="(021) 1234567" autocomplete="off">
        @error('telpon')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="jatuh_tempo" class="mb-1.5 block text-sm font-semibold text-slate-700">Tanggal Jatuh Tempo (1-31)</label>
        <input type="number" id="jatuh_tempo" name="jatuh_tempo" min="1" max="31" value="{{ $initialJatuhTempo }}" class="invoice-input" placeholder="10" autocomplete="off">
        @error('jatuh_tempo')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
</div>

<div>
    <label for="alamat" class="mb-1.5 block text-sm font-semibold text-slate-700">Alamat</label>
    <textarea id="alamat" name="alamat" rows="3" class="invoice-input" placeholder="Alamat lengkap sekolah">{{ $initialAlamat }}</textarea>
    @error('alamat')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
</div>
