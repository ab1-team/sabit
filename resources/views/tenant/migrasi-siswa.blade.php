<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Migrasi Siswa — {{ env('APP_NAME') }} Master</title>
    <link rel="icon" type="image/png" href="{{ \App\Models\Profil::logoUrl() }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; background: #f1f5f9; }
        .select2-container { width: 100% !important; }
        .select2-container--bootstrap-5 .select2-selection { min-height: 40px; border-color: #e2e8f0; border-radius: .5rem; padding: .25rem .75rem; font-size: .875rem; }
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered { padding: .125rem 1.5rem .125rem 0; color: #1e293b; line-height: 1.75rem; }
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow { top: 8px; right: 8px; }
        .select2-container--bootstrap-5.select2-container--focus .select2-selection,
        .select2-container--bootstrap-5.select2-container--open .select2-selection { border-color: #6366f1; box-shadow: 0 0 0 4px rgba(99, 102, 241, .15); }
        .select2-container--bootstrap-5 .select2-dropdown { border-color: #e2e8f0; border-radius: .5rem; overflow: hidden; z-index: 60; }
        .select2-container--bootstrap-5 .select2-results__options { max-height: 45vh; }

        #drop-zone { -webkit-tap-highlight-color: transparent; }
        @media (max-width: 480px) {
            #drop-zone { padding-left: 1rem; padding-right: 1rem; }
        }

        .swal-wide { width: auto !important; max-width: min(92vw, 640px) !important; }
        .swal-wide ul { padding-left: 1.25rem; }
    </style>
</head>
<body class="min-h-screen text-slate-800">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('tenant.partials.bilah-atas')

    <main class="mx-auto max-w-3xl px-4 py-5 sm:px-6 sm:py-8">
        <header class="mb-5 sm:mb-6">
            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600 sm:text-sm">Tenant Console</p>
            <h2 class="mt-1 text-xl font-bold tracking-tight text-slate-900 sm:text-2xl md:text-3xl">Migrasi Siswa Baru</h2>
            <p class="mt-1 text-xs text-slate-500 sm:text-sm">Upload file Excel (.xlsx) untuk import data siswa baru secara bulk.</p>
        </header>

        @include('tenant.partials.tenant-filter')

        @if (!empty($needsTenant))
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-center shadow-sm">
                <svg class="mx-auto h-10 w-10 text-amber-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/></svg>
                <h3 class="mt-2 text-base font-bold text-amber-800">Belum ada sekolah dipilih</h3>
                <p class="mt-1 text-sm text-amber-700">Pilih sekolah dari dropdown <strong>Filter Sekolah</strong> untuk menampilkan data tahun akademik, jurusan, dan kelas.</p>
                <a href="{{ route('tenant.tenant.index') }}" class="mt-4 inline-flex items-center gap-2 rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">
                    Kelola Sekolah
                </a>
            </div>
        @else
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="min-w-0">
                    <label for="filter-tahun" class="mb-1 block text-xs font-semibold text-slate-600">Tahun Akademik <span class="text-rose-500">*</span></label>
                    <select id="filter-tahun" class="select2 w-full" data-placeholder="— Pilih Tahun Akademik —">
                        <option value="">— Pilih Tahun Akademik —</option>
                        @foreach ($tahunAkademiks as $ta)
                            <option value="{{ $ta->id }}" {{ $selectedTahun == $ta->id ? 'selected' : '' }}>{{ $ta->nama_tahun }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-0">
                    <label for="filter-status" class="mb-1 block text-xs font-semibold text-slate-600">Status Siswa</label>
                    <select id="filter-status" class="select2 w-full" data-placeholder="Pilih status siswa">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                        <option value="blokir">Blokir</option>
                    </select>
                </div>
            </div>

            <div class="mt-5 flex flex-col gap-3 rounded-lg bg-slate-50 px-4 py-3 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-slate-700">Belum punya formatnya?</p>
                    <p class="mt-0.5 text-[11px] text-slate-500">Download template Excel yang sudah berisi header + 1 baris contoh.</p>
                </div>
                <a href="{{ route('tenant.migrasi.siswa.template') }}" class="inline-flex w-full shrink-0 items-center justify-center gap-2 rounded-lg border border-indigo-200 bg-white px-3 py-2 text-xs font-semibold text-indigo-700 hover:bg-indigo-50 sm:w-auto">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
                    Download Template
                </a>
            </div>

            <div id="drop-zone" class="mt-5 cursor-pointer rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center hover:border-indigo-400 hover:bg-indigo-50 transition sm:px-6 sm:py-10">
                <p class="text-sm font-semibold text-slate-700">Klik atau seret file Excel ke sini</p>
                <p class="mt-1 text-xs text-slate-400">Format: .xlsx, .xls, .csv (maks 10 MB)</p>
                <p id="file-name" class="mt-3 hidden break-all px-2 text-xs font-semibold text-indigo-600"></p>
                <input type="file" id="file-input" accept=".xlsx,.xls,.csv" class="hidden">
            </div>

            <div class="mt-5 flex flex-col gap-2 sm:flex-row sm:justify-end">
                <button id="btn-reset" type="button" class="order-2 inline-flex w-full items-center justify-center gap-1.5 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 sm:order-1 sm:w-auto sm:py-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Atur Ulang
                </button>
                <button id="btn-import" type="button" class="order-1 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700 disabled:opacity-50 sm:order-2 sm:w-auto sm:py-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <span>Import Sekarang</span>
                </button>
            </div>
        </div>
        @endif

        <p class="mt-8 text-center text-xs text-slate-400">&copy; {{ date('Y') }} {{ env('APP_NAME') }}</p>
    </main>

    <script>
        const csrf = '{{ csrf_token() }}';
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('file-input');
        const fileName = document.getElementById('file-name');
        const btnImport = document.getElementById('btn-import');
        const btnReset = document.getElementById('btn-reset');

        function resetForm() {
            fileInput.value = '';
            fileName.textContent = '';
            fileName.classList.add('hidden');
            btnImport.disabled = false;
        }

        dropZone.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', () => {
            if (fileInput.files.length) {
                fileName.textContent = 'File: ' + fileInput.files[0].name;
                fileName.classList.remove('hidden');
            }
        });
        btnReset.addEventListener('click', resetForm);

        btnImport.addEventListener('click', function () {
            const tahunId = document.getElementById('filter-tahun').value;
            if (!tahunId) {
                Swal.fire({ icon: 'warning', title: 'Pilih Tahun Akademik', text: 'Tahun akademik harus dipilih.' });
                return;
            }
            if (!fileInput.files.length) {
                Swal.fire({ icon: 'warning', title: 'Pilih File', text: 'Silakan pilih file Excel terlebih dahulu.' });
                return;
            }

            const formData = new FormData();
            formData.append('_token', csrf);
            formData.append('file', fileInput.files[0]);
            formData.append('tahun_akademik_id', tahunId);
            formData.append('status', document.getElementById('filter-status').value);

            btnImport.disabled = true;

            fetch('{{ route('tenant.migrasi.siswa.import') }}', {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
            })
            .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
            .then(({ ok, data }) => {
                btnImport.disabled = false;
                if (ok && data.ok) {
                    let html = (data.message || 'File berhasil diupload.');
                    if (data.failures && data.failures.length) {
                        const list = data.failures.slice(0, 10).map(f =>
                            `<li class="text-left text-xs">Baris ${f.row} (${f.nama || '-'}): ${(f.errors || []).join(', ')}</li>`
                        ).join('');
                        html += `<ul class="mt-2 list-disc pl-5">${list}${data.failures.length > 10 ? `<li class="text-left text-xs">...dan ${data.failures.length - 10} baris lainnya</li>` : ''}</ul>`;
                    }
                    Swal.fire({
                        icon: data.summary && data.summary.failed > 0 ? 'warning' : 'success',
                        title: data.summary && data.summary.failed > 0 ? 'Selesai dengan catatan' : 'Berhasil',
                        html: html,
                        customClass: { popup: 'swal-wide' },
                    });
                    if (!data.summary || data.summary.failed === 0) {
                        resetForm();
                    }
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Terjadi kesalahan.' });
                    }
                })
                .catch(() => {
                    btnImport.disabled = false;
                    Swal.fire({ icon: 'error', title: 'Galat Jaringan', text: 'Tidak dapat terhubung ke server.' });
                });
        });

        document.getElementById('filter-tahun').addEventListener('change', function () {
            const params = new URLSearchParams(window.location.search);
            params.set('tahun_akademik_id', this.value);
            window.location.search = params.toString();
        });

        $(function () {
            $('#filter-tahun').select2({ theme: 'bootstrap-5', width: '100%', placeholder: '— Pilih Tahun Akademik —', allowClear: true });
            $('#filter-status').select2({ theme: 'bootstrap-5', width: '100%', placeholder: 'Pilih status siswa', allowClear: false, minimumResultsForSearch: Infinity });
        });
    </script>

    @if (session('success'))
        <script>Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: @json(session('success')), showConfirmButton: false, timer: 3000 });</script>
    @endif
    @if (session('error'))
        <script>Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: @json(session('error')), showConfirmButton: false, timer: 3000 });</script>
    @endif
</body>
</html>
