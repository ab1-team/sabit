@php
    $tenantId   = old('id',   $tenant->id   ?? '');
    $tenantName = old('nama_sekolah', $tenant->nama_sekolah  ?? '');
    $tenantDomain = old('domain', optional($tenant->domains->first())->domain ?? '');
    $tenantEmail  = old('email', $tenant->email ?? '');
    $isEdit = !empty($tenant->id);
@endphp

<div class="sm:col-span-2">
    <label for="tenant-nama" class="mb-1.5 block text-sm font-semibold text-slate-700">Nama Sekolah <span class="text-rose-500">*</span></label>
    <input type="text" id="tenant-nama" name="nama_sekolah" required value="{{ $tenantName }}" class="invoice-input" placeholder="SMA Bina Jaya" autocomplete="off">
    @error('nama_sekolah')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
</div>

<div class="sm:col-span-2">
    <label for="tenant-id" class="mb-1.5 block text-sm font-semibold text-slate-700">ID Tenant <span class="text-rose-500">*</span></label>
    <input type="text" id="tenant-id" name="id" value="{{ $tenantId }}" {{ $isEdit ? 'readonly' : 'required' }} pattern="^[a-z0-9-]+$" class="invoice-input {{ $isEdit ? 'cursor-not-allowed border-slate-200 bg-slate-50 font-mono text-slate-700 focus:border-slate-200 focus:shadow-none' : 'font-mono' }}" placeholder="contoh: sma-bina-jaya" autocomplete="off">
    @if ($isEdit)
        <p class="mt-1 text-xs text-slate-500">ID tenant tidak dapat diubah.</p>
    @else
        <p class="mt-1 text-xs text-slate-500">Akan jadi prefix database: <span class="font-mono">tenant&lt;id&gt;</span></p>
    @endif
    @error('id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
</div>

<div>
    <label for="tenant-domain" class="mb-1.5 block text-sm font-semibold text-slate-700">Domain <span class="text-rose-500">*</span></label>
    <input type="text" id="tenant-domain" name="domain" required value="{{ $tenantDomain }}" pattern="^[a-z0-9.-]+$" class="invoice-input font-mono" placeholder="contoh: sma-bina-jaya.local.test" autocomplete="off">
    @error('domain')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
</div>

<div>
    <label for="tenant-email" class="mb-1.5 block text-sm font-semibold text-slate-700">Email Admin</label>
    <input type="email" id="tenant-email" name="email" value="{{ $tenantEmail }}" class="invoice-input" placeholder="admin@binajaya.sch.id" autocomplete="off">
    @error('email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
</div>