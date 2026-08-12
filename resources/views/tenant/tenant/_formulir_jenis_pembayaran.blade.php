@php
    $initialNama = old('nama', $jenisPembayaranItem->nama ?? '');
    $initialKode = old('kode_akun', $jenisPembayaranItem->kode_akun ?? '');
    $initialJumlah = old('jumlah', $jenisPembayaranItem->jumlah ?? '');
@endphp

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:col-span-2">
    <div class="sm:col-span-2">
        <label for="nama" class="mb-1.5 block text-sm font-semibold text-slate-700">Nama <span class="text-rose-500">*</span></label>
        <input type="text" id="nama" name="nama" required value="{{ $initialNama }}" class="invoice-input" placeholder="Contoh: SPP Bulanan, Uang Gedung" autocomplete="off">
        @error('nama')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="kode_akun" class="mb-1.5 block text-sm font-semibold text-slate-700">Kode Akun</label>
        <input type="text" id="kode_akun" name="kode_akun" value="{{ $initialKode }}" class="invoice-input font-mono" placeholder="Contoh: 4.1.1" autocomplete="off">
        @error('kode_akun')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="jumlah" class="mb-1.5 block text-sm font-semibold text-slate-700">Jumlah</label>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm font-semibold text-slate-400">Rp</span>
            <input type="text" inputmode="numeric" id="jumlah" name="jumlah" value="{{ $initialJumlah }}" class="invoice-input nominal pl-10" placeholder="0" autocomplete="off">
        </div>
        @error('jumlah')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
</div>
