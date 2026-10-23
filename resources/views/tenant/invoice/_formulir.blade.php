@php
    $initialInvoiceDate = old('tgl_invoice', optional($invoice->tgl_invoice)->format('Y-m-d')) ?? '';
    $initialDueDate     = old('tgl_lunas', optional($invoice->tgl_lunas)->format('Y-m-d')) ?? '';
    $initialStatus      = old('status', $invoice->status ?? 'unpaid');
    $initialType        = old('jenis_pembayaran', $invoice->jenis_pembayaran ?? '');
    $initialAmount      = old('jumlah', $invoice->jumlah ?? '');
    $initialTenantId    = old('tenant_id', $invoice->tenant_id ?? '');
    $paymentTypes       = $paymentTypes ?? \App\Models\Tenant\TenantInvoice::JENIS_PEMBAYARAN;
    $tenantsList        = $tenants ?? [];
@endphp

<div class="space-y-5">
    {{-- Sekolah --}}
    <div>
        <label for="tenant_id" class="mb-1.5 block text-sm font-semibold text-slate-700">
            {{ __('tenant.invoice.field.sekolah') }} <span class="text-rose-500">*</span>
        </label>
        <select id="tenant_id" name="tenant_id" required
                class="invoice-input select2 bg-white"
                data-placeholder="{{ __('tenant.invoice.field.sekolah_placeholder') }}">
            <option value="">{{ __('tenant.invoice.field.sekolah_placeholder') }}</option>
            @foreach ($tenantsList as $t)
                <option value="{{ $t->id }}" @selected((string) $initialTenantId === (string) $t->id)>
                    {{ $t->nama }}{{ !empty($t->domain) && $t->domain !== '—' ? ' · '.$t->domain : '' }}
                </option>
            @endforeach
        </select>
        @error('tenant_id')
            <p class="mt-1.5 flex items-start gap-1 text-xs font-medium text-rose-600">
                <svg class="mt-px h-3.5 w-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/></svg>
                <span>{{ $message }}</span>
            </p>
        @enderror
    </div>

    {{-- Jenis pembayaran --}}
    <div>
        <label for="jenis_pembayaran" class="mb-1.5 block text-sm font-semibold text-slate-700">
            {{ __('tenant.invoice.field.jenis') }} <span class="text-rose-500">*</span>
        </label>
        <select id="jenis_pembayaran" name="jenis_pembayaran" required
                class="invoice-input select2 bg-white"
                data-placeholder="{{ __('tenant.invoice.field.jenis_placeholder') }}">
            <option value="">{{ __('tenant.invoice.field.jenis_placeholder') }}</option>
            @foreach ($paymentTypes as $pt)
                <option value="{{ $pt }}" @selected($initialType === $pt)>{{ $pt }}</option>
            @endforeach
        </select>
        @error('jenis_pembayaran')
            <p class="mt-1.5 flex items-start gap-1 text-xs font-medium text-rose-600">
                <svg class="mt-px h-3.5 w-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/></svg>
                <span>{{ $message }}</span>
            </p>
        @enderror
    </div>

    {{-- Tanggal --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label for="tgl_invoice" class="mb-1.5 block text-sm font-semibold text-slate-700">
                {{ __('tenant.invoice.field.tgl_invoice') }} <span class="text-rose-500">*</span>
            </label>
            <input type="text" id="tgl_invoice" name="tgl_invoice" required
                   value="{{ $initialInvoiceDate }}"
                   class="invoice-input datepicker"
                   placeholder="{{ __('tenant.invoice.field.pilih_tanggal') }}"
                   autocomplete="off">
            @error('tgl_invoice')
                <p class="mt-1.5 flex items-start gap-1 text-xs font-medium text-rose-600">
                    <svg class="mt-px h-3.5 w-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/></svg>
                    <span>{{ $message }}</span>
                </p>
            @enderror
        </div>

        <div>
            <label for="tgl_lunas" class="mb-1.5 block text-sm font-semibold text-slate-700">
                {{ __('tenant.invoice.field.tgl_lunas') }}
            </label>
            <input type="text" id="tgl_lunas" name="tgl_lunas"
                   value="{{ $initialDueDate }}"
                   class="invoice-input datepicker"
                   placeholder="{{ __('tenant.invoice.field.tgl_lunas_hint') }}"
                   autocomplete="off">
            @error('tgl_lunas')
                <p class="mt-1.5 flex items-start gap-1 text-xs font-medium text-rose-600">
                    <svg class="mt-px h-3.5 w-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/></svg>
                    <span>{{ $message }}</span>
                </p>
            @enderror
        </div>
    </div>

    {{-- Nominal --}}
    <div>
        <label for="jumlah" class="mb-1.5 block text-sm font-semibold text-slate-700">
            {{ __('tenant.invoice.field.jumlah') }} <span class="text-rose-500">*</span>
        </label>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-sm font-semibold text-slate-400">Rp</span>
            <input type="text" id="jumlah" name="jumlah" inputmode="numeric" required
                   value="{{ $initialAmount }}"
                   class="invoice-input nominal pl-11"
                   placeholder="0"
                   autocomplete="off">
        </div>
        <p class="mt-1.5 text-xs text-slate-500">{{ __('tenant.invoice.field.jumlah_hint') }}</p>
        @error('jumlah')
            <p class="mt-1.5 flex items-start gap-1 text-xs font-medium text-rose-600">
                <svg class="mt-px h-3.5 w-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/></svg>
                <span>{{ $message }}</span>
            </p>
        @enderror
    </div>

    {{-- Status --}}
    <div>
        <span class="mb-1.5 block text-sm font-semibold text-slate-700">
            {{ __('tenant.invoice.field.status') }} <span class="text-rose-500">*</span>
        </span>
        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 bg-white px-3.5 py-3 transition hover:border-amber-300 hover:bg-amber-50/40 has-[:checked]:border-amber-400 has-[:checked]:bg-amber-50">
                <input type="radio" name="status" value="unpaid" @checked($initialStatus === 'unpaid')
                       class="h-4 w-4 border-slate-300 text-amber-500 focus:ring-amber-400">
                <span class="min-w-0">
                    <span class="block text-sm font-semibold text-slate-800">{{ __('tenant.invoice.status.unpaid') }}</span>
                    <span class="block text-[11px] text-slate-500">{{ __('tenant.invoice.status.unpaid_hint') }}</span>
                </span>
            </label>
            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 bg-white px-3.5 py-3 transition hover:border-emerald-300 hover:bg-emerald-50/40 has-[:checked]:border-emerald-400 has-[:checked]:bg-emerald-50">
                <input type="radio" name="status" value="paid" @checked($initialStatus === 'paid')
                       class="h-4 w-4 border-slate-300 text-emerald-600 focus:ring-emerald-500">
                <span class="min-w-0">
                    <span class="block text-sm font-semibold text-slate-800">{{ __('tenant.invoice.status.paid') }}</span>
                    <span class="block text-[11px] text-slate-500">{{ __('tenant.invoice.status.paid_hint') }}</span>
                </span>
            </label>
        </div>
        @error('status')
            <p class="mt-1.5 flex items-start gap-1 text-xs font-medium text-rose-600">
                <svg class="mt-px h-3.5 w-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/></svg>
                <span>{{ $message }}</span>
            </p>
        @enderror
    </div>
</div>
