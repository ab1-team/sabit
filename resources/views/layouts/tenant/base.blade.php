@php
    use App\Utils\Tanggal;
    $jatuhTempo = optional(session('profil'))->jatuh_tempo ?? null;
    $appName = $appName ?? \App\Models\Profil::namaLembaga();
    $appLogoUrl = $appLogoUrl ?? \App\Models\Profil::logoUrl();
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $appName }} | {{ $title ?? 'Dashboard' }}</title>

    <link rel="icon" type="image/png" href="{{ $appLogoUrl }}?v={{ \App\Models\Profil::logoVersion() }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ $appLogoUrl }}?v={{ \App\Models\Profil::logoVersion() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=block">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />

    <link rel="stylesheet" href="/assets/css/nucleo-icons.css">
    <link rel="stylesheet" href="/assets/css/nucleo-svg.css">
    <link rel="stylesheet" href="/assets/css/material-dashboard.css?v=3.2.0">
    <link rel="stylesheet" href="https://unpkg.com/nprogress@0.2.0/nprogress.css">
    <style>#nprogress .bar { background: #37d17c !important; height: 3px !important; } #nprogress .peg { box-shadow: 0 0 10px #37d17c, 0 0 5px #37d17c !important; } #nprogress .spinner-icon { border-top-color: #37d17c !important; border-left-color: #37d17c !important; }</style>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jstree@3.3.12/dist/themes/default/style.min.css">
    <link rel="stylesheet" href="{{ asset('vendor/quill/quill.snow.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <script src="/assets/tinymce/tinymce.min.js"></script>

    <style>
        .modal-fullscreen {
            z-index: 2000 !important;
        }
        .modal-backdrop.show {
            z-index: 1999 !important;
        }
        .modal {
            z-index: 12000 !important;
        }
        .modal-backdrop {
            z-index: 11999 !important;
        }

        body:not(.modal-open) .card {
            border: none !important;
            box-shadow: 0 6px 18px rgba(15, 23, 42, .08),
                        0 2px 6px rgba(15, 23, 42, .06) !important;
        }

        .row > [class*="col-"] > .card {
            margin-bottom: 1.25rem;
        }

        .card .card {
            box-shadow: 0 4px 12px rgba(15, 23, 42, .07) !important;
        }

        .card-header,
        .card-footer {
            border: none !important;
        }

        .card-body {
            transition: all 0.3s ease;
        }
        #editor,
        .ql-container {
            min-height: 20px
        }

        .table tbody tr {
            cursor: pointer;
        }

        .table tbody tr:hover {
            background-color: #f5f5f5;
        }

        .swal2-container {
            z-index: 99999 !important
        }

        .table {
            table-layout: fixed;
            width: 100%
        }

        .table tbody tr {
            height: 48px
        }
        .table thead th{
            font-size: 16px;
        }
        .table tbody td {
            font-size: 13px;
        }
        .table td input[type="checkbox"] {
            width: 20px;
            height: 20px;
            transform: scale(1);
        }
        .table.table-nowrap td,
        .table.table-nowrap th {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: middle;
        }
        .table:not(.table-nowrap) td,
        .table:not(.table-nowrap) th {
            vertical-align: middle;
        }
        @media (max-width: 767.98px) {
            .table.table-nowrap td,
            .table.table-nowrap th { white-space: normal; }
            .table thead th { font-size: 14px; }
            .table tbody td { font-size: 12px; }
        }

        .td-action .action-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            height: 100%
        }

        .table .btn {
            margin: 0
        }
        .btn-compact {
            padding: 6px 10px !important;
            line-height: 1.2 !important;
            min-height: 36px;
        }
        .btn-compact i {
            font-size: 16px !important;
        }
        @media (max-width: 575.98px) {
            .btn-compact { min-height: 44px; padding: 8px 12px !important; }
        }

        /* === Reusable utilities === */
        .action-toolbar {
            display: flex;
            flex-direction: column;
            gap: .5rem;
        }
        @media (min-width: 768px) {
            .action-toolbar { flex-direction: row; align-items: center; justify-content: space-between; }
        }

        .page-title {
            font-size: 1.15rem;
            font-weight: 700;
            margin: 0;
            line-height: 1.2;
            word-break: break-word;
        }
        @media (min-width: 768px) { .page-title { font-size: 1.35rem; } }
        @media (min-width: 1200px) { .page-title { font-size: 1.5rem; } }

        .filter-bar {
            display: flex;
            flex-direction: column;
            gap: .5rem;
        }
        @media (min-width: 768px) {
            .filter-bar { flex-direction: row; flex-wrap: wrap; align-items: end; }
        }

        .touch-target { min-width: 36px; min-height: 36px; display: inline-flex; align-items: center; justify-content: center; }
        @media (max-width: 575.98px) { .touch-target { min-width: 44px; min-height: 44px; } }

        @media(max-width:576px) {
            #preview-img-box {
                width: 310px !important;
                height: 310px !important
            }
        }
        .twitter-typeahead {
            width: 100%;
            position: relative;
            display: block;
        }

        .twitter-typeahead .tt-input {
            width: 100%;
            font-size: 13px;
        }

        .form-search {
            border: none;
            border-bottom: 2px solid #dee2e6;
            border-radius: 0;
            padding-left: 0;
        }

        .form-search:focus {
            box-shadow: none;
            border-bottom-color: #37d17c;
        }

        .tt-menu {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            margin-top: 6px;
            background: #37d17c;
            max-height: 260px;
            z-index: 9999;
            border-radius: 10px;
            overflow-y: auto;
            padding: 4px 0;
        }

        .tt-suggestion {
            display: block !important;
            width: 100% !important;
            padding: 4px 8px !important;
            line-height: 1.25 !important;
            font-size: 11px !important;
            color: #000;
            background: #cecece;
            border-bottom: 1px solid rgba(0, 0, 0, 0.15);
            cursor: pointer;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .tt-suggestion * {
            margin: 0 !important;
            padding: 0 !important;
            line-height: 1.25 !important;
            font-size: 11px !important;
        }

        .tt-suggestion.tt-cursor {
            background: #6d6d6d;
            color: #fff;
            border-bottom-color: #37d17c;
        }

        .tt-suggestion:last-child {
            border-bottom: none;
        }

        @media (max-width: 768px) {
            .tt-suggestion {
                font-size: 12px !important;
                padding: 5px 8px !important;
            }
        }

        .tt-suggestion * {
            margin: 8px !important;
            padding: 0 !important;
            line-height: 1 !important;
        }

        .tt-suggestion:last-child {
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
        }

        .tt-suggestion:hover,
        .tt-suggestion.tt-cursor {
            background: #6d6d6d;
        }

        .tt-highlight {
            font-weight: 600;
        }

        .select2-container--bootstrap-5 * {
            --bs-primary: #37d17c;
        }

        .select2-container--bootstrap-5 .select2-selection {
            border: 1px solid #37d17c !important;
            box-shadow: none !important;
        }

        .select2-container--bootstrap-5.select2-container--focus .select2-selection {
            border-color: #37d17c !important;
            box-shadow: 0 0 0 .25rem rgba(53, 220, 89, 0.25) !important;
        }

        .select2-container--bootstrap-5 .select2-selection__rendered {
            color: #000 !important;
        }

        .select2-container--bootstrap-5 .select2-selection__arrow {
            color: #000 !important;
        }

        .select2-container--bootstrap-5 .select2-search--dropdown .select2-search__field {
            border: 1px solid #37d17c !important;
            outline: none !important;
            box-shadow: none !important;
            color: #000 !important;
        }

        .select2-container--bootstrap-5 .select2-search--dropdown .select2-search__field:focus {
            border-color: #37d17c !important;
            outline: none !important;
            box-shadow: 0 0 0 .2rem rgba(42, 249, 128, 0.25) !important;
        }

        .select2-container--bootstrap-5 .select2-dropdown {
            border: 1px solid #37d17c !important;
        }

        .select2-container--bootstrap-5 .select2-results__option {
            color: #000 !important;
        }

        .select2-container--bootstrap-5 .select2-results__option--highlighted {
            background-color: #37d17c !important;
            color: #fff !important;
        }

        .select2-container--bootstrap-5 .select2-results__option--selected {
            background-color: rgba(53, 220, 73, 0.2) !important;
            color: #000 !important;
        }

        .select2-container--bootstrap-5 *:focus {
            outline: none !important;
        }

        /* teks yang tampil di select */
        .select2-container--bootstrap-5 .select2-selection__rendered {
            font-family: inherit !important;
            font-size: 0.875rem !important;
            /* sama dengan .form-control */
            font-weight: 400 !important;
            /* ini yang bikin tidak tebal */
            line-height: 1.5 !important;
            padding-left: 0.75rem !important;
        }

        /* input search di dropdown */
        .select2-container--bootstrap-5 .select2-search__field {
            font-family: inherit !important;
            font-size: 0.875rem !important;
            font-weight: 400 !important;
            line-height: 1.5 !important;
        }

        /* tinggi & alignment box select */
        .select2-container--bootstrap-5 .select2-selection {
            min-height: calc(1.5em + .75rem + 2px);
            display: flex;
            align-items: center;
        }

        /* arrow biar sejajar */
        .select2-container--bootstrap-5 .select2-selection__arrow {
            height: 100% !important;
        }


        /* NORMAL: belum klik */
        .select2-container--bootstrap-5 .select2-selection {
            border-color: #ced4da !important;
            box-shadow: none !important;
        }

        /* focus & focus-within TIDAK BOLEH ubah warna */
        .select2-container--bootstrap-5.select2-container--focus .select2-selection,
        .select2-container--bootstrap-5:focus-within .select2-selection {
            border-color: #ced4da !important;
            box-shadow: none !important;
        }

        /* hover tetap normal */
        .select2-container--bootstrap-5 .select2-selection:hover {
            border-color: #ced4da !important;
        }

        /*HANYA SAAT DIKLIK / DROPDOWN TERBUKA */
        .select2-container--bootstrap-5.select2-container--open .select2-selection {
            border-color: #37d17c !important;
            box-shadow: 0 0 0 .25rem rgba(53, 220, 103, 0.25) !important;
        }
        .material-symbols-rounded {
            font-family: 'Material Symbols Rounded' !important;
            font-weight: normal;
            font-style: normal;
            font-size: 20px;
            line-height: 1;
            letter-spacing: normal;
            text-transform: none;
            white-space: nowrap;
            word-wrap: normal;
            direction: ltr;
            font-variation-settings:
                'FILL' 0,
                'wght' 400,
                'GRAD' 0,
                'opsz' 24;
            display: inline-block;
            vertical-align: middle;
        }
        .material-symbols-rounded:empty { display: none; }
        .material-icons {
            font-family: 'Material Icons' !important;
            font-weight: normal;
            font-style: normal;
            font-size: 20px;
            line-height: 1;
            display: inline-block;
            white-space: nowrap;
        }

        .modal:not(.modal-fullscreen) .modal-content {
            border-radius: 1rem !important;
            overflow: hidden;
        }

        .sidenav-header .navbar-brand {
            display: flex !important;
            align-items: center;
            gap: 6px;
        }
        .sidenav-header .navbar-brand img { flex-shrink: 0; }
        .sidenav-header .navbar-brand > span {
            flex: 1 1 auto;
            min-width: 0;
            line-height: 1.2;
        }
        .modal:not(.modal-fullscreen) .modal-header {
            border-top-left-radius: 1rem !important;
            border-top-right-radius: 1rem !important;
        }
        .modal:not(.modal-fullscreen) .modal-footer {
            border-bottom-left-radius: 1rem !important;
            border-bottom-right-radius: 1rem !important;
        }
        .modal-fullscreen .modal-content,
        .modal-fullscreen .modal-header,
        .modal-fullscreen .modal-footer {
            border-radius: 0 !important;
        }

        @media (min-width: 1200px) {
            .sidenav.navbar-vertical.navbar-expand-xs,
            .sidenav:hover {
                max-width: 15rem !important;
            }
            .sidenav.fixed-start + .main-content,
            .sidenav.fixed-end + .main-content {
                margin-left: 15rem !important;
                margin-right: 0 !important;
            }
            .main-content > .container-fluid {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }
            .main-content {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }
        }

        .sidenav .nav-link.nav-active,
        .sidenav .nav-link.active.nav-active {
            background-color: #1a1a1a !important;
            color: #ffffff !important;
            border-radius: 50rem !important;
            padding-top: 0.6rem !important;
            padding-bottom: 0.6rem !important;
        }
        .sidenav .nav-link.nav-active .material-symbols-rounded,
        .sidenav .nav-link.active.nav-active .material-symbols-rounded {
            color: #ffffff !important;
            opacity: 1 !important;
        }
        .sidenav .nav-link:hover:not(.nav-active) {
            background-color: #f5f5f5 !important;
            color: #1a1a1a !important;
            border-radius: 50rem !important;
            padding-top: 0.6rem !important;
            padding-bottom: 0.6rem !important;
        }
        #sidenav-main .collapse,
        #sidenav-main .collapsing {
            transition: none !important;
        }
        #sidenav-main {
            border-radius: 0.75rem !important;
            border: none !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.10), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
        }

        .main-content .card,
        .main-content .modal-content {
            border-radius: 0.75rem !important;
            border: none !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.10), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
        }
        #sidenav-main .nav .nav-link.text-dark:hover {
            color: #1a1a1a !important;
        }
    </style>


    @yield('style')
