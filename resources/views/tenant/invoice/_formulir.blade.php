@php
    $initialInvoiceDate = old('tgl_invoice', optional($invoice->tgl_invoice)->format('Y-m-d')) ?? '';
    $initialStatus = old('status', $invoice->status ?? 'unpaid');
    $initialType = old('jenis_pembayaran', $invoice->jenis_pembayaran ?? '');
    $initialAmount = old('jumlah', $invoice->jumlah ?? '');
    $initialTenantId = old('tenant_id', $invoice->tenant_id ?? '');
    $paymentTypes = [
        'Biaya Lisensi Instalasi',
        'Biaya Perpanjangan Maintenance dan Server',
        'Biaya Bimbingan Teknis',
        'Biaya Migrasi Ulang',
        'Biaya Aktivasi WA Gateway',
    ];
    $tenantsList = $tenants ?? [];
@endphp

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:col-span-2">
    <div>
        <label for="tenant_id" class="mb-1.5 block text-sm font-semibold text-slate-700">Sekolah <span class="text-rose-500">*</span></label>
        <select id="tenant_id" name="tenant_id" required class="invoice-input select2 bg-white" data-placeholder="Pilih sekolah tujuan invoice">
            <option value="">Pilih sekolah</option>
            @foreach ($tenantsList as $t)
                <option value="{{ $t->id }}" @selected((string) $initialTenantId === (string) $t->id)>
                    {{ $t->nama }}{{ !empty($t->domain) ? ' · '.$t->domain : '' }}
                </option>
            @endforeach
        </select>
        @error('tenant_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="jenis_pembayaran" class="mb-1.5 block text-sm font-semibold text-slate-700">Jenis Pembayaran <span class="text-rose-500">*</span></label>
        <select id="jenis_pembayaran" name="jenis_pembayaran" required class="invoice-input select2 bg-white" data-placeholder="Pilih jenis pembayaran">
            <option value="">Pilih jenis pembayaran</option>
            @foreach ($paymentTypes as $pt)
                <option value="{{ $pt }}" @selected($initialType === $pt)>{{ $pt }}</option>
            @endforeach
        </select>
        @error('jenis_pembayaran')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="status" class="mb-1.5 block text-sm font-semibold text-slate-700">Status <span class="text-rose-500">*</span></label>
        <select id="status" name="status" required class="invoice-input select2 bg-white" data-placeholder="Pilih status">
            <option value="unpaid" @selected($initialStatus === 'unpaid')>Belum Lunas</option>
            <option value="paid" @selected($initialStatus === 'paid')>Lunas</option>
        </select>
        @error('status')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror>
    </div>

    <div>
        <label for="tgl_invoice" class="mb-1.5 block text-sm font-semibold text-slate-700">Tanggal Invoice <span class="text-rose-500">*</span></label>
        <input type="text" id="tgl_invoice" name="tgl_invoice" required value="{{ $initialInvoiceDate }}" class="invoice-input datepicker" placeholder="Pilih tanggal" autocomplete="off">
        @error('tgl_invoice')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div class="sm:col-span-2">
        <label for="jumlah" class="mb-1.5 block text-sm font-semibold text-slate-700">Jumlah <span class="text-rose-500">*</span></label>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm font-semibold text-slate-400">Rp</span>
            <input type="text" id="jumlah" name="jumlah" inputmode="numeric" required value="{{ $initialAmount }}" class="invoice-input nominal pl-10" placeholder="0" autocomplete="off">
        </div>
        @error('jumlah')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
</div>
