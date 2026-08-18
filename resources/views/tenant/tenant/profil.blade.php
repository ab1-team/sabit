<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Sekolah — {{ env('APP_NAME') }}</title>
    <link rel="icon" type="image/png" href="{{ \App\Models\Profil::logoUrl() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    @include('tenant.partials._fancy_inputs_head')
    <style>
        html, body { font-family: 'Inter', system-ui, sans-serif; }
        body { -webkit-tap-highlight-color: transparent; background: #f8fafc; }
        .invoice-input { width: 100%; min-height: 44px; border: 1px solid #cbd5e1; border-radius: .75rem; background: #fff; padding: .625rem .875rem; font-size: .875rem; color: #1e293b; transition: border-color .15s ease, box-shadow .15s ease; }
        .invoice-input::placeholder { color: #94a3b8; }
        .invoice-input:focus { border-color: #6366f1; outline: none; box-shadow: 0 0 0 4px rgba(99, 102, 241, .15); }
        textarea.invoice-input { min-height: 96px; }
    </style>
</head>
<body class="min-h-screen text-slate-800">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('tenant.partials.bilah-atas')

    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <header class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-indigo-600">Tenant Console</p>
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Profil Sekolah</h2>
                <p class="mt-1 text-sm text-slate-500">Identitas & kontak lembaga.</p>
            </div>
            <a href="{{ route('tenant.tenant.index') }}" title="Kembali ke data tenant" aria-label="Kembali ke data tenant" class="inline-flex h-10 w-10 items-center justify-center self-end rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100 sm:h-auto sm:w-auto sm:gap-2 sm:self-auto sm:px-4 sm:py-2.5 sm:text-sm sm:font-semibold">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                <span class="sr-only sm:not-sr-only sm:inline">Kembali ke data tenant</span>
            </a>
        </header>

        @include('tenant.partials.tenant_subnav', ['tenant' => $tenant, 'active' => 'profil'])

        <form method="POST" action="{{ route('tenant.tenant.profil.update', $tenant) }}" class="mt-6 space-y-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            @csrf
            @method('PUT')
            @include('tenant.tenant._formulir_profil', ['profilItem' => $profil])

            <div class="flex flex-col-reverse gap-2 border-t border-slate-100 pt-4 sm:flex-row sm:justify-end">
                <button type="submit" id="submit-profil" class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200 sm:w-auto">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span id="submit-label">Simpan</span>
                </button>
            </div>
        </form>
    </main>

    <script>
        const profilForm = document.querySelector('form[action*="profil"]');
        if (profilForm) {
            profilForm.addEventListener('submit', function () {
                const btn = document.getElementById('submit-profil');
                btn.disabled = true;
                btn.classList.add('opacity-60', 'cursor-not-allowed');
                document.getElementById('submit-label').textContent = 'Menyimpan…';
            });
        }
    </script>

    @if (session('success'))
        <script>Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: @json(session('success')), showConfirmButton: false, timer: 3000, timerProgressBar: true });</script>
    @endif
    @if (session('error'))
        <script>Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: @json(session('error')), showConfirmButton: false, timer: 3000, timerProgressBar: true });</script>
    @endif
</body>
</html>
