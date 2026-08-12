@php
    $initialNama = old('nama', $userItem->nama ?? '');
    $initialUsername = old('username', $userItem->username ?? '');
    $initialEmail = old('email', $userItem->email ?? '');
    $initialTelepon = old('telepon', $userItem->telepon ?? '');
@endphp

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:col-span-2">
    <div class="sm:col-span-2">
        <label for="nama" class="mb-1.5 block text-sm font-semibold text-slate-700">Nama <span class="text-rose-500">*</span></label>
        <input type="text" id="nama" name="nama" required value="{{ $initialNama }}" class="invoice-input" placeholder="Nama lengkap operator" autocomplete="off">
        @error('nama')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    @if ($showUsername)
    <div>
        <label for="username" class="mb-1.5 block text-sm font-semibold text-slate-700">Nama Pengguna <span class="text-rose-500">*</span></label>
        <input type="text" id="username" name="username" required value="{{ $initialUsername }}" class="invoice-input font-mono" placeholder="operator_sekolah" autocomplete="off">
        @error('username')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
    @endif

    @if ($showPassword)
    <div>
        <label for="password" class="mb-1.5 block text-sm font-semibold text-slate-700">Kata Sandi <span class="text-rose-500">*</span></label>
        <input type="password" id="password" name="password" required minlength="6" value="" class="invoice-input" placeholder="Minimal 6 karakter" autocomplete="new-password">
        @error('password')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
    @endif

    <div>
        <label for="email" class="mb-1.5 block text-sm font-semibold text-slate-700">Email</label>
        <input type="email" id="email" name="email" value="{{ $initialEmail }}" class="invoice-input" placeholder="opsional" autocomplete="off">
        @error('email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="telepon" class="mb-1.5 block text-sm font-semibold text-slate-700">Telepon</label>
        <input type="text" id="telepon" name="telepon" value="{{ $initialTelepon }}" class="invoice-input" placeholder="opsional" autocomplete="off">
        @error('telepon')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>
</div>
