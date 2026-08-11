<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Sekolah — Tenant {{ env('APP_NAME') }}</title>
    <link rel="icon" type="image/png" href="{{ \App\Models\Profil::logoUrl() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>html, body { font-family: 'Inter', system-ui, sans-serif; } body { background:#f8fafc; }</style>
</head>
<body class="min-h-screen text-slate-800">
    @include('tenant.partials.bilah-atas')

    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <header class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-indigo-600">Tenant Console</p>
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Profil Sekolah</h2>
                <p class="mt-1 text-sm text-slate-500">Identitas & kontak lembaga untuk tenant {{ $tenant->id }}.</p>
            </div>
        </header>

        @include('tenant.partials.tenant_subnav', ['tenant' => $tenant, 'active' => 'profil'])

        <form method="POST" action="{{ route('tenant.tenant.profil.update', $tenant) }}" class="mt-6 space-y-5 rounded-2xl border border-slate-200 bg-white shadow-sm">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Nama Sekolah</label>
                    <input type="text" name="nama" value="{{ old('nama', $profil->nama ?? '') }}" required class="mt-1 block w-full rounded-lg border-slate-200 text-sm shadow-sm focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                    @error('nama') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Email</label>
                    <input type="email" name="email" value="{{ old('email', $profil->email ?? '') }}" class="mt-1 block w-full rounded-lg border-slate-200 text-sm shadow-sm focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                    @error('email') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Telepon</label>
                    <input type="text" name="telpon" value="{{ old('telpon', $profil->telpon ?? '') }}" class="mt-1 block w-full rounded-lg border-slate-200 text-sm shadow-sm focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                    @error('telpon') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Tanggal Jatuh Tempo (1-31)</label>
                    <input type="number" name="jatuh_tempo" min="1" max="31" value="{{ old('jatuh_tempo', $profil->jatuh_tempo ?? 10) }}" class="mt-1 block w-full rounded-lg border-slate-200 text-sm shadow-sm focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                    @error('jatuh_tempo') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Alamat</label>
                <textarea name="alamat" rows="3" class="mt-1 block w-full rounded-lg border-slate-200 text-sm shadow-sm focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">{{ old('alamat', $profil->alamat ?? '') }}</textarea>
                @error('alamat') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-end gap-2 pt-4">
                <a href="{{ route('tenant.tenant.index') }}" class="inline-flex items-center rounded-lg bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-200">Batal</a>
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Simpan
                </button>
            </div>
        </form>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if (session('success'))
        <script>Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: @json(session('success')), showConfirmButton: false, timer: 3000, timerProgressBar: true });</script>
    @endif
    @if (session('error'))
        <script>Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: @json(session('error')), showConfirmButton: false, timer: 3000, timerProgressBar: true });</script>
    @endif
</body>
</html>