</head>

<body class="g-sidenav-show bg-gray-100">
    <aside class="sidenav navbar navbar-vertical navbar-expand-xs fixed-start ms-2 my-2 bg-white border-radius-lg"
        id="sidenav-main">
        <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-xl-none"
                id="iconSidenav"></i>
            <a class="navbar-brand px-4 py-3 m-0" href="/" target="_blank">
                <img src="{{ $appLogoUrl }}?v={{ \App\Models\Profil::logoVersion() }}" width="35" height="35">
                <span class="ms-1 text-sm text-dark">
                    @php
                        $__words = preg_split('/\s+/', trim($appName));
                        $__first = true;
                        foreach ($__words as $__w) {
                            if (!$__first) echo '<wbr>';
                            echo e($__w);
                            $__first = false;
                        }
                    @endphp
                </span>
            </a>
        </div>
        <hr class="horizontal dark mt-0 mb-2">
        @include('layouts.sidebar')
    </aside>

    <main class="main-content position-relative">
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-3 shadow-none border-radius-xl" id="navbarBlur"
            data-scroll="true">
            @include('layouts.navbar')
        </nav>

        <div class="container-fluid py-2">
            <div class="container-fluid">
                @yield('content')
            </div>
            <footer class="footer py-4">
                @include('layouts.footer')
            </footer>
        </div>
    </main>
    
    <div class="fixed-plugin">
        <a href="javascript:void(0)" class="fixed-plugin-button text-dark position-fixed px-3 py-2">
            <span id="iconSettings" class="material-symbols-rounded py-2">settings</span>
        </a>
        <div class="card shadow-lg">
        <div class="card-header pb-0 pt-3">
            <div class="float-start">
            <h5 class="mt-3 mb-0">Konfigurasi Tampilan</h5>
            <p>Atur tampilan dasbor Anda.</p>
            </div>
            <div class="float-end mt-4">
            <button class="btn btn-link text-dark p-0 fixed-plugin-close-button">
                <span class="material-symbols-rounded">clear</span>
            </button>
            </div>
        </div>
        <hr class="horizontal dark my-1">
        <div class="card-body pt-sm-3 pt-0">
            <div>
            <h6 class="mb-0">Warna Sidebar</h6>
            </div>
            <a href="javascript:void(0)" class="switch-trigger background-color">
            <div class="badge-colors my-2 text-start">
                <span class="badge filter bg-gradient-primary" data-color="primary" onclick="sidebarColor(this)"></span>
                <span class="badge filter bg-gradient-dark active" data-color="dark" onclick="sidebarColor(this)"></span>
                <span class="badge filter bg-gradient-info" data-color="info" onclick="sidebarColor(this)"></span>
                <span class="badge filter bg-gradient-success" data-color="success" onclick="sidebarColor(this)"></span>
                <span class="badge filter bg-gradient-warning" data-color="warning" onclick="sidebarColor(this)"></span>
                <span class="badge filter bg-gradient-danger" data-color="danger" onclick="sidebarColor(this)"></span>
            </div>
            </a>
            <div class="mt-5">
            <h6 class="mb-0">Tipe Sidenav</h6>
            <p class="text-sm">Pilih tipe sidenav yang diinginkan.</p>
            </div>
            <div class="d-flex ">
            <button class="btn bg-gradient-dark px-3 mb-2" data-class="bg-gradient-dark" onclick="sidebarType(this)">Gelap</button>
            <button class="btn bg-gradient-dark px-3 mb-2 ms-2" data-class="bg-transparent" onclick="sidebarType(this)">Transparan</button>
            <button class="btn bg-gradient-dark px-3 mb-2  active ms-2" data-class="bg-white" onclick="sidebarType(this)">Putih</button>
            </div>
            <p class="text-sm d-xl-none d-block mt-2">Tipe sidenav hanya dapat diubah pada tampilan desktop.</p>
            <div class="mt-5 d-flex">
                <h6 class="mb-0">Navbar Tetap</h6>
                <div class="form-check form-switch ps-0 ms-auto my-auto">
                    <input class="form-check-input mt-1 ms-auto" type="checkbox" id="navbarFixed" onclick="navbarFixed(this)">
                </div>
            </div>
            <hr class="horizontal dark my-3">
            <div class="mt-2 d-flex">
            <h6 class="mb-0">Mode Terang / Gelap</h6>
            <div class="form-check form-switch ps-0 ms-auto my-auto">
                <input class="form-check-input mt-1 ms-auto" type="checkbox" id="dark-version" onclick="darkMode(this)">
            </div>
            </div>
            <hr class="horizontal dark my-sm-4">
        </div>
        </div>
    </div>
    @yield('modal')

    <div class="modal fade" id="DukunganTeknis" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 720px;">
            <div class="modal-content border-0" style="border-radius: 1.25rem; overflow: hidden;">
                <div class="modal-body p-0">
                    <div class="px-4 pt-4 pb-2 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="material-symbols-rounded" style="font-size:24px; color:#37d17c;">support_agent</span>
                            <h5 class="modal-title mb-0">Dukungan Teknis</h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="px-4 pb-3 text-muted" style="font-size: 14px;">
                        Kendala pada sistem? Hubungi kami lewat saluran di bawah ini.
                    </div>

                    <div class="row g-2 px-3 pb-4">
                        <div class="col-12 col-md-6">
                            <a href="tel:+62882006644656" class="text-decoration-none text-dark support-card d-flex align-items-center gap-3 p-3">
                                <span class="support-icon" style="background: rgba(55,209,124,.12); color:#37d17c;">
                                    <span class="material-symbols-rounded">call</span>
                                </span>
                                <span class="flex-grow-1">
                                    <span class="d-block fw-semibold">Telepon</span>
                                    <span class="d-block text-muted" style="font-size:13px;">+62 882-0066-44656</span>
                                </span>
                                <span class="material-symbols-rounded text-muted">chevron_right</span>
                            </a>
                        </div>
                        <div class="col-12 col-md-6">
                            <a href="javascript:void(0)" id="waSupport" class="text-decoration-none text-dark support-card d-flex align-items-center gap-3 p-3">
                                <span class="support-icon" style="background: rgba(37,211,102,.12); color:#25d366;">
                                    <span class="material-symbols-rounded">chat</span>
                                </span>
                                <span class="flex-grow-1">
                                    <span class="d-block fw-semibold">WhatsApp</span>
                                    <span class="d-block text-muted" style="font-size:13px;">Chat sekarang</span>
                                </span>
                                <span class="material-symbols-rounded text-muted">chevron_right</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .support-card {
            border: 1px solid #e9ecef;
            border-radius: .85rem;
            transition: all .2s ease;
            background: #fff;
        }
        .support-card:hover {
            border-color: #37d17c;
            background: #fafdfb;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(55,209,124,.12);
        }
        .support-icon {
            width: 44px;
            height: 44px;
            border-radius: .75rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .support-icon .material-symbols-rounded {
            font-size: 22px;
        }

        .fixed-plugin-button-nav {
            display: inline-block;
            transition: transform .4s ease;
            cursor: pointer;
        }
        .fixed-plugin-button-nav:hover {
            transform: rotate(90deg);
        }
    </style>

    <script>
        const icon = document.getElementById('iconSettings');
        const fpPanel = document.querySelector('.fixed-plugin');
        const fpButton = document.querySelector('.fixed-plugin-button');
        let angle = 0;
        let spinning = true;
        function rotate() {
            if (spinning) {
                angle += 2;
                icon.style.transform = `rotate(${angle}deg)`;
            }
            requestAnimationFrame(rotate);
        } rotate();
        fpButton.addEventListener('mouseenter', () => spinning = false);
        fpButton.addEventListener('mouseleave', () => spinning = true);
        fpButton.addEventListener('click', function (e) {
            e.stopPropagation();
            fpPanel.classList.toggle('show');
        });
        document.querySelectorAll('.fixed-plugin-close-button').forEach(btn => {
            btn.addEventListener('click', () => fpPanel.classList.remove('show'));
        });
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.fixed-plugin')) {
                fpPanel.classList.remove('show');
            }
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-maskmoney/3.0.2/jquery.maskMoney.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/jstree@3.3.12/dist/jstree.min.js"></script>
    <script src="{{ asset('vendor/quill/quill.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.10.3/typeahead.jquery.min.js"></script>
    <script src="/assets/js/core/popper.min.js"></script>
    <script src="/assets/js/core/bootstrap.min.js"></script>
    <script>
        document.getElementById('btnDukunganTeknis').addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var dd = this.closest('.dropdown-menu');
            var trigger = dd ? dd.closest('.nav-item').querySelector('[data-bs-toggle="dropdown"]') : null;
            if (trigger) bootstrap.Dropdown.getOrCreateInstance(trigger).hide();
            bootstrap.Modal.getOrCreateInstance(document.getElementById('DukunganTeknis')).show();
        });
        document.getElementById('waSupport').addEventListener('click', function () {
            var nama = @json(auth()->user()->name ?? 'User');
            var email = @json(auth()->user()->email ?? '-');
            var pesan = 'Halo Technical Support,%0A%0APerkenalkan, saya *' + nama + '* (' + email + ') ingin berkonsultasi mengenai kendala pada sistem.%0A%0A*Detail:%0AHalaman*: ' + document.title + '%0A*URL*: ' + window.location.href + '%0A%0AMohon bantuannya. Terima kasih.';
            window.open('https://wa.me/62882006644656?text=' + pesan, '_blank');
        });
    </script>
    <script src="/assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="/assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script src="/assets/js/plugins/chartjs.min.js"></script>
    <script src="/assets/js/material-dashboard.min.js?v=3.2.0"></script>
    <script src="https://unpkg.com/nprogress@0.2.0/nprogress.js"></script>
    <script>
        NProgress.configure({ showSpinner: false, trickleSpeed: 120, minimum: 0.2 });
        NProgress.configure({ template: '<div class="bar" role="bar"><div class="peg"></div></div><div class="spinner" role="spinner"><div class="spinner-icon"></div></div>' });
        document.addEventListener('click', function (e) {
            var a = e.target.closest && e.target.closest('a');
            if (!a) return;
            var href = a.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript:') || a.target === '_blank' || e.ctrlKey || e.metaKey || e.shiftKey) return;
            if (a.hasAttribute('data-no-progress') || a.hasAttribute('data-bs-toggle') || a.hasAttribute('data-toggle')) return;
            if (a.origin && a.origin !== window.location.origin) return;
            NProgress.start();
        });
        window.addEventListener('beforeunload', function () { NProgress.set(0.9); });
        window.addEventListener('pageshow', function (e) { if (e.persisted) NProgress.remove(); else NProgress.done(true); });
    </script>
    <script>
        const dimTargets = () => [
            document.querySelector('.sidenav'),
            document.querySelector('.main-content'),
            document.querySelector('.navbar-main'),
        ].filter(Boolean);
        const setDimmed = (on) => {
            dimTargets().forEach(el => {
                el.style.opacity = on ? '0.4' : '';
                el.style.filter = on ? 'blur(5px) grayscale(1) brightness(0.6)' : '';
            });
        };
        document.addEventListener('show.bs.modal', () => setDimmed(true));
        document.addEventListener('hidden.bs.modal', () => setDimmed(false));
    </script>
    <script>
        if (navigator.platform.includes('Win') && document.querySelector('#sidenav-scrollbar')) {
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), {
                damping: .5
            })
        }
    </script>
    <script>
        $(document).ready(function() {

            $('textarea.form-control').each(function() {
                if ($(this).val()) {
                    $(this).closest('.input-group').addClass('is-filled');
                }
            });

            $('textarea.form-control').on('focus input', function() {
                $(this).closest('.input-group').addClass('is-filled');
            });

            $('textarea.form-control').on('blur', function() {
                if (!$(this).val()) {
                    $(this).closest('.input-group').removeClass('is-filled');
                }
            });

        });
    </script>
    @php $pp = session('piutang_prompt'); @endphp
    @if(is_array($pp))
        @php session()->forget('piutang_prompt'); @endphp
        <script>
        (function () {
            const pp = @json($pp);
            const job = pp.job;
            Swal.fire({
                icon: 'warning',
                title: 'Waktunya Generate Piutang SPP',
                html: '<strong>Bulan: ' + pp.bulan + '</strong><br>Tekan tombol untuk memproses di tab baru.',
                showCancelButton: true,
                confirmButtonText: 'Generate',
                cancelButtonText: 'Nanti',
                allowOutsideClick: false,
            }).then((r) => {
                if (r.isConfirmed) {
                    window.open(
                        "{{ url('/app/system/generate-tunggakan') }}?job=" + encodeURIComponent(job),
                        '_blank'
                    );
                    window.location.href = "{{ route('app.dashboard') }}?gen_piutang=1&job=" + encodeURIComponent(job);
                }
            });
        })();
        </script>
    @endif

    @if(session('msg') && !session('piutang_prompt'))
        <script>
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: @json(session('msg')),
            showConfirmButton: false,
            timer: 3000
        });
        </script>
    @endif


    <script>
        $('.btn-logout').on('click', function(e) {
            e.preventDefault()
            Swal.fire({
                title: 'Keluar dari aplikasi?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, keluar',
                cancelButtonText: 'Batal'
            }).then(v => {
                if (v.isConfirmed) $('#formLogout').submit()
            })
        })
    </script>
    @yield('script')
</body>

</html>
