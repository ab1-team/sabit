@php
    $initialModalOpen = $errors->any();
    $summary = $summary ?? ['total' => 0, 'paid' => 0, 'unpaid' => 0, 'overdue' => 0, 'nominal' => 0, 'nominal_unpaid' => 0];
    $rekenings = $rekenings ?? [];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('tenant.invoice.title') }} — {{ env('APP_NAME') }}</title>
    <link rel="icon" type="image/png" href="{{ \App\Models\Profil::logoUrl() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    @include('tenant.partials._fancy_inputs_head')
    <style>
        html, body { font-family: 'Inter', system-ui, sans-serif; }
        body { -webkit-tap-highlight-color: transparent; background: #f8fafc; }
        .invoice-input { width: 100%; min-height: 44px; border: 1px solid #cbd5e1; border-radius: .75rem; background: #fff; padding: .625rem .875rem; font-size: .875rem; color: #1e293b; transition: border-color .15s ease, box-shadow .15s ease; }
        .invoice-input::placeholder { color: #94a3b8; }
        .invoice-input:focus { border-color: #6366f1; outline: none; box-shadow: 0 0 0 4px rgba(99, 102, 241, .15); }
        .invoice-input:disabled, .invoice-input[readonly] { background: #f8fafc; color: #475569; }
        .modal-scroll { max-height: calc(100vh - 2rem); overflow-y: auto; }

        /* Select2 harus berada di atas layer modal (z-50) */
        .select2-container { width: 100% !important; }
        .select2-container--open { z-index: 1080 !important; }
        .select2-container--bootstrap-5 .select2-selection { min-height: 44px; border-color: #cbd5e1; border-radius: .75rem; padding: .375rem .875rem; font-size: .875rem; box-shadow: none; }
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered { padding: .25rem 1.5rem .25rem 0; color: #1e293b; line-height: 1.75rem; }
        /* Teks select2 tepat di tengah kotak (vertikal).
           Tanpa flex + align-items:center, baris teks dengan line-height
           1.75rem (28px) melebihi tinggi kotak (40px - padding) sehingga
           teks terdorong ke bawah dan terlihat "kurang ke atas". */
        .select2-container--bootstrap-5 .select2-selection--single { display: flex; align-items: center; }
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered { flex: 1 1 auto; min-width: 0; }
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow { top: 9px; right: 10px; }
        .select2-container--bootstrap-5.select2-container--focus .select2-selection,
        .select2-container--bootstrap-5.select2-container--open .select2-selection { border-color: #6366f1; box-shadow: 0 0 0 4px rgba(99, 102, 241, .15); }
        .select2-container--bootstrap-5 .select2-dropdown { border-color: #cbd5e1; border-radius: .75rem; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(15, 23, 42, .18); }
        .select2-container--bootstrap-5 .select2-search__field:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99, 102, 241, .15) !important; }
        .select2-container--bootstrap-5 .select2-results__option--highlighted[aria-selected] { background: #eef2ff; color: #4338ca; }
        .select2-container--bootstrap-5 .select2-results__option[aria-selected="true"] { background: #eef2ff; color: #4338ca; font-weight: 600; }
        /* Tema select2-bootstrap-5 memberi opsi terpilih warna biru (#0d6efd).
           Samakan dengan tema indigo halaman ini. */
        .select2-container--bootstrap-5 .select2-dropdown .select2-results__options .select2-results__option.select2-results__option--selected,
        .select2-container--bootstrap-5 .select2-dropdown .select2-results__options .select2-results__option[aria-selected="true"]:not(.select2-results__option--highlighted) { background: #eef2ff !important; color: #4338ca !important; font-weight: 600; }
        .select2-container--bootstrap-5 .select2-dropdown .select2-results__options .select2-results__option.select2-results__option--highlighted { background: #e0e7ff !important; color: #3730a3 !important; }
        .select2-container--bootstrap-5 .select2-selection__clear { margin-right: 1.6rem; }

        /* Flatpickr berada di atas modal */
        .flatpickr-calendar { z-index: 1090 !important; box-shadow: 0 10px 30px rgba(15, 23, 42, .16); }
        .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange { background: #4f46e5; border-color: #4f46e5; }
        .flatpickr-day.today { border-color: #6366f1; }
        .flatpickr-months .flatpickr-month, .flatpickr-current-month .flatpickr-monthDropdown-months { color: #1e293b; }

        .nominal { letter-spacing: .01em; }

        /* Kartu statistik */
        .stat-card { border-radius: 1rem; border: 1px solid transparent; box-shadow: 0 10px 25px -12px rgba(15, 23, 42, .35); }
        .stat-violet { background: linear-gradient(135deg, #6366f1 0%, #7c3aed 55%, #9333ea 100%); }
        .stat-emerald { background: linear-gradient(135deg, #059669 0%, #10b981 55%, #34d399 100%); }
        .stat-amber { background: linear-gradient(135deg, #d97706 0%, #f59e0b 55%, #fbbf24 100%); }
        .stat-rose { background: linear-gradient(135deg, #e11d48 0%, #f43f5e 55%, #fb7185 100%); }
        .stat-icon { width: 38px; height: 38px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; background: rgba(255,255,255,.22); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); }

        /* Filter bar */
        .filter-select { min-width: 0; }
        .filter-select + .select2-container { width: 100% !important; }
        .filter-select + .select2-container .select2-selection { min-height: 40px; }
        /* CATATAN: jangan memberi `width: 100% !important` pada
           `.filter-select.select2-hidden-accessible`. Select2 menyembunyikan
           <select> asli dengan `position:absolute; width:1px !important`.
           Override itu membuat select tersembunyi selebar viewport sehingga
           halaman ikut ter-scroll horizontal. */

        /* Tabel */
        .table-wrap { overflow-x: auto; }
        #invoices { width: 100% !important; }
        #invoices thead th { background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%); border-bottom: 1px solid #e2e8f0; color: #475569; font-weight: 600; font-size: .7rem; letter-spacing: .06em; text-transform: uppercase; padding: .8rem 1rem; white-space: nowrap; }
        #invoices tbody td { padding: .875rem 1rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        #invoices tbody tr:last-child td { border-bottom: 0; }
        #invoices tbody tr { transition: background-color .15s ease; }
        #invoices tbody tr:hover { background-color: #f8fafc; }

        /* Kolom kontrol detail (Responsive "column" type).
           Selector memakai #invoices agar menang atas
           `table.dataTable.dtr-column>tbody>tr>td.control:before`. */
        #invoices thead th.control { padding-left: 1rem; padding-right: 1rem; text-align: center; }
        #invoices tbody td.control { padding: .875rem 1rem !important; text-align: center; cursor: pointer; }
        #invoices tbody td.control::before {
            content: '';
            display: inline-block;
            width: .5rem;
            height: .5rem;
            box-sizing: border-box;
            border-right: 2px solid #6366f1;
            border-bottom: 2px solid #6366f1;
            transform: rotate(45deg);
            transition: transform .18s ease;
            vertical-align: middle;
        }
        #invoices tbody tr.parent td.control::before { transform: rotate(-135deg); }
        #invoices .dtr-details { padding: 0.5rem 0; }
        #invoices .dtr-details > li { padding: 0.5rem 1rem; border-bottom: 1px solid #f1f5f9; display: flex; gap: 0.75rem; align-items: flex-start; }
        #invoices .dtr-details > li:last-child { border-bottom: 0; }
        #invoices .dtr-details .dtr-title { font-weight: 600; color: #64748b; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.04em; min-width: 7rem; padding-top: 0.125rem; }
        #invoices .dtr-details .dtr-data { color: #1e293b; font-size: 0.875rem; word-break: break-word; }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter { margin-bottom: .75rem; }
        .dataTables_wrapper .dataTables_length label,
        .dataTables_wrapper .dataTables_filter label { color: #475569; font-size: .8125rem; font-weight: 500; display: inline-flex; align-items: center; gap: .5rem; }
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input { border: 1px solid #cbd5e1 !important; border-radius: .5rem !important; padding: .375rem .625rem !important; font-size: .875rem !important; color: #1e293b; background: #fff; }
        .dataTables_wrapper .dataTables_length select:focus,
        .dataTables_wrapper .dataTables_filter input:focus { border-color: #6366f1 !important; outline: none !important; box-shadow: 0 0 0 3px rgba(99, 102, 241, .15) !important; }
        .dataTables_wrapper .dataTables_filter input { min-width: 220px; }
        .dataTables_wrapper .dataTables_info { color: #64748b !important; font-size: .8125rem !important; padding-top: .75rem; }
        .dataTables_wrapper .dataTables_paginate { padding-top: .75rem; }
        .dataTables_wrapper .dataTables_paginate .paginate_button { box-sizing: border-box; display: inline-flex; align-items: center; justify-content: center; min-width: 2.25rem; height: 2.25rem; padding: 0 .65rem !important; margin: 0 2px !important; border-radius: .5rem !important; border: 1px solid #e2e8f0 !important; background: #fff !important; color: #475569 !important; font-weight: 600; font-size: .8125rem; cursor: pointer; transition: all .15s ease; }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled):not(.current) { background: #eef2ff !important; border-color: #c7d2fe !important; color: #4f46e5 !important; }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #4f46e5 !important; border-color: #4f46e5 !important; color: #fff !important; box-shadow: 0 1px 2px rgba(79, 70, 229, .25); }
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled { color: #cbd5e1 !important; background: #f8fafc !important; border-color: #f1f5f9 !important; cursor: not-allowed; }
        .dataTables_wrapper .dataTables_processing { background: rgba(255, 255, 255, .85) !important; border: 1px solid #e2e8f0 !important; border-radius: .75rem !important; color: #475569 !important; font-weight: 600 !important; box-shadow: 0 10px 25px rgba(15, 23, 42, .08); }
        .dataTables_empty { padding: 3rem 1rem !important; color: #94a3b8 !important; font-size: .875rem; }

        @media (max-width: 1024px) {
            /* Rapatkan padding tabel agar kolom Aksi tidak terpotong pada lebar
               tablet. Tombol Aksi juga menjadi ikon-saja karena labelnya memakai
               `hidden lg:inline` di markup kolom `action`. */
            #invoices thead th, #invoices tbody td { padding-left: .625rem !important; padding-right: .625rem !important; }
            #invoices tbody td.control { padding-left: .5rem !important; padding-right: .5rem !important; }
            #invoices tbody td.control::before { left: .25rem; }
            #invoices tbody td:last-child > div { gap: .25rem; }
        }
        @media (max-width: 640px) {
            .dataTables_wrapper .dataTables_filter { float: none; text-align: left; }
            .dataTables_wrapper .dataTables_length { float: none; text-align: left; }
            .dataTables_wrapper .dataTables_filter input { width: 100%; min-width: 0; margin-left: 0; }
            .dataTables_wrapper .dataTables_paginate .paginate_button { min-width: 2rem; height: 2rem; font-size: .75rem; padding: 0 .5rem !important; margin: 0 1px !important; }
            #invoices thead th, #invoices tbody td { padding-left: .5rem !important; padding-right: .5rem !important; }
            #invoices tbody td.control { padding-left: .5rem !important; padding-right: .5rem !important; }
            #invoices tbody td.control::before { left: .25rem; }
        }
        @media (max-width: 480px) {
            #invoices { font-size: .75rem; }
            /* Judul kolom lebih rapat supaya tabel muat di layar ponsel sempit. */
            #invoices thead th { font-size: .5625rem; letter-spacing: .02em; }
            #invoices thead th, #invoices tbody td { padding-left: .375rem !important; padding-right: .375rem !important; }
            #invoices tbody td { padding-top: .625rem !important; padding-bottom: .625rem !important; }
            #invoices tbody td.control { padding-left: .375rem !important; padding-right: .375rem !important; }
            /* Nomor baris tidak informatif di layar sangat kecil. Disembunyikan lewat CSS
               (bukan kelas breakpoint Responsive) karena cukup disembunyikan di bawah 480px;
               `!important` diperlukan karena Responsive menulis `display` inline. */
            #invoices th.col-no, #invoices tbody td.col-no { display: none !important; }
            /* Badge status dirapatkan dan teksnya dikecilkan. */
            #invoices tbody td.col-status > span { gap: .25rem; padding: .25rem .375rem; font-size: .625rem; }
            #invoices tbody td.col-status > span > span:first-child { width: .375rem; height: .375rem; }
            /* Angka rupiah diperkecil sedikit; min-width kolom ini cukup untuk
               "Rp 1.234.500" tanpa membuat tabel meluber. */
            #invoices tbody td.col-amount { font-size: .6875rem; }
            /* Di layar sangat kecil semua tombol aksi dibuat berbentuk kotak ikon
               (bukan disembunyikan) agar kolom Aksi tetap muat tanpa
               menghilangkan functionality. */
            #invoices tbody td:last-child > div { gap: .25rem; }
            #invoices tbody td:last-child .print-invoice,
            #invoices tbody td:last-child .delete-invoice,
            #invoices tbody td:last-child .pay-invoice,
            #invoices tbody td:last-child > div > span[aria-disabled="true"] { box-sizing: border-box; width: 1.75rem; height: 1.75rem; padding: 0; justify-content: center; }
        }
    </style>

    {{-- Gaya bersama tabel DataTables (grid + paginasi) — harus SETELAH <style> di atas
         supaya aturan partial (pakai !important) yang menang. --}}
    @include('tenant.partials._datatables')
</head>
<body class="min-h-screen text-slate-800">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('tenant.partials.bilah-atas')

    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        {{-- Header --}}
        <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600 sm:text-sm">{{ __('tenant.app.name') }}</p>
                <h2 class="mt-1 text-xl font-bold tracking-tight text-slate-900 sm:text-2xl md:text-3xl">{{ __('tenant.invoice.title') }}</h2>
                <p class="mt-1.5 text-xs text-slate-500 sm:text-sm">{{ __('tenant.invoice.subtitle') }}</p>
            </div>
            <button type="button" id="add-invoice" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-sm shadow-indigo-600/20 transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200 sm:w-auto sm:py-2.5">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                {{ __('tenant.invoice.add') }}
            </button>
        </header>

        {{-- Kartu ringkasan --}}
        <section class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <article class="stat-card stat-violet flex items-center gap-3.5 px-4 py-4 text-white">
                <div class="stat-icon">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a2 2 0 01.707.293l5.414 5.414A2 2 0 0121 10v8a2 2 0 01-2 2z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="truncate text-[11px] font-semibold uppercase tracking-wider opacity-90">{{ __('tenant.invoice.stats.total') }}</p>
                    <p class="truncate text-2xl font-bold leading-tight">{{ number_format($summary['total']) }}</p>
                    <p class="truncate text-[11px] leading-tight opacity-90">{{ __('tenant.invoice.stats.total_hint') }}</p>
                </div>
            </article>

            <article class="stat-card stat-emerald flex items-center gap-3.5 px-4 py-4 text-white">
                <div class="stat-icon">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="truncate text-[11px] font-semibold uppercase tracking-wider opacity-90">{{ __('tenant.invoice.stats.paid') }}</p>
                    <p class="truncate text-2xl font-bold leading-tight">{{ number_format($summary['paid']) }}</p>
                    <p class="truncate text-[11px] leading-tight opacity-90">{{ __('tenant.invoice.stats.paid_hint') }}</p>
                </div>
            </article>

            <article class="stat-card stat-amber flex items-center gap-3.5 px-4 py-4 text-white">
                <div class="stat-icon">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="truncate text-[11px] font-semibold uppercase tracking-wider opacity-90">{{ __('tenant.invoice.stats.unpaid') }}</p>
                    <p class="truncate text-2xl font-bold leading-tight">{{ number_format($summary['unpaid']) }}</p>
                    <p class="truncate text-[11px] leading-tight opacity-90">Rp {{ number_format($summary['nominal_unpaid'], 0, ',', '.') }}</p>
                </div>
            </article>

            <article class="stat-card stat-rose flex items-center gap-3.5 px-4 py-4 text-white">
                <div class="stat-icon">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="truncate text-[11px] font-semibold uppercase tracking-wider opacity-90">{{ __('tenant.invoice.stats.overdue') }}</p>
                    <p class="truncate text-2xl font-bold leading-tight">{{ number_format($summary['overdue']) }}</p>
                    <p class="truncate text-[11px] leading-tight opacity-90">{{ __('tenant.invoice.stats.overdue_hint') }}</p>
                </div>
            </article>
        </section>

        {{--
            Filter sekolah + filter status (Lunas / Belum Lunas).
            Kedua filter diletakkan pada satu panel supaya sederhana.
            Filter status mengirim nilainya lewat AJAX ke tabel (bukan reload
            halaman), sedangkan pilihan sekolah tetap memakai navigasi GET.
        --}}
        @include('tenant.partials.tenant-filter', [
            'statusFilterOptions' => [
                ['value' => '__all__', 'label' => __('tenant.invoice.filter.status_all')],
                ['value' => 'unpaid', 'label' => __('tenant.invoice.status.unpaid')],
                ['value' => 'paid', 'label' => __('tenant.invoice.status.paid')],
            ],
            'statusFilterValue' => $statusFilterValue ?? '__all__',
            'statusFilterLabel' => __('tenant.invoice.filter.status'),
        ])

        {{-- Tabel --}}
        <section class="mt-4 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-1 border-b border-slate-100 px-4 py-3.5 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">{{ __('tenant.invoice.title') }}</h3>
                    <p class="mt-0.5 text-xs text-slate-500">Rp {{ number_format($summary['nominal'], 0, ',', '.') }} — {{ __('tenant.invoice.stats.total') }}</p>
                </div>
            </div>
            <div class="table-wrap px-3 py-3 sm:px-5 sm:py-4">
                {{--
                    Responsive (DataTables Responsive 2.5):
                    - `all`         → kolom selalu tampil di semua breakpoint
                    - `min-tablet-p`→ tampil pada tablet-p (≥768px) & lebih lebar
                    - `desktop`     → hanya tampil pada desktop (>1024px)
                    Kolom yang disembunyikan tetap bisa dibaca lewat baris detail
                    (ikon chevron pada kolom kontrol).

                    PENTING: jumlah <th> HARUS sama dengan jumlah entri `columns`
                    pada konfigurasi DataTables di bawah, karena DataTables
                    memetakan kolom ke <th> berdasarkan indeks.
                --}}
                <table id="invoices" class="table align-items-center mb-0 w-full text-sm text-slate-700">
                    <thead>
                        <tr>
                            <th class="control" aria-label="{{ __('tenant.invoice.col.detail') }}"></th>
                            <th class="all px-3 py-3 text-left font-semibold"><span class="hidden sm:inline">{{ __('tenant.invoice.col.no') }}</span><span class="sm:hidden">#</span></th>
                            <th class="all px-3 py-3 text-left font-semibold"><span class="hidden sm:inline">{{ __('tenant.invoice.col.jenis') }}</span><span class="sm:hidden">{{ __('tenant.invoice.col.jenis_short') }}</span></th>
                            <th class="min-tablet-p px-3 py-3 text-left font-semibold">{{ __('tenant.invoice.col.sekolah') }}</th>
                            <th class="desktop px-3 py-3 text-left font-semibold">{{ __('tenant.invoice.col.pemilik') }}</th>
                            <th class="desktop px-3 py-3 text-left font-semibold">{{ __('tenant.invoice.col.tgl_invoice') }}</th>
                            <th class="min-tablet-l px-3 py-3 text-left font-semibold"><span class="hidden sm:inline">{{ __('tenant.invoice.col.tgl_lunas') }}</span><span class="sm:hidden">{{ __('tenant.invoice.col.due_short') }}</span></th>
                            <th class="all px-3 py-3 text-right font-semibold">{{ __('tenant.invoice.col.jumlah') }}</th>
                            <th class="all px-3 py-3 text-center font-semibold">{{ __('tenant.invoice.col.status') }}</th>
                            <th class="all px-3 py-3 text-center font-semibold">{{ __('tenant.invoice.col.aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </section>
    </main>

    {{-- Modal tambah invoice --}}
    <div id="invoice-modal" class="{{ $initialModalOpen ? 'flex' : 'hidden' }} fixed inset-0 z-50 items-center justify-center bg-slate-900/50 p-2 backdrop-blur-sm sm:p-4" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <div class="modal-scroll w-full max-w-2xl max-h-[95vh] overflow-y-auto rounded-2xl bg-white shadow-2xl">
            <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-5 py-4 sm:px-6">
                <div>
                    <h3 id="modal-title" class="text-lg font-bold text-slate-900">{{ __('tenant.invoice.modal.create_title') }}</h3>
                    <p id="modal-description" class="mt-1 text-sm text-slate-500">{{ __('tenant.invoice.modal.create_desc') }}</p>
                </div>
                <button type="button" id="close-invoice-modal" aria-label="{{ __('tenant.invoice.modal.close') }}" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus:ring-4 focus:ring-indigo-100">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            @if ($errors->any())
                <div class="mx-5 mt-5 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 sm:mx-6">
                    <svg class="mt-0.5 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/></svg>
                    <span>{{ __('tenant.invoice.modal.error_desc') }}</span>
                </div>
            @endif

            <form id="invoice-form" action="{{ route('tenant.invoice.store') }}" method="POST" class="px-5 py-5 sm:px-6">
                @csrf
                @include('tenant.invoice._formulir')
                <div class="mt-6 flex flex-col-reverse gap-2 border-t border-slate-100 pt-4 sm:flex-row sm:justify-end">
                    <button type="button" id="cancel-invoice-modal" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100">{{ __('tenant.invoice.modal.cancel') }}</button>
                    <button type="submit" id="submit-invoice" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        <span id="submit-label">{{ __('tenant.invoice.modal.save') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal pembayaran --}}
    <div id="payment-modal" class="hidden fixed inset-0 z-50 items-center justify-center bg-slate-900/50 p-2 backdrop-blur-sm sm:p-4" role="dialog" aria-modal="true" aria-labelledby="payment-modal-title">
        <div class="modal-scroll w-full max-w-2xl max-h-[95vh] overflow-y-auto rounded-2xl bg-white shadow-2xl">
            <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-5 py-4 sm:px-6">
                <div>
                    <h3 id="payment-modal-title" class="text-lg font-bold text-slate-900">{{ __('tenant.invoice.payment.title') }}</h3>
                    <p id="payment-invoice-id" class="mt-1 text-sm text-slate-500"></p>
                </div>
                <button type="button" id="close-payment-modal" aria-label="{{ __('tenant.invoice.modal.close') }}" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus:ring-4 focus:ring-indigo-100">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            @if ($errors->any() && $errors->has('rekening_debit'))
                <div class="mx-5 mt-5 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 sm:mx-6">
                    <svg class="mt-0.5 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/></svg>
                    <span>{{ $errors->first('rekening_debit') }}</span>
                </div>
            @endif

            <form id="payment-form" action="{{ route('tenant.transaksi.store') }}" method="POST" class="px-5 py-5 sm:px-6">
                @csrf
                <input type="hidden" id="payment-idv" name="idv">
                {{-- Setelah pembayaran sukses, balik ke halaman ini supaya user
                     bisa langsung lanjut ke langkah berikutnya. --}}
                <input type="hidden" name="redirect_to" value="{{ request()->getRequestUri() }}">

                {{-- Ringkasan invoice --}}
                <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ __('tenant.invoice.payment.jenis') }}</p>
                            <p id="payment-jenis" class="mt-0.5 text-sm font-semibold text-slate-800"></p>
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ __('tenant.invoice.payment.sekolah') }}</p>
                            <p id="payment-sekolah" class="mt-0.5 text-sm font-semibold text-slate-800"></p>
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ __('tenant.invoice.payment.pemilik') }}</p>
                            <p id="payment-owner" class="mt-0.5 text-sm font-semibold text-slate-800"></p>
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ __('tenant.invoice.payment.tgl_invoice') }}</p>
                            <p id="payment-tgl-invoice" class="mt-0.5 text-sm font-semibold text-slate-800"></p>
                        </div>
                    </div>
                    <div class="mt-3 rounded-xl border border-emerald-200 bg-emerald-50 px-3.5 py-2.5">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-emerald-600">{{ __('tenant.invoice.payment.jumlah') }}</p>
                        <p id="payment-jumlah" class="mt-0.5 text-xl font-bold tracking-tight text-emerald-700"></p>
                    </div>
                </div>

                <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="payment-rekening" class="mb-1.5 block text-sm font-semibold text-slate-700">
                            {{ __('tenant.invoice.payment.rekening') }} <span class="text-rose-500">*</span>
                        </label>
                        <select id="payment-rekening" name="rekening_debit" required class="invoice-input select2 bg-white" data-placeholder="{{ __('tenant.invoice.payment.rekening') }}">
                            <option value="">{{ __('tenant.invoice.payment.rekening') }}</option>
                            @foreach ($rekenings as $r)
                                <option value="{{ $r['kode'] }}">{{ $r['kode'] }} — {{ $r['nama'] }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1.5 text-xs text-slate-500">{{ __('tenant.invoice.payment.rekening_hint') }}</p>
                        @error('rekening_debit')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="payment-jumlah-input" class="mb-1.5 block text-sm font-semibold text-slate-700">
                            {{ __('tenant.invoice.payment.jumlah') }} <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-sm font-semibold text-slate-400">Rp</span>
                            <input type="text" id="payment-jumlah-input" name="jumlah" inputmode="numeric" required class="invoice-input nominal pl-11" value="" autocomplete="off">
                        </div>
                        @error('jumlah')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="payment-tgl-lunas" class="mb-1.5 block text-sm font-semibold text-slate-700">{{ __('tenant.invoice.payment.tgl_lunas') }}</label>
                        <input type="text" id="payment-tgl-lunas" name="tgl_lunas" class="invoice-input datepicker" placeholder="{{ __('tenant.invoice.field.pilih_tanggal') }}" autocomplete="off">
                        @error('tgl_lunas')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="payment-keterangan" class="mb-1.5 block text-sm font-semibold text-slate-700">{{ __('tenant.invoice.payment.keterangan') }}</label>
                        <input type="text" id="payment-keterangan" name="keterangan" class="invoice-input" placeholder="{{ __('tenant.invoice.payment.keterangan_hint') }}">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50/60 px-3.5 py-3 transition hover:bg-emerald-50">
                            <input type="checkbox" id="payment-mark-paid" name="mark_paid" value="1" checked class="mt-0.5 h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="flex-1">
                                <span class="block text-sm font-semibold text-emerald-800">{{ __('tenant.invoice.payment.mark_paid') }}</span>
                                <span class="block text-xs text-emerald-700/80">{{ __('tenant.invoice.payment.mark_paid_hint') }}</span>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="mt-6 flex flex-col-reverse gap-2 border-t border-slate-100 pt-4 sm:flex-row sm:justify-end">
                    <button type="button" id="cancel-payment-modal" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100">{{ __('tenant.invoice.modal.cancel') }}</button>
                    <button type="submit" id="submit-payment" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 text-sm font-semibold text-white hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span id="submit-payment-label">{{ __('tenant.invoice.payment.submit') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <form id="delete-invoice-form" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    @include('tenant.partials._fancy_inputs_scripts')
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script>
        (function () {
            'use strict';

            const $ = window.jQuery;

            const invoiceModal  = document.getElementById('invoice-modal');
            const invoiceForm   = document.getElementById('invoice-form');
            const deleteForm    = document.getElementById('delete-invoice-form');
            const paymentModal  = document.getElementById('payment-modal');
            const paymentForm   = document.getElementById('payment-form');
            const paymentAmount = document.getElementById('payment-jumlah-input');
            const amountField   = document.getElementById('jumlah');

            const HAS_ERRORS    = @json($errors->any());
            const TODAY         = @json(now()->format('Y-m-d'));
            const DATE_REQUIRED = @json(__('tenant.invoice.validation.date_required'));

            /* ------------------------------------------------------------------
             * Helper umum
             * ------------------------------------------------------------------ */
            function setModalState(modal, open) {
                modal.classList.toggle('hidden', !open);
                modal.classList.toggle('flex', open);
                document.body.classList.toggle('overflow-hidden', open);
            }

            /**
             * Terima baik elemen DOM maupun objek jQuery.
             */
            function toEl(target) {
                if (!target) { return null; }
                return target.jquery ? target[0] : target;
            }

            /**
             * Nilai uang dari input ber-mask.
             * `maskMoney('unmasked')` tidak dipakai karena pemrosesannya salah
             * untuk nominal yang digit terakhirnya "1" (1.000.000 -> 100000.1).
             * Karena presisi 0, cukup ambil digitnya saja.
             */
            function moneyValue(target) {
                const el = toEl(target);
                const digits = String((el && el.value) || '').replace(/[^0-9]/g, '');
                return digits === '' ? '' : String(parseInt(digits, 10));
            }

            /**
             * Isi input uang lalu terapkan mask supaya tampilannya rapi.
             * Nilai kosong dibiarkan kosong (tidak memicu `mask`, karena mask
             * akan mengubah string kosong menjadi "0").
             */
            function setMoneyValue(target, value) {
                const el = toEl(target);
                if (!el) { return; }

                const digits = String(value === null || value === undefined ? '' : value).replace(/[^0-9]/g, '');

                if (digits === '') {
                    el.value = '';
                    return;
                }

                el.value = digits;
                $(el).trigger('mask');
            }

            /**
             * Tulis nilai mentah (tanpa pemisah ribuan) sebelum form disubmit
             * supaya sisi server menerima angka yang valid.
             */
            function commitMoneyValue(target) {
                const el = toEl(target);
                if (!el) { return; }
                el.value = moneyValue(el);
            }

            /* ------------------------------------------------------------------
             * Select2 pada filter bar (di luar modal → dropdown ke body)
             *
             * Filter status (Lunas / Belum Lunas) berada di partial
             * `tenant.partials.tenant-filter` bersama pemilih sekolah.
             *
             * Opsi "Semua …" memakai nilai sentinel `__all__` (lihat catatan
             * pada markup filter bar). Select2 TIDAK boleh diberi `placeholder`
             * di sini: bila diberi, opsi bernilai kosong dianggap placeholder
             * dan dihapus dari daftar dropdown.
             * ------------------------------------------------------------------ */
            const FILTER_ALL = '__all__';

            /**
             * Terjemahkan nilai sentinel `__all__` menjadi string kosong,
             * karena string kosong berarti "tanpa filter" di sisi server.
             */
            function filterValue(selector) {
                var value = $(selector).val();
                return value === FILTER_ALL ? '' : (value || '');
            }

            function initFilterSelects() {
                $('.filter-select').each(function () {
                    const $el = $(this);
                    if ($el.data('select2')) { return; }
                    $el.select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        allowClear: false,
                        minimumResultsForSearch: $el.find('option').length <= 5 ? Infinity : 0,
                    }).on('select2:open', function () {
                        // Select2 4.1 tidak punya opsi `searchPlaceholder`;
                        // placeholder kotak pencarian diisi manual.
                        const $field = $('.select2-container--open .select2-search__field');
                        if ($field.length) {
                            $field.attr('placeholder', $el.data('search-placeholder') || 'Cari...');
                        }
                    });
                });
            }

            /* ------------------------------------------------------------------
             * Select2 di dalam modal → dropdownParent = modal, z-index di atas
             * ------------------------------------------------------------------ */
            function initModalSelects(scope) {
                $(scope).find('select.select2').each(function () {
                    const $el = $(this);
                    if ($el.data('select2')) { return; }
                    $el.select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        placeholder: $el.data('placeholder') || '—',
                        allowClear: false,
                        minimumResultsForSearch: $el.find('option').length <= 5 ? Infinity : 0,
                        dropdownParent: $(scope),
                    });
                });
            }

            /* ------------------------------------------------------------------
             * Flatpickr.
             * Catatan: flatpickr menyembunyikan input asli (`type=hidden`) tetapi
             * TETAP membawa atribut `required`. Input hidden yang wajib membuat
             * submit gagal dengan pesan "an invalid form control ... is not
             * focusable". Solusinya:(required dipindah ke altInput yang terlihat)
             * dan `required` dilepas dari input asli.
             * ------------------------------------------------------------------ */
            function initDatepickers(scope) {
                $(scope).find('input.datepicker').each(function () {
                    const el = this;
                    if (el._flatpickr) { return; }   // sudah di-init → jangan diulang

                    const required = el.hasAttribute('required');

                    const picker = flatpickr(el, {
                        dateFormat: 'Y-m-d',
                        altInput: true,
                        altFormat: 'd/m/Y',
                        altInputClass: 'invoice-input',
                        allowInput: true,
                        disableMobile: true,
                        onReady: function (selectedDates, dateStr, instance) {
                            if (!instance || !instance.altInput) { return; }
                            instance.altInput.classList.add('invoice-input');
                            if (!required) { return; }
                            instance.altInput.setAttribute('required', 'required');
                            instance.altInput.setAttribute('aria-required', 'true');
                        },
                        onChange: function (selectedDates, dateStr, instance) {
                            if (!instance || !instance.altInput) { return; }
                            instance.altInput.setCustomValidity('');
                        },
                    });

                // flatpickr sudah memasang `el._flatpickr` secara otomatis.
                el._flatpickr = picker;

                // Input asli tidak boleh lagi memblokir validasi browser.
                el.removeAttribute('required');

                if (!picker.altInput) { return; }

                picker.altInput.setAttribute('aria-label', el.getAttribute('aria-label') || '');

                if (!required) { return; }

                picker.altInput.setAttribute('required', 'required');
                picker.altInput.setAttribute('aria-required', 'true');

                const validate = function () {
                    const filled = String(el.value || '').trim() !== '';
                    picker.altInput.setCustomValidity(filled ? '' : DATE_REQUIRED);
                };

                el.addEventListener('change', validate);
                el.addEventListener('input', validate);
                picker.altInput.addEventListener('input', validate);
                picker.altInput.addEventListener('change', validate);

                validate();
                });
            }

            /* ------------------------------------------------------------------
             * Mask uang.
             * `allowZero: false` → kolom kosong tetap kosong saat blur
             * (dengan allowZero, nilai kosong berubah jadi "0" sehingga lolos
             * validasi `required`).
             * ------------------------------------------------------------------ */
            function initNominals(scope) {
                $(scope).find('input.nominal').each(function () {
                    const el = this;
                    if (el._invoiceMask) { return; }   // sudah di-init
                    el._invoiceMask = true;

                    const $el = $(el);
                    $el.maskMoney({
                        prefix: '',
                        thousands: ',',
                        decimal: '.',
                        precision: 0,
                        allowZero: false,
                        allowNegative: false,
                    });

                    const v = $el.val();
                    if (v !== '' && v !== null && typeof v !== 'undefined') {
                        setMoneyValue($el, v);
                    }
                });
            }

            function initAll(scope) {
                initModalSelects(scope);
                initNominals(scope);
                initDatepickers(scope);
            }

            /* ------------------------------------------------------------------
             * DataTable
             *
             * CATATAN PENTING:
             * Jumlah entri `columns` HARUS sama dengan jumlah <th> di <thead>
             * (10 = 1 kolom kontrol + 9 kolom data). DataTables memetakan
             * `aoColumns[i].nTh` ke <th> ke-i, sehingga ketidaksesuaian
             * menyebabkan: (1) judul header bergeser satu kolom, dan
             * (2) exception `Cannot read properties of undefined (reading
             * 'sWidth')` pada `_fnScrollDraw` yang menggagalkan redraw sehingga
             * overlay "Memproses…" tidak pernah hilang dan filter tidak
             * pernah seemingly bekerja.
             * ------------------------------------------------------------------ */
            const table = $('#invoices').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                lengthMenu: [[10, 15, 25, 50, 100], [10, 15, 25, 50, 100]],
                ajax: {
                    url: @json(route('tenant.invoice.data')),
                    data: function (d) {
                        d.status_filter = filterValue('#filter-status');
                    },
                },
                language: {
                    emptyTable: @json(__('tenant.invoice.empty')),
                    info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                    infoEmpty: 'Menampilkan 0 data',
                    infoFiltered: '(difilter dari _MAX_ total)',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    loadingRecords: 'Memuat…',
                    processing: 'Memproses…',
                    search: 'Cari:',
                    searchPlaceholder: 'Cari data...',
                    zeroRecords: 'Tidak ada invoice yang cocok.',
                    paginate: { first: '«', last: '»', next: '›', previous: '‹' },
                },
                // Kolom "Jatuh Tempo" berada pada indeks 6 (setelah kolom kontrol).
                order: [[6, 'desc']],
                columns: [
                    // Kolom kontrol: hanya tampil saat ada kolom yang disembunyikan.
                    { data: 'control', orderable: false, searchable: false, className: 'control', width: '3%' },
                    // `col-no` disembunyikan CSS di bawah 480px (lihat blok media query).
                    { data: 'DT_RowIndex', orderable: false, searchable: false, className: 'col-no', width: '4%' },
                    { data: 'jenis_pembayaran', name: 'admin_invoice.jenis_pembayaran', className: 'ps-3 font-semibold text-slate-800' },
                    { data: 'tenant_nama', name: 'tenant_nama', className: 'ps-3' },
                    { data: 'owner', name: 'owner', className: 'ps-3 text-slate-700' },
                    { data: 'tgl_invoice_fmt', name: 'admin_invoice.tgl_invoice', className: 'ps-3 text-slate-600 whitespace-nowrap' },
                    { data: 'tgl_lunas_fmt', name: 'admin_invoice.tgl_lunas', className: 'ps-3 text-slate-600 whitespace-nowrap' },
                    { data: 'jumlah_fmt', name: 'admin_invoice.jumlah', className: 'ps-3 text-right text-slate-900 font-semibold tabular-nums whitespace-nowrap col-amount' },
                    { data: 'status_badge', name: 'status_badge', orderable: false, searchable: false, className: 'text-center col-status' },
                    { data: 'action', orderable: false, searchable: false, className: 'text-center whitespace-nowrap' },
                ],
                columnDefs: [
                    { targets: 0, orderable: false, searchable: false },
                    { targets: -1, orderable: false, searchable: false },
                ],
                responsive: {
                    details: {
                        type: 'column',
                        // Hanya klik pada kolom kontrol yang membuka detail,
                        // sehingga tombol Bayar/Print/Hapus tidak ikut memicu.
                        target: 0,
                        renderer: function (api, rowIdx, columns) {
                            var data = $.map(columns, function (c, i) {
                                if (c.hidden && c.data !== null && c.data !== '') {
                                    var title = c.title || api.column(i).header().textContent || '';
                                    return '<li><span class="dtr-title">' + $('<div>').text(title).html() + '</span> <span class="dtr-data">' + (c.data || '') + '</span></li>';
                                }
                                return '';
                            }).join('');
                            return data ? $('<ul class="dtr-details" />').append(data) : false;
                        }
                    }
                },
            });

            /* ------------------------------------------------------------------
             * Filter
             *
             * Hanya ada filter status (Lunas / Belum Lunas). Jenis pembayaran
             * dan jatuh tempo sengaja dihapus agar semua pembayaran tetap
             * tampil; info jatuh tempo sudah tersedia di kolom tabel.
             * ------------------------------------------------------------------ */
            function reloadTable() {
                table.ajax.reload(null, false);
            }

            /**
             * Simpan pilihan filter status ke URL (query param `status_filter`).
             *
             * Tujuannya supaya filter tetap aktif setelah halaman di-reload,
             * termasuk setelah redirect dari proses pembayaran (kembali ke
             * /invoice). Query param ini dibaca controller untuk meng-set
             * nilai awal <select>.
             */
            function syncStatusFilterToUrl() {
                var value = filterValue('#filter-status');
                var url = new URL(window.location.href);

                if (value) {
                    url.searchParams.set('status_filter', value);
                } else {
                    url.searchParams.delete('status_filter');
                }

                window.history.replaceState({}, '', url.toString());
            }

            $('#filter-status').on('change', function () {
                syncStatusFilterToUrl();
                reloadTable();
            });

            /* ------------------------------------------------------------------
             * Modal tambah invoice
             * ------------------------------------------------------------------ */
            function openCreateModal() {
                invoiceForm.reset();
                $('#tenant_id').val('').trigger('change.select2');
                $('#jenis_pembayaran').val('').trigger('change.select2');
                $('input[name="status"][value="unpaid"]').prop('checked', true);

                initAll('#invoice-modal');

                // Kolom nominal dikosongkan (bukan "0") agar tetap wajib diisi.
                setMoneyValue(amountField, '');

                const invoicePicker = document.getElementById('tgl_invoice')._flatpickr;
                if (invoicePicker) {
                    invoicePicker.clear(false);
                    // memicu validasi ulang (kolom wajib kembali kosong)
                    document.getElementById('tgl_invoice').dispatchEvent(new Event('change', { bubbles: true }));
                }

                const duePicker = document.getElementById('tgl_lunas')._flatpickr;
                if (duePicker) { duePicker.clear(false); }

                setModalState(invoiceModal, true);

                const tenantSelect = $('#tenant_id');
                const searchField = tenantSelect.next('.select2-container').find('input.select2-search__field');
                if (searchField.length) { searchField.trigger('focus'); }
            }

            document.getElementById('add-invoice').addEventListener('click', openCreateModal);

            invoiceForm.addEventListener('submit', function () {
                commitMoneyValue(amountField);
            });

            /* ------------------------------------------------------------------
             * Modal pembayaran
             * ------------------------------------------------------------------ */
            function openPaymentModal(btn) {
                const d = btn.dataset;

                document.getElementById('payment-invoice-id').textContent = 'Invoice #' + d.id;
                document.getElementById('payment-idv').value = d.id;
                document.getElementById('payment-jenis').textContent = d.jenis || '—';
                document.getElementById('payment-sekolah').textContent = d.sekolah || '—';
                document.getElementById('payment-owner').textContent = d.owner || '—';
                document.getElementById('payment-tgl-invoice').textContent = d.tgl || '—';

                const nominal = Number(d.jumlah || 0);
                document.getElementById('payment-jumlah').textContent = 'Rp ' + nominal.toLocaleString('id-ID');

                initAll('#payment-modal');

                // Nominal default = nilai invoice yang benar-benar tercatat.
                setMoneyValue(paymentAmount, nominal);

                const duePicker = document.getElementById('payment-tgl-lunas')._flatpickr;
                if (duePicker) { duePicker.setDate(TODAY, true, 'Y-m-d'); }

                $('#payment-rekening').val('').trigger('change.select2');
                document.getElementById('payment-keterangan').value = '';
                document.getElementById('payment-mark-paid').checked = true;

                setModalState(paymentModal, true);

                const searchField = $('#payment-rekening').next('.select2-container').find('input.select2-search__field');
                if (searchField.length) { searchField.trigger('focus'); }
            }

            $('#invoices').on('click', '.pay-invoice', function (e) {
                e.stopPropagation();
                openPaymentModal(this);
            });

            $('#invoices').on('click', '.print-invoice', function (e) {
                e.stopPropagation();
                window.open(this.dataset.url, '_blank');
            });

            $('#invoices').on('click', '.delete-invoice', function (e) {
                e.stopPropagation();
                const action = this.dataset.action;
                Swal.fire({
                    title: @json(__('tenant.invoice.confirm.delete_title')),
                    text: @json(__('tenant.invoice.confirm.delete_text')),
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: @json(__('tenant.invoice.confirm.delete_yes')),
                    cancelButtonText: @json(__('tenant.invoice.confirm.delete_no')),
                    reverseButtons: true,
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-rose-600 text-white font-semibold text-sm hover:bg-rose-700 mx-1',
                        cancelButton: 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-slate-100 text-slate-700 font-semibold text-sm hover:bg-slate-200 mx-1',
                    },
                }).then(function (result) {
                    if (!result.isConfirmed) return;
                    deleteForm.action = action;
                    deleteForm.submit();
                });
            });

            paymentForm.addEventListener('submit', function () {
                commitMoneyValue(paymentAmount);
            });

            /* ------------------------------------------------------------------
             * Tutup modal
             * ------------------------------------------------------------------ */
            document.getElementById('close-payment-modal').addEventListener('click', function () { setModalState(paymentModal, false); });
            document.getElementById('cancel-payment-modal').addEventListener('click', function () { setModalState(paymentModal, false); });
            paymentModal.addEventListener('click', function (event) {
                if (event.target === paymentModal) setModalState(paymentModal, false);
            });

            document.getElementById('close-invoice-modal').addEventListener('click', function () { setModalState(invoiceModal, false); });
            document.getElementById('cancel-invoice-modal').addEventListener('click', function () { setModalState(invoiceModal, false); });
            invoiceModal.addEventListener('click', function (event) {
                if (event.target === invoiceModal) setModalState(invoiceModal, false);
            });

            document.addEventListener('keydown', function (event) {
                if (event.key !== 'Escape') return;
                if (!paymentModal.classList.contains('hidden')) {
                    setModalState(paymentModal, false);
                } else if (!invoiceModal.classList.contains('hidden')) {
                    setModalState(invoiceModal, false);
                }
            });

            /* ------------------------------------------------------------------
             * Init
             * ------------------------------------------------------------------ */
            $(function () {
                initFilterSelects();
                initAll('#invoice-modal');
                initAll('#payment-modal');

                if (HAS_ERRORS) {
                    setModalState(invoiceModal, true);
                }
            });
        })();
    </script>

    @if (session('success'))
        <script>
            Swal.fire({ toast: true, position: window.innerWidth < 640 ? 'top' : 'top-end', icon: 'success', title: @json(session('success')), showConfirmButton: false, timer: 3000, timerProgressBar: true });
        </script>
    @endif
    @if (session('error'))
        <script>
            Swal.fire({ toast: true, position: window.innerWidth < 640 ? 'top' : 'top-end', icon: 'error', title: @json(session('error')), showConfirmButton: false, timer: 3000, timerProgressBar: true });
        </script>
    @endif
</body>
</html>
