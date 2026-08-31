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
        .swal-xwide { width: auto !important; max-width: min(96vw, 860px) !important; }
        .nk-table th, .nk-table td { padding: 6px 8px; font-size: 12px; text-align: left; vertical-align: middle; }
        .nk-input { width: 100%; padding: 6px 8px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px; }
        .nk-input:focus { outline: none; border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.15); }
        .nk-table { width: 100%; border-collapse: collapse; }
        .nk-table thead { background: #f8fafc; }
        .nk-table tbody tr { border-top: 1px solid #e2e8f0; }
        .nk-table .col-kode { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-weight: 600; }
        .nk-bulk { background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 12px; margin-bottom: 10px; display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
        .nk-bulk label { font-size: 12px; font-weight: 600; color: #475569; }
        .nk-bulk select { padding: 6px 8px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px; background: #fff; min-width: 220px; }
        .nk-bulk .hint { font-size: 11px; color: #64748b; }
        .nk-add-btn { color: #4f46e5; font-weight: 600; }
        .swal-narrow { width: auto !important; max-width: min(92vw, 480px) !important; }
        .nk-inline-form { text-align: left; }
        .nk-inline-form .field { margin-bottom: 10px; }
        .nk-inline-form label { display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px; }
        .nk-inline-form input, .nk-inline-form select { width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; }
        .nk-inline-form .err { color: #b91c1c; background: #fef2f2; border: 1px solid #fecaca; padding: 8px 10px; border-radius: 6px; font-size: 12px; margin-bottom: 10px; display: none; }
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

            btnImport.disabled = true;
            btnImport.querySelector('span').textContent = 'Membaca file...';

            const previewForm = new FormData();
            previewForm.append('_token', csrf);
            previewForm.append('file', fileInput.files[0]);

            fetch('{{ route('tenant.migrasi.siswa.preview') }}', {
                method: 'POST',
                body: previewForm,
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
            })
            .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
            .then(({ ok, data }) => {
                if (!ok || !data.ok) {
                    btnImport.disabled = false;
                    btnImport.querySelector('span').textContent = 'Import Sekarang';
                    Swal.fire({ icon: 'error', title: 'Gagal Membaca File', text: data.message || 'Terjadi kesalahan.' });
                    return;
                }
                const missing = data.missing_kode_kelas || [];
                if (missing.length === 0) {
                    submitImport([]);
                } else {
                    showNewKelasModal(missing, data.kurikulum_options || [], []);
                }
            })
            .catch(() => {
                btnImport.disabled = false;
                btnImport.querySelector('span').textContent = 'Import Sekarang';
                Swal.fire({ icon: 'error', title: 'Galat Jaringan', text: 'Tidak dapat terhubung ke server.' });
            });
        });

        function showNewKelasModal(missing, kurikulumOptions, prefilled) {
            const optsHtml = (selected) => ['<option value="">— Pilih Kurikulum —</option>']
                .concat(kurikulumOptions.map(o =>
                    `<option value="${escapeHtml(o.value)}" ${o.value === selected ? 'selected' : ''}>${escapeHtml(o.label)}</option>`
                ))
                .concat(['<option value="__new__">+ Tambah Kurikulum Baru…</option>'])
                .join('');
            const rows = missing.map((m, idx) => {
                const pref = prefilled.find(p => p.kode_kelas === m.kode_kelas) || {};
                const tingkatVal = pref.tingkat ?? m.tingkat ?? '';
                const kodeKurikulumVal = pref.kode_kurikulum ?? m.kode_kurikulum ?? '';
                const namaKelasVal = pref.nama_kelas ?? m.nama_kelas ?? m.kode_kelas;
                const userTouchedTingkat = pref.userTouchedTingkat === true;
                return `
                    <tr data-idx="${idx}" data-kode="${escapeHtml(m.kode_kelas)}" data-user-touched-tingkat="${userTouchedTingkat ? '1' : '0'}">
                        <td class="col-kode"><span class="font-mono">${escapeHtml(m.kode_kelas)}</span><div class="text-[10px] text-slate-500">${m.jumlah_siswa} siswa</div></td>
                        <td><input type="text" class="nk-input nk-nama" value="${escapeHtml(namaKelasVal)}" placeholder="cth: X TKJ 1"></td>
                        <td><input type="text" class="nk-input nk-tingkat" value="${escapeHtml(tingkatVal)}" placeholder="cth: X" maxlength="10"></td>
                        <td><select class="nk-input nk-kurikulum">${optsHtml(kodeKurikulumVal)}</select></td>
                    </tr>
                `;
            }).join('');

            const bulkOpts = ['<option value="">— Pilih Kurikulum —</option>']
                .concat(kurikulumOptions.map(o => `<option value="${escapeHtml(o.value)}">${escapeHtml(o.label)}</option>`))
                .concat(['<option value="__new__">+ Tambah Kurikulum Baru…</option>'])
                .join('');

            const html = `
                <div class="text-left text-sm">
                    <p class="text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 mb-3">
                        Ditemukan <strong>${missing.length}</strong> kode kelas baru di file Excel yang belum ada di tabel kelas tenant ini.
                        Isi metadata di bawah, lalu klik <strong>Buat &amp; Lanjutkan Import</strong>.
                    </p>
                    <div class="nk-bulk">
                        <label for="nk-bulk-kurikulum">Terapkan Kurikulum ke Semua Baris:</label>
                        <select id="nk-bulk-kurikulum">${bulkOpts}</select>
                        <button type="button" id="nk-bulk-apply" class="inline-flex items-center gap-1.5 rounded-md bg-amber-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-amber-600">Terapkan</button>
                        <span class="hint">Akan mengganti kurikulum untuk semua baris di bawah.</span>
                    </div>
                    <div class="overflow-auto max-h-[55vh] border border-slate-200 rounded-lg">
                        <table class="nk-table">
                            <thead class="sticky top-0">
                                <tr>
                                    <th style="width:28%">Kode Kelas</th>
                                    <th style="width:30%">Nama Kelas</th>
                                    <th style="width:14%">Tingkat</th>
                                    <th style="width:28%">Kurikulum</th>
                                </tr>
                            </thead>
                            <tbody>${rows}</tbody>
                        </table>
                    </div>
                </div>
            `;

            Swal.fire({
                title: 'Konfirmasi Kelas Baru',
                html,
                showCancelButton: true,
                showConfirmButton: true,
                confirmButtonText: 'Buat & Lanjutkan Import',
                cancelButtonText: 'Batal',
                customClass: { popup: 'swal-xwide' },
                width: 860,
                didOpen: () => {
                    const popup = Swal.getPopup();

                    // Auto-fill tingkat SELALU dari kode_kelas (token pertama sebelum '-'/'_'/'.'/whitespace).
                    // Tingkat disimpan sebagai romawi di DB (X, XI, XII) atau angka 1..12, sesuai preferensi.
                    // Non-destructive: jika user sudah pernah edit input secara manual, auto-fill di-skip.
                    popup.querySelectorAll('.nk-tingkat').forEach(inp => {
                        const tr = inp.closest('tr');
                        if (tr && tr.dataset.userTouchedTingkat === '1') return;
                        const kode = tr ? (tr.dataset.kode || '') : '';
                        inp.value = inferTingkatFromKode(kode);
                    });

                    // Tandai input sebagai "telah disentuh user" begitu ada event input manual,
                    // supaya auto-fill berikutnya (mis. setelah modal dibuka ulang) tidak menimpa.
                    popup.querySelectorAll('.nk-tingkat').forEach(inp => {
                        inp.addEventListener('input', function () {
                            const tr = inp.closest('tr');
                            if (tr) tr.dataset.userTouchedTingkat = '1';
                        });
                    });

                    // Handler: dropdown "Tambah Kurikulum Baru" di select per-baris
                    popup.querySelectorAll('.nk-kurikulum').forEach(sel => {
                        sel.addEventListener('change', function (e) {
                            if (e.target.value === '__new__') {
                                e.target.value = '';
                                openInlineKurikulumModal().then((newK) => {
                                    if (!newK) return;
                                    // Tambahkan option ke SEMUA select.nk-kurikulum (sebelum __new__), lalu pilih di select asal
                                    addKurikulumOptionToAllSelects(popup, newK);
                                    e.target.value = newK.value;
                                    e.target.dispatchEvent(new Event('change'));
                                });
                            }
                        });
                    });

                    // Handler: dropdown "Tambah Kurikulum Baru" di BULK selector — buka modal inline,
                    // lalu otomatis terapkan kurikulum baru ke semua baris.
                    const bulkSel = popup.querySelector('#nk-bulk-kurikulum');
                    const bulkBtn = popup.querySelector('#nk-bulk-apply');
                    if (bulkSel) {
                        bulkSel.addEventListener('change', function (e) {
                            if (e.target.value === '__new__') {
                                e.target.value = '';
                                openInlineKurikulumModal().then((newK) => {
                                    if (!newK) return;
                                    addKurikulumOptionToAllSelects(popup, newK);
                                    // Set value di bulk selector + otomatis terapkan ke semua baris
                                    e.target.value = newK.value;
                                    popup.querySelectorAll('.nk-kurikulum').forEach(sel => { sel.value = newK.value; });
                                });
                            }
                        });
                    }
                    if (bulkBtn && bulkSel) {
                        bulkBtn.addEventListener('click', function () {
                            const v = bulkSel.value;
                            if (!v) {
                                Swal.showValidationMessage?.('Pilih kurikulum dulu.');
                                return;
                            }
                            popup.querySelectorAll('.nk-kurikulum').forEach(sel => { sel.value = v; });
                        });
                    }
                },
                preConfirm: () => {
                    const popup = Swal.getPopup();
                    const newKelas = [];
                    let invalid = false;
                    popup.querySelectorAll('tbody tr').forEach(tr => {
                        const kode = tr.dataset.kode || '';
                        const nama = tr.querySelector('.nk-nama').value.trim();
                        const tingkat = tr.querySelector('.nk-tingkat').value.trim();
                        const kurikulum = tr.querySelector('.nk-kurikulum').value;
                        if (!nama || !tingkat || !kurikulum) {
                            invalid = true;
                            tr.style.background = '#fef2f2';
                            return;
                        }
                        tr.style.background = '';
                        newKelas.push({ kode_kelas: kode, nama_kelas: nama, tingkat, kode_kurikulum: kurikulum });
                    });
                    if (invalid) {
                        Swal.showValidationMessage('Semua kolom (Nama, Tingkat, Kurikulum) wajib diisi.');
                        return false;
                    }
                    return newKelas;
                },
            }).then(result => {
                if (result.isConfirmed) {
                    submitImport(result.value || []);
                } else {
                    btnImport.disabled = false;
                    btnImport.querySelector('span').textContent = 'Import Sekarang';
                }
            });
        }

        function inferTingkatFromKode(kode) {
            if (!kode) return '';
            const first = String(kode).split(/[-._\s]/)[0] || '';
            return first.toUpperCase().trim();
        }

        function addKurikulumOptionToAllSelects(popup, newK) {
            popup.querySelectorAll('.nk-kurikulum').forEach(sel => {
                if (sel.querySelector(`option[value="${CSS.escape(newK.value)}"]`)) return;
                const opt = document.createElement('option');
                opt.value = newK.value;
                opt.textContent = newK.label;
                // Sisipkan sebelum option "__new__"
                const newOpt = sel.querySelector('option[value="__new__"]');
                if (newOpt) sel.insertBefore(opt, newOpt);
                else sel.appendChild(opt);
            });
            // Tambahkan juga ke bulk selector
            const bulkSel = popup.querySelector('#nk-bulk-kurikulum');
            if (bulkSel && !bulkSel.querySelector(`option[value="${CSS.escape(newK.value)}"]`)) {
                const bOpt = document.createElement('option');
                bOpt.value = newK.value;
                bOpt.textContent = newK.label;
                bulkSel.appendChild(bOpt);
            }
        }

        function openInlineKurikulumModal() {
            return new Promise((resolve) => {
                let resolved = false;
                const finish = (v) => { if (!resolved) { resolved = true; resolve(v); } };

                const html = `
                    <div class="nk-inline-form">
                        <div class="err" id="nk-inline-err"></div>
                        <div class="field">
                            <label for="nk-inline-nama">Nama Kurikulum <span style="color:#e11d48">*</span></label>
                            <input type="text" id="nk-inline-nama" placeholder="Contoh: Kurikulum 2013" autocomplete="off">
                        </div>
                        <div class="field">
                            <label for="nk-inline-kode">Kode Kurikulum</label>
                            <input type="text" id="nk-inline-kode" placeholder="K13, MERDEKA" autocomplete="off">
                            <div style="font-size:11px; color:#64748b; margin-top:3px;">Opsional. Harus unik di tenant ini.</div>
                        </div>
                        <div class="field">
                            <label for="nk-inline-status">Status</label>
                            <select id="nk-inline-status">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                `;

                Swal.fire({
                    title: 'Tambah Kurikulum Baru',
                    html,
                    showCancelButton: true,
                    showConfirmButton: true,
                    confirmButtonText: 'Simpan & Pilih',
                    cancelButtonText: 'Batal',
                    customClass: { popup: 'swal-narrow' },
                    width: 480,
                    didOpen: () => {
                        setTimeout(() => document.getElementById('nk-inline-nama')?.focus(), 30);
                    },
                    preConfirm: () => {
                        const nama = document.getElementById('nk-inline-nama').value.trim();
                        const kode = document.getElementById('nk-inline-kode').value.trim();
                        const status = document.getElementById('nk-inline-status').value;
                        if (!nama) {
                            Swal.showValidationMessage('Nama kurikulum wajib diisi.');
                            return false;
                        }
                        return { nama, kode, status };
                    },
                }).then(async (result) => {
                    if (!result.isConfirmed || !result.value) {
                        finish(null);
                        return;
                    }
                    const { nama, kode, status } = result.value;
                    const body = new FormData();
                    body.append('nama_kurikulum', nama);
                    if (kode) body.append('kode_kurikulum', kode);
                    body.append('status', status);

                    try {
                        const resp = await fetch('{{ route('tenant.migrasi.siswa.preview-quick-kurikulum') }}', {
                            method: 'POST',
                            body,
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        });
                        const data = await resp.json();
                        if (!resp.ok || !data.ok) {
                            let msg = data.message || 'Gagal menambah kurikulum.';
                            if (data.errors) {
                                const flat = Object.values(data.errors).flat();
                                if (flat.length) msg = flat.join(' ');
                            }
                            Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
                            finish(null);
                            return;
                        }
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 2200 });
                        finish(data.data);
                    } catch (e) {
                        Swal.fire({ icon: 'error', title: 'Galat Jaringan', text: 'Tidak dapat terhubung ke server.' });
                        finish(null);
                    }
                });
            });
        }

        function submitImport(newKelas) {
            btnImport.disabled = true;
            btnImport.querySelector('span').textContent = 'Mengimpor...';
            const formData = new FormData();
            formData.append('_token', csrf);
            formData.append('file', fileInput.files[0]);
            const tahunId = document.getElementById('filter-tahun').value;
            const statusVal = document.getElementById('filter-status').value;
            formData.append('tahun_akademik_id', tahunId);
            formData.append('status', statusVal);
            newKelas.forEach((k, i) => {
                formData.append(`new_kelas[${i}][kode_kelas]`, k.kode_kelas);
                formData.append(`new_kelas[${i}][nama_kelas]`, k.nama_kelas);
                formData.append(`new_kelas[${i}][tingkat]`, k.tingkat);
                formData.append(`new_kelas[${i}][kode_kurikulum]`, k.kode_kurikulum);
            });

            fetch('{{ route('tenant.migrasi.siswa.import') }}', {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
            })
            .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
            .then(({ ok, data }) => {
                btnImport.disabled = false;
                btnImport.querySelector('span').textContent = 'Import Sekarang';
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
                btnImport.querySelector('span').textContent = 'Import Sekarang';
                Swal.fire({ icon: 'error', title: 'Galat Jaringan', text: 'Tidak dapat terhubung ke server.' });
            });
        }

        function escapeHtml(s) {
            return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
        }

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
