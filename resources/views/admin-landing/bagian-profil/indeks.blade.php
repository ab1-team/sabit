@extends('layouts.tenant.base')

@section('style')
    @include('admin-landing._gaya')
    <style>
        /* ============ Stack Container ============ */
        .lp-ps-stack {
            display: flex;
            flex-direction: column;
            gap: .75rem;
        }

        /* ============ Card ============ */
        .lp-ps-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: .85rem;
            padding: .9rem 1.1rem;
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        .lp-ps-card.is-overview {
            background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 60%);
            border-color: rgba(25, 135, 84, .25);
        }
        .lp-ps-card.is-inactive { background: #f8fafc; opacity: .92; }

        /* ============ Card Header ============ */
        .lp-ps-head {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding-bottom: .7rem;
            margin-bottom: .75rem;
            border-bottom: 1px dashed #e2e8f0;
        }
        .lp-ps-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
            box-shadow: 0 3px 8px -4px rgba(15, 23, 42, .12);
        }
        /* Icon variants */
        .lp-ps-icon.is-overview    { background: linear-gradient(135deg, #dcfce7, #bbf7d0); color: #15803d; }
        .lp-ps-icon.is-sejarah     { background: linear-gradient(135deg, #ede9fe, #ddd6fe); color: #6d28d9; }
        .lp-ps-icon.is-visi_misi   { background: linear-gradient(135deg, #cffafe, #a5f3fc); color: #0e7490; }
        .lp-ps-icon.is-akreditasi  { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #b45309; }

        .lp-ps-title {
            font-weight: 700;
            font-size: .98rem;
            color: #1f2937;
            margin: 0;
            line-height: 1.2;
            letter-spacing: -.005em;
        }
        .lp-ps-key {
            font-size: .66rem;
            font-weight: 600;
            letter-spacing: .06em;
            color: #94a3b8;
            text-transform: uppercase;
            margin-top: 2px;
        }
        .lp-ps-status {
            margin-left: auto;
            flex-shrink: 0;
        }

        /* ============ Switch ============ */
        .lp-ps-toggle {
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            padding: .35rem .55rem .35rem .45rem;
            background: #f1f5f9;
            border-radius: 999px;
            cursor: pointer;
            user-select: none;
            transition: background .2s ease;
            border: 1px solid transparent;
        }
        .lp-ps-toggle:hover { background: #e2e8f0; }
        .lp-ps-toggle.is-on {
            background: rgba(25, 135, 84, .12);
            border-color: rgba(25, 135, 84, .25);
        }
        .lp-ps-toggle.is-off {
            background: rgba(100, 116, 139, .12);
            border-color: rgba(100, 116, 139, .2);
        }
        .lp-ps-toggle input[type="checkbox"] {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }
        .lp-ps-toggle-track {
            position: relative;
            width: 34px;
            height: 18px;
            background: #94a3b8;
            border-radius: 999px;
            transition: background .2s ease;
            flex-shrink: 0;
        }
        .lp-ps-toggle-track::after {
            content: "";
            position: absolute;
            top: 2px;
            left: 2px;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 1px 2px rgba(0, 0, 0, .2);
            transition: transform .2s ease;
        }
        .lp-ps-toggle.is-on .lp-ps-toggle-track {
            background: #198754;
        }
        .lp-ps-toggle.is-on .lp-ps-toggle-track::after {
            transform: translateX(16px);
        }
        .lp-ps-toggle-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            font-size: 12px;
            flex-shrink: 0;
        }
        .lp-ps-toggle.is-on .lp-ps-toggle-icon {
            background: #198754;
            color: #fff;
        }
        .lp-ps-toggle.is-off .lp-ps-toggle-icon {
            background: #64748b;
            color: #fff;
        }
        .lp-ps-toggle-text {
            font-size: .78rem;
            font-weight: 700;
            letter-spacing: .02em;
        }
        .lp-ps-toggle.is-on .lp-ps-toggle-text { color: #15803d; }
        .lp-ps-toggle.is-off .lp-ps-toggle-text { color: #475569; }
        .lp-ps-toggle:focus-within {
            outline: 2px solid rgba(25, 135, 84, .35);
            outline-offset: 2px;
        }

        /* ============ Form Layout ============ */
        .lp-ps-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .6rem .85rem;
        }
        @media (max-width: 575.98px) {
            .lp-ps-form-grid { grid-template-columns: 1fr; }
        }
        .lp-ps-form-grid .lp-ps-full { grid-column: 1 / -1; }
        .lp-ps-form-grid .input-group { margin-bottom: 0; }
        .lp-ps-form-grid .input-group .form-control { padding-top: .85rem; padding-bottom: .35rem; }

        .lp-ps-field-help {
            font-size: .72rem;
            color: #94a3b8;
            margin: 0 0 .5rem;
            line-height: 1.45;
        }

        /* ============ Footer / Save per-card ============ */
        .lp-ps-foot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .65rem;
            flex-wrap: wrap;
            margin-top: .85rem;
            padding-top: .65rem;
            border-top: 1px dashed #e2e8f0;
        }
        .lp-ps-foot-info {
            font-size: .72rem;
            color: #94a3b8;
            display: inline-flex;
            align-items: center;
            gap: .3rem;
        }
        .lp-ps-foot-info .material-symbols-rounded { font-size: 13px; }
        .lp-ps-foot .btn {
            min-width: 110px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .35rem;
            padding: .45rem 1rem;
            border-radius: .5rem;
            font-size: .85rem;
        }
        .lp-ps-foot .btn .material-symbols-rounded {
            font-size: 16px;
            line-height: 1;
        }

        /* TinyMCE container rapi */
        .lp-ps-card .tox-tinymce {
            border-radius: .55rem;
            border-color: #e2e8f0 !important;
        }
        .lp-ps-card .tox-tinymce:focus-within { border-color: #1f9d57 !important; }

        /* Plain textarea rapi */
        .lp-ps-card textarea.lp-plain {
            font-size: .88rem;
            line-height: 1.55;
            padding: .65rem .85rem;
            border-color: #e2e8f0;
            resize: vertical;
            min-height: 70px;
        }
        .lp-ps-card textarea.lp-plain:focus {
            border-color: #1f9d57;
            box-shadow: 0 0 0 2px rgba(31, 157, 87, .12);
        }

        /* ============ Empty state ============ */
        .lp-ps-empty {
            text-align: center;
            padding: 2.5rem 1rem;
            color: #94a3b8;
            background: #fff;
            border: 1px dashed #cbd5e1;
            border-radius: .85rem;
        }
        .lp-ps-empty .material-symbols-rounded { font-size: 42px; opacity: .55; }

        /* ============ Sub-section divider (Fasilitas) ============ */
        .lp-ps-divider {
            display: flex;
            align-items: center;
            gap: .65rem;
            margin: 1.5rem 0 .85rem;
            padding-top: 1rem;
            border-top: 2px dashed #e2e8f0;
        }
        .lp-ps-divider-icon {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, #ecfeff, #cffafe);
            color: #0e7490;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }
        .lp-ps-divider-title {
            font-weight: 700;
            font-size: .98rem;
            color: #1f2937;
            margin: 0;
            line-height: 1.2;
        }
        .lp-ps-divider-sub {
            font-size: .72rem;
            color: #94a3b8;
            margin-top: 2px;
        }
        .lp-ps-divider-spacer { flex: 1; }

        /* ============ Fasilitas accordion (pola PPDB Jadwal) ============ */
        .lp-fas-card .lp-rep-icon {
            background: linear-gradient(135deg, #ede9fe, #ddd6fe);
            color: #6d28d9;
        }
        .lp-fas-card.is-new .lp-rep-icon {
            background: linear-gradient(135deg, #e0f2fe, #bae6fd);
            color: #0369a1;
        }

        /* === Outer card yang membungkus seluruh section Fasilitas === */
        .lp-fas-section-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: .95rem;
            padding: 1.1rem 1.25rem 1rem;
            margin-top: 1rem;
            box-shadow: 0 2px 6px -4px rgba(15, 23, 42, .08);
        }
        .lp-fas-section-head {
            display: flex;
            align-items: center;
            gap: .85rem;
            padding: .25rem .25rem 1rem;
            margin-bottom: 1rem;
            border-bottom: 1px dashed #e2e8f0;
        }
        .lp-fas-section-icon {
            width: 44px; height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, #ecfeff, #cffafe);
            color: #0e7490;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
            box-shadow: 0 3px 8px -4px rgba(15, 23, 42, .12);
        }
        .lp-fas-section-title {
            font-weight: 700;
            font-size: 1rem;
            color: #1f2937;
            margin: 0;
            line-height: 1.25;
            letter-spacing: -.005em;
        }
        .lp-fas-section-sub {
            font-size: .76rem;
            color: #64748b;
            margin-top: 3px;
            line-height: 1.45;
        }
        .lp-fas-list {
            margin: 0;
        }
        .lp-fas-head {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .85rem 1.1rem;
            border-bottom: 1px dashed #e2e8f0;
            background: #fafbfc;
            cursor: pointer;
            user-select: none;
            border-radius: .85rem .85rem 0 0;
        }
        .lp-fas-card.is-collapsed .lp-fas-head {
            border-bottom: none;
            border-radius: .85rem;
        }
        .lp-fas-card.is-open .lp-fas-head {
            border-radius: .85rem .85rem 0 0;
        }
        .lp-fas-head-title {
            font-weight: 700;
            font-size: .98rem;
            color: #1f2937;
            margin: 0;
            line-height: 1.2;
        }
        .lp-fas-head-key {
            font-size: .66rem;
            font-weight: 600;
            letter-spacing: .06em;
            color: #94a3b8;
            text-transform: uppercase;
            margin-top: 2px;
        }
        .lp-fas-head-meta {
            display: inline-flex;
            align-items: center;
            flex-wrap: wrap;
            gap: .35rem;
            margin-left: .5rem;
        }
        .lp-fas-head-meta .lp-fas-pill {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            padding: .2rem .55rem;
            background: #f1f5f9;
            border-radius: 999px;
            font-size: .72rem;
            color: #334155;
            font-weight: 600;
        }
        .lp-fas-head-meta .lp-fas-pill .material-symbols-rounded { font-size: 13px; }
        .lp-fas-head-meta .lp-fas-pill.is-empty { color: #94a3b8; font-weight: 500; }
        .lp-fas-head-meta .lp-fas-pill.is-draft { background: #fef3c7; color: #92400e; }
        .lp-fas-head-meta .lp-fas-pill.is-pub   { background: #dcfce7; color: #15803d; }
        .lp-fas-status { margin-left: auto; flex-shrink: 0; display: inline-flex; align-items: center; gap: .5rem; }
        .lp-fas-chev {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: #fff;
            border: 1px solid #e2e8f0;
            display: inline-flex; align-items: center; justify-content: center;
            color: #475569;
            transition: transform .2s ease, background .15s ease;
        }
        .lp-fas-card.is-open .lp-fas-chev {
            transform: rotate(180deg);
            background: rgba(25, 135, 84, .12);
            color: #15803d;
            border-color: rgba(25, 135, 84, .25);
        }
        .lp-fas-body-wrap {
            display: grid;
            grid-template-rows: 0fr;
            transition: grid-template-rows .25s ease;
        }
        .lp-fas-card.is-open .lp-fas-body-wrap { grid-template-rows: 1fr; }
        .lp-fas-body-inner { overflow: hidden; }

        .lp-fas-body .lp-fas-full { grid-column: 1 / -1; }

        /* === Override .lp-rep-body khusus Fasilitas: form grid 2 kolom konsisten === */
        .lp-fas-body {
            padding: 1rem 1.1rem 1.1rem;
            display: grid;
            grid-template-columns: 1fr;
            gap: .85rem 1rem;
        }
        @media (min-width: 768px) {
            .lp-fas-body {
                grid-template-columns: 1.6fr .9fr;
                gap: .85rem 1rem;
            }
            .lp-fas-body .lp-fas-full { grid-column: 1 / -1; }
        }
        .lp-fas-body .input-group { margin-bottom: 0; }
        .lp-fas-body .input-group .form-control {
            padding-top: 1rem;
            padding-bottom: .35rem;
            font-size: .88rem;
            line-height: 1.4;
        }
        .lp-fas-body .input-group .form-label {
            font-size: .72rem;
            font-weight: 600;
            color: #334155;
            letter-spacing: .01em;
            margin-bottom: 0;
        }
        .lp-fas-body textarea.form-control { min-height: 92px; line-height: 1.55; padding: .65rem .85rem; }

        /* Toggle "Tampilkan di halaman publik" — seragam dengan lp-ps-toggle */
        .lp-fas-toggle {
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            padding: .42rem .7rem .42rem .55rem;
            background: #f1f5f9;
            border-radius: .65rem;
            cursor: pointer;
            user-select: none;
            transition: background .2s ease;
            border: 1px solid transparent;
            height: 44px;
            width: 100%;
        }
        .lp-fas-toggle:hover { background: #e2e8f0; }
        .lp-fas-toggle.is-on {
            background: rgba(25, 135, 84, .12);
            border-color: rgba(25, 135, 84, .25);
        }
        .lp-fas-toggle.is-off {
            background: rgba(100, 116, 139, .12);
            border-color: rgba(100, 116, 139, .2);
        }
        .lp-fas-toggle input[type="checkbox"] {
            position: absolute; opacity: 0; pointer-events: none;
        }
        .lp-fas-toggle-track {
            position: relative;
            width: 34px; height: 18px;
            background: #94a3b8;
            border-radius: 999px;
            transition: background .2s ease;
            flex-shrink: 0;
        }
        .lp-fas-toggle-track::after {
            content: "";
            position: absolute; top: 2px; left: 2px;
            width: 14px; height: 14px;
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 1px 2px rgba(0,0,0,.2);
            transition: transform .2s ease;
        }
        .lp-fas-toggle.is-on .lp-fas-toggle-track { background: #198754; }
        .lp-fas-toggle.is-on .lp-fas-toggle-track::after { transform: translateX(16px); }
        .lp-fas-toggle-icon {
            display: inline-flex; align-items: center; justify-content: center;
            width: 20px; height: 20px;
            border-radius: 50%; font-size: 13px;
            flex-shrink: 0;
        }
        .lp-fas-toggle.is-on  .lp-fas-toggle-icon { background: #198754; color: #fff; }
        .lp-fas-toggle.is-off .lp-fas-toggle-icon { background: #64748b; color: #fff; }
        .lp-fas-toggle-text { font-size: .78rem; font-weight: 700; letter-spacing: .02em; }
        .lp-fas-toggle.is-on  .lp-fas-toggle-text { color: #15803d; }
        .lp-fas-toggle.is-off .lp-fas-toggle-text { color: #475569; }

        /* === Icon select + Toggle publish jadi 1 baris kanan-kiri (full-width) === */
        .lp-fas-body .lp-fas-icon-toggle-row {
            grid-column: 1 / -1;
            display: grid;
            grid-template-columns: 1fr;
            gap: .75rem;
            align-items: end;
        }
        @media (min-width: 768px) {
            .lp-fas-body .lp-fas-icon-toggle-row {
                grid-template-columns: 1fr auto;
                gap: 1rem;
                align-items: end;
            }
            .lp-fas-body .lp-fas-icon-toggle-row .lp-fas-toggle {
                min-width: 240px;
            }
        }
        .lp-fas-body .lp-fas-icon-toggle-row .lp-icon-select-wrap {
            grid-column: auto;
        }
        .lp-fas-body .lp-fas-icon-toggle-row .lp-fas-toggle {
            margin-top: 0;
            width: 100%;
        }
        .lp-fas-body .lp-icon-select-wrap .form-label {
            font-size: .72rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0;
            letter-spacing: .01em;
        }
        .lp-fas-body .lp-icon-select-wrap .select2-container {
            width: 100% !important;
        }
        .lp-fas-body .lp-icon-select-wrap .select2-selection {
            min-height: 42px;
            padding: .25rem .55rem;
            border: 1px solid #e2e8f0;
            border-radius: .55rem;
            background: #fff;
            display: flex;
            align-items: center;
            gap: .55rem;
        }
        .lp-fas-body .lp-icon-select-wrap .select2-selection--single .select2-selection__rendered {
            padding-left: 0;
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            color: #1f2937;
            font-weight: 500;
            font-size: .88rem;
            line-height: 1.2;
        }
        .lp-fas-body .lp-icon-select-wrap .select2-selection--single .select2-selection__placeholder {
            color: #94a3b8;
            font-weight: 500;
        }
        .lp-fas-body .lp-icon-select-wrap .select2-selection--single .select2-selection__arrow {
            height: 40px;
            right: .5rem;
        }
        .lp-fas-body .lp-icon-select-wrap.select2-container--bootstrap-5.select2-container--focus .select2-selection,
        .lp-fas-body .lp-icon-select-wrap.select2-container--bootstrap-5 .select2-selection:focus-within {
            border-color: #1f9d57;
            box-shadow: 0 0 0 2px rgba(31, 157, 87, .12);
        }

        /* Icon preview di opsi & selection */
        .lp-icon-preview {
            width: 22px; height: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            background: linear-gradient(135deg, #ede9fe, #ddd6fe);
            color: #6d28d9;
            flex-shrink: 0;
            font-size: 13px;
        }
        .lp-fas-card.is-new .lp-icon-preview {
            background: linear-gradient(135deg, #e0f2fe, #bae6fd);
            color: #0369a1;
        }
        .select2-results__option .lp-icon-preview {
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
            color: #475569;
        }
        .select2-results__option--highlighted .lp-icon-preview {
            background: rgba(255,255,255,.18);
            color: #fff;
        }

        /* Wrapper "Urutan" di kolom kedua */
        .lp-fas-body .lp-fas-side {
            display: flex;
            flex-direction: column;
            gap: .35rem;
        }

        /* === Background pembeda tiap accordion (alternating warna) === */
        .lp-fas-card:nth-of-type(6n+1) { background: linear-gradient(180deg, #faf5ff 0%, #ffffff 100%); }
        .lp-fas-card:nth-of-type(6n+2) { background: linear-gradient(180deg, #ecfeff 0%, #ffffff 100%); }
        .lp-fas-card:nth-of-type(6n+3) { background: linear-gradient(180deg, #fef3c7 0%, #ffffff 100%); }
        .lp-fas-card:nth-of-type(6n+4) { background: linear-gradient(180deg, #dcfce7 0%, #ffffff 100%); }
        .lp-fas-card:nth-of-type(6n+5) { background: linear-gradient(180deg, #fee2e2 0%, #ffffff 100%); }
        .lp-fas-card:nth-of-type(6n+6) { background: linear-gradient(180deg, #e0e7ff 0%, #ffffff 100%); }

        .lp-fas-card.is-new { background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%) !important; border-style: dashed; }
        .lp-fas-card.is-inactive { background: #f8fafc !important; opacity: .94; }

        /* === Card lebih lengkung + jarak antar accordion diperbesar === */
        #lpFasList {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .lp-fas-card + .lp-fas-card {
            margin-top: 0;
        }
        .lp-fas-card {
            margin-bottom: 0.5rem;
        }
        .lp-fas-card:last-child {
            margin-bottom: 0;
        }
        .lp-fas-card {
            border-radius: 1.15rem !important;
            box-shadow: 0 4px 14px -8px rgba(15, 23, 42, .14);
            border: 1px solid #e2e8f0;
            transition: box-shadow .2s ease, transform .2s ease, border-color .2s ease;
        }
        .lp-fas-card:hover {
            box-shadow: 0 8px 20px -8px rgba(15, 23, 42, .18);
            border-color: #cbd5e1;
        }
        .lp-fas-card .lp-fas-head {
            border-radius: 1.15rem 1.15rem 0 0;
        }
        .lp-fas-card.is-collapsed .lp-fas-head {
            border-radius: 1.15rem;
        }
        .lp-fas-card.is-open .lp-fas-head {
            border-radius: 1.15rem 1.15rem 0 0;
        }
        .lp-fas-card .lp-rep-icon {
            border-radius: 12px;
        }
        .lp-fas-card .lp-fas-chev {
            border-radius: 50%;
        }
        .lp-fas-card .lp-fas-foot {
            border-radius: 0;
        }
        .lp-fas-card.is-open .lp-fas-foot {
            border-bottom-left-radius: 1.15rem;
            border-bottom-right-radius: 1.15rem;
        }

        .lp-fas-foot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .65rem;
            flex-wrap: wrap;
            padding: .65rem 1.1rem;
            border-top: 1px dashed #e2e8f0;
            background: #fafbfc;
        }
        .lp-fas-foot-info {
            font-size: .72rem;
            color: #94a3b8;
            display: inline-flex;
            align-items: center;
            gap: .3rem;
        }
        .lp-fas-foot-info .material-symbols-rounded { font-size: 13px; }
        .lp-fas-foot .btn .material-symbols-rounded { font-size: 16px; line-height: 1; }

        /* Toolbar repeater untuk Fasilitas — flow normal di bawah list, tombol rata kanan */
        .lp-fas-section-card {
            position: relative;
        }
        .lp-fas-toolbar {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: .55rem;
            flex-wrap: wrap;
            margin-top: 0.5rem;
            padding: 0 1.1rem;
            background: transparent;
            border: none;
            border-radius: 0;
            border-top: 1px dashed #e2e8f0;
            padding-top: 0.6rem;
            padding-bottom: .25rem;
        }
        .lp-fas-toolbar .lp-fas-toolbar-info {
            display: none;
        }
        .lp-fas-toolbar .lp-fas-toolbar-actions {
            display: inline-flex;
            gap: .55rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }
        .lp-fas-toolbar .btn {
            border-radius: .6rem;
            padding: .45rem .95rem;
            font-weight: 600;
            font-size: .82rem;
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            box-shadow: 0 4px 10px -4px rgba(15, 23, 42, .25);
        }
        .lp-fas-toolbar .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 14px -4px rgba(15, 23, 42, .3);
        }
        .lp-fas-toolbar .btn .material-symbols-rounded {
            font-size: 16px !important;
        }

        /* Tombol Simpan Semua: pakai biru primer (override Material Kit hijau) */
        .lp-btn-primary {
            --bs-btn-color: #fff;
            --bs-btn-bg: #2563eb;
            --bs-btn-border-color: #2563eb;
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: #1d4ed8;
            --bs-btn-hover-border-color: #1d4ed8;
            --bs-btn-active-color: #fff;
            --bs-btn-active-bg: #1e40af;
            --bs-btn-active-border-color: #1e40af;
            background-color: var(--bs-btn-bg);
            border-color: var(--bs-btn-border-color);
            color: var(--bs-btn-color);
        }
        .lp-btn-primary:hover,
        .lp-btn-primary:focus {
            background-color: var(--bs-btn-hover-bg);
            border-color: var(--bs-btn-hover-border-color);
            color: var(--bs-btn-hover-color);
        }

        /* ============================================================
           Layout halaman Bagian Profil — mengikuti pola
           /app/pengaturan/sop#sopPembayaran:
             - Tanpa hero, langsung sidebar + content
             - Sidebar kiri: card menu daftar anchor
             - Active state via CSS :has(:target), default via :not(:has(:target))
             - Default section = Tinjauan (#bps-overview)
           ============================================================ */
        .bps-shell {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        .bps-menu { gap: 4px !important; }
        .bps-menu a.btn {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 6px 12px;
            color: #0f172a;
            font-weight: 500;
            font-size: 13px;
            transition: all .2s ease;
            position: relative;
        }
        .bps-menu a.btn .mi {
            width: 28px; height: 28px;
            border-radius: 8px;
            display: inline-flex; align-items: center; justify-content: center;
            background: #f1f5f9;
            color: #334155;
            font-size: 14px;
            transition: all .2s ease;
        }
        .bps-menu a.btn:hover {
            border-color: #37d17c;
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(15, 23, 42, .06);
        }
        .bps-menu a.btn:hover .mi {
            background: rgba(55, 209, 124, .15);
            color: #0f9b58;
        }

        /* active state via :target (SOP pattern: dark gradient + white text + accent dot) */
        .bps-setting:not(:has(:target)) .bps-menu a[href="#bps-overview"],
        .bps-wrapper:has(#bps-overview:target) .bps-menu a[href="#bps-overview"],
        .bps-wrapper:has(#bps-sejarah:target)   .bps-menu a[href="#bps-sejarah"],
        .bps-wrapper:has(#bps-visi_misi:target) .bps-menu a[href="#bps-visi_misi"],
        .bps-wrapper:has(#bps-akreditasi:target) .bps-menu a[href="#bps-akreditasi"],
        .bps-wrapper:has(#bps-fasilitas:target) .bps-menu a[href="#bps-fasilitas"] {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #fff;
            border-color: transparent;
            box-shadow: 0 10px 22px rgba(15, 23, 42, .25);
        }
        .bps-setting:not(:has(:target)) .bps-menu a[href="#bps-overview"] .mi,
        .bps-wrapper:has(#bps-overview:target) .bps-menu a[href="#bps-overview"] .mi,
        .bps-wrapper:has(#bps-sejarah:target)   .bps-menu a[href="#bps-sejarah"] .mi,
        .bps-wrapper:has(#bps-visi_misi:target) .bps-menu a[href="#bps-visi_misi"] .mi,
        .bps-wrapper:has(#bps-akreditasi:target) .bps-menu a[href="#bps-akreditasi"] .mi,
        .bps-wrapper:has(#bps-fasilitas:target) .bps-menu a[href="#bps-fasilitas"] .mi {
            background: rgba(255, 255, 255, .14);
            color: #fff;
        }
        .bps-setting:not(:has(:target)) .bps-menu a[href="#bps-overview"]::after,
        .bps-wrapper:has(#bps-overview:target) .bps-menu a[href="#bps-overview"]::after,
        .bps-wrapper:has(#bps-sejarah:target)   .bps-menu a[href="#bps-sejarah"]::after,
        .bps-wrapper:has(#bps-visi_misi:target) .bps-menu a[href="#bps-visi_misi"]::after,
        .bps-wrapper:has(#bps-akreditasi:target) .bps-menu a[href="#bps-akreditasi"]::after,
        .bps-wrapper:has(#bps-fasilitas:target) .bps-menu a[href="#bps-fasilitas"]::after {
            content: "";
            position: absolute;
            right: 14px; top: 50%;
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #37d17c;
            transform: translateY(-50%);
            box-shadow: 0 0 0 4px rgba(55, 209, 124, .25);
        }

        /* Konten: 1 tombol 1 konten (SOP pattern) */
        .bps-content { display: none; animation: bpsFade .3s ease; }
        .bps-content:target { display: block; }
        .bps-setting:not(:has(:target)) #bps-overview { display: block; }

        @keyframes bpsFade {
            from { opacity: 0; transform: translateY(6px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .bps-card,
        .bps-shell .card.bps-card {
            border-radius: 0.85rem !important;
            border: 1px solid rgba(15, 23, 42, .08) !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.05) !important;
            overflow: hidden;
        }
        .bps-card .card-header {
            background: linear-gradient(135deg, #f8fafc, #eef2ff);
            border-bottom: 1px dashed rgba(15, 23, 42, .08);
            padding: 14px 18px;
            display: flex; align-items: center; gap: 12px;
        }
        .bps-card > .card-body.bps-menu { flex: 0 0 auto; }
        .bps-card .card-header .header-icon {
            width: 32px; height: 32px;
            border-radius: 9px;
            background: linear-gradient(135deg, rgba(55, 209, 124, .15), rgba(15, 118, 110, .15));
            color: #0f9b58;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 15px;
        }
        .bps-card .card-header h5 { margin: 0; font-weight: 700; color: #0f172a; }
        .bps-card .card-header .sub { font-size: 12px; color: #64748b; margin-top: 2px; }
        .bps-card .card-header .target-id {
            margin-left: auto;
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
            font-size: 11px;
            color: #94a3b8;
            background: rgba(15, 23, 42, .04);
            padding: 2px 8px;
            border-radius: 999px;
        }

        /* ---------- Responsive ---------- */
        @media (max-width: 767.98px) {
            .bps-menu a.btn { padding: 10px 12px; font-size: 13px; }
            .bps-menu a.btn .mi { width: 30px; height: 30px; font-size: 14px; }
            .bps-card .card-header { padding: 12px 14px; gap: 10px; }
            .bps-card .card-header h5 { font-size: 15px; }
            .bps-card .card-header .sub { font-size: 11px; }
            .bps-card .card-header .header-icon { width: 30px; height: 30px; font-size: 14px; }
            .bps-wrapper .bps-menu a.btn::after { display: none !important; }
        }
        @media (max-width: 575.98px) {
            .bps-card .card-body { padding: 14px; }
        }
    </style>
@endsection

@section('content')
@php
    $iconMap = [
        'overview'     => ['fa' => 'fa-eye',               'css' => 'is-overview'],
        'sejarah'      => ['fa' => 'fa-clock-rotate-left', 'css' => 'is-sejarah'],
        'visi_misi'    => ['fa' => 'fa-bullseye',          'css' => 'is-visi_misi'],
        'akreditasi'   => ['fa' => 'fa-circle-check',      'css' => 'is-akreditasi'],
    ];
    $labelMap = [
        'overview'     => 'Tinjauan',
        'sejarah'      => 'Sejarah',
        'visi_misi'    => 'Visi & Misi',
        'akreditasi'   => 'Akreditasi',
    ];
    $helpMap = [
        'overview'   => 'Ceritakan ringkasan sekolah. Bisa beberapa paragraf. Badge & NPSN tampil sebagai chip kecil di hero card.',
        'sejarah'    => 'Ceritakan perjalanan & pencapaian sekolah dari waktu ke waktu.',
        'visi_misi'  => 'Format yang disarankan: <code>&lt;h3&gt;Visi Kami&lt;/h3&gt;</code> lalu paragraf, kemudian <code>&lt;h3&gt;Misi Kami&lt;/h3&gt;</code> dan daftar bernomor.',
        'akreditasi' => 'Jelaskan status akreditasi. Badge akan tampil sebagai label aksen di card.',
    ];
    // Daftar anchor sidebar (untuk card Sections menu)
    $bpsSidebarItems = [
        ['id' => 'overview',   'label' => 'Tinjauan',     'icon' => 'eye-fill'],
        ['id' => 'sejarah',    'label' => 'Sejarah',      'icon' => 'history'],
        ['id' => 'visi_misi',  'label' => 'Visi & Misi',  'icon' => 'track_changes'],
        ['id' => 'akreditasi', 'label' => 'Akreditasi',   'icon' => 'verified'],
        ['id' => 'fasilitas',  'label' => 'Fasilitas',    'icon' => 'apartment'],
    ];
@endphp

<div class="container-fluid py-4 bps-setting bps-shell">
    @if (session('success'))
        <div class="alert alert-success py-2 small mb-3">{{ session('success') }}</div>
    @endif
<div class="row bps-wrapper g-3 align-items-stretch">
        {{-- ===== Sidebar ===== --}}
        <div class="col-lg-3 col-md-4 d-flex">
            <div class="card bps-card shadow-sm w-100">
                <div class="card-header">
                    <div class="header-icon">
                        <i class="fas fa-bookmark"></i>
                    </div>
                    <div>
                        <h5>Section Profil</h5>
                        <div class="sub">Pilih modul untuk diedit</div>
                    </div>
                </div>
                <div class="card-body d-grid gap-1 bps-menu">
                    <a href="#bps-overview" class="btn text-start">
                        <span class="mi"><i class="fas fa-eye"></i></span>
                        <span>Tinjauan</span>
                    </a>
                    <a href="#bps-sejarah" class="btn text-start">
                        <span class="mi"><i class="fas fa-clock-rotate-left"></i></span>
                        <span>Sejarah</span>
                    </a>
                    <a href="#bps-visi_misi" class="btn text-start">
                        <span class="mi"><i class="fas fa-bullseye"></i></span>
                        <span>Visi &amp; Misi</span>
                    </a>
                    <a href="#bps-akreditasi" class="btn text-start">
                        <span class="mi"><i class="fas fa-circle-check"></i></span>
                        <span>Akreditasi</span>
                    </a>
                    <a href="#bps-fasilitas" class="btn text-start">
                        <span class="mi"><i class="fas fa-building"></i></span>
                        <span>Fasilitas</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- ===== Content ===== --}}
        <div class="col-lg-9 col-md-8 d-flex flex-column">
            @if ($items->isEmpty())
                <div class="bps-content card bps-card shadow-sm" id="bps-overview">
                    <div class="card-header">
                        <div class="header-icon"><i class="fas fa-circle-info"></i></div>
                        <div>
                            <h5>Section belum tersedia</h5>
                            <div class="sub">Default Tinjauan akan tampil setelah data tersedia.</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="lp-ps-empty">
                            <span class="material-symbols-rounded">article</span>
                            <div class="mt-2">Belum ada section.</div>
                        </div>
                    </div>
                </div>
            @else
                <div class="lp-ps-stack">
                    @foreach ($items as $row)
                        @php
                            $ic = $iconMap[$row->section_key] ?? ['fa' => 'fa-file-lines', 'css' => ''];
                            $label = $labelMap[$row->section_key] ?? $row->section_key;
                            $help = $helpMap[$row->section_key] ?? null;
                            $k = $row->section_key;
                            $showBadge = in_array($k, ['overview', 'akreditasi']);
                        @endphp

                <div class="bps-content card bps-card shadow-sm" id="bps-{{ $k }}">
                    <div class="card-header">
                        <div class="header-icon"><i class="fas {{ $ic['fa'] }}"></i></div>
                        <div>
                            <h5>{{ $label }}</h5>
                            <div class="sub">section: {{ $k }}</div>
                        </div>
                        <div class="target-id">#bps-{{ $k }}</div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('app.admin-landing.profile-sections.update', $row->id) }}"
                              method="POST" class="lp-ajax lp-ps-card {{ $ic['css'] }} {{ $row->is_active ? '' : 'is-inactive' }}"
                              data-section-key="{{ $k }}">
                            @csrf
                            @method('PUT')

                    {{-- ====== HEADER ====== --}}
                    <div class="lp-ps-head">
                        <div class="lp-ps-icon {{ $ic['css'] }}"><i class="fas {{ $ic['fa'] }}"></i></div>
                        <div class="min-w-0">
                            <h6 class="lp-ps-title">{{ $label }}</h6>
                            <div class="lp-ps-key">section: {{ $k }}</div>
                        </div>
                        <div class="lp-ps-status">
                            <label class="lp-ps-toggle {{ $row->is_active ? 'is-on' : 'is-off' }}"
                                   for="is_active_{{ $row->id }}">
                                <input type="checkbox"
                                       name="is_active"
                                       id="is_active_{{ $row->id }}"
                                       value="1"
                                       {{ old('is_active', $row->is_active) ? 'checked' : '' }}>
                                <span class="lp-ps-toggle-track" aria-hidden="true"></span>
                                <span class="lp-ps-toggle-icon" aria-hidden="true">
                                    <i class="bi {{ $row->is_active ? 'bi-check-lg' : 'bi-x-lg' }}"></i>
                                </span>
                                <span class="lp-ps-toggle-text">
                                    {{ $row->is_active ? 'Aktif' : 'Non-aktif' }}
                                </span>
                            </label>
                        </div>
                    </div>

                    {{-- ====== FIELDS ====== --}}
                    <div class="lp-ps-form-grid">
                        <div class="input-group input-group-outline mb-0 @if(old('title', $row->title)) is-filled @endif">
                            <label class="form-label">Judul <span class="text-danger">*</span></label>
                            <input type="text" name="title" required maxlength="200"
                                   value="{{ old('title', $row->title) }}"
                                   class="form-control" placeholder="Cth: Visi &amp; Misi Sekolah">
                        </div>

                        @if ($showBadge)
                            <div class="input-group input-group-outline mb-0 @if(old('badge_text', $row->badge_text)) is-filled @endif">
                                <label class="form-label">Badge</label>
                                <input type="text" name="badge_text" maxlength="100"
                                       value="{{ old('badge_text', $row->badge_text) }}"
                                       class="form-control" placeholder="Cth: Akreditasi A">
                            </div>
                            <div class="input-group input-group-outline mb-0 @if(old('extra_label', $row->extra_label)) is-filled @endif">
                                <label class="form-label">Label</label>
                                <input type="text" name="extra_label" maxlength="100"
                                       value="{{ old('extra_label', $row->extra_label) }}"
                                       class="form-control" placeholder="Cth: NPSN">
                            </div>
                            <div class="input-group input-group-outline mb-0 @if(old('badge_extra', $row->badge_extra)) is-filled @endif">
                                <label class="form-label">Nilai</label>
                                <input type="text" name="badge_extra" maxlength="100"
                                       value="{{ old('badge_extra', $row->badge_extra) }}"
                                       class="form-control" placeholder="Cth: 20212345">
                            </div>
                        @endif

                        <div class="lp-ps-full">
                            @if ($help)
                                <div class="lp-ps-field-help">{!! $help !!}</div>
                            @endif
                            <textarea name="content"
                                      rows="{{ $k === 'visi_misi' ? 5 : ($k === 'overview' ? 4 : 3) }}"
                                      class="form-control {{ $k === 'visi_misi' ? 'lp-tinymce' : 'lp-plain' }}"
                                      placeholder="Tulis konten di sini...">{{ old('content', $row->content) }}</textarea>
                        </div>
                    </div>

                    {{-- ====== FOOTER / SAVE ====== --}}
                    <div class="lp-ps-foot">
                        <div class="lp-ps-foot-info">
                            <span class="material-symbols-rounded">cloud_done</span>
                            Perubahan langsung tersinkron ke halaman publik.
                        </div>
                        <button type="submit" class="btn btn-info">
                            <span class="material-symbols-rounded align-middle" style="font-size:17px;">save</span>
                            Simpan
                        </button>
                    </div>
                </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ============ FASILITAS (accordion repeater) ============ --}}
    <div class="bps-content card bps-card shadow-sm" id="bps-fasilitas">
        <div class="card-header">
            <div class="header-icon"><i class="bi bi-building"></i></div>
            <div>
                <h5>Fasilitas Sekolah</h5>
                <div class="sub">Daftar fasilitas tampil di section Fasilitas halaman profil</div>
            </div>
            <div class="target-id">#bps-fasilitas</div>
        </div>
        <div class="card-body">
    <div class="lp-fas-section-card" id="fasilitas-section">
        <div class="lp-fas-section-head">
            <div class="lp-fas-section-icon">
                <i class="bi bi-building"></i>
            </div>
            <div class="min-w-0">
                <h6 class="lp-fas-section-title">Fasilitas Sekolah</h6>
                <div class="lp-fas-section-sub">Daftar fasilitas tampil di section Fasilitas halaman profil. Klik kartu untuk membuka form edit.</div>
            </div>
        </div>

        <div id="lpFasList" class="lp-rep-stack lp-fas-list">
            @forelse ($fasilitasItems as $row)
                @php
                    $rowIndex = $loop->iteration;
                    $rowId = (int) $row->id;
                    $titleVal = $row->title ?? '';
                    $iconVal = $row->icon ?? '';
                    $sortVal = (int) ($row->sort_order ?: 0);
                    $descVal = $row->description ?? '';
                    $pub = (bool) $row->is_published;
                    $descShort = $descVal !== '' ? \Illuminate\Support\Str::limit($descVal, 50) : null;
                @endphp
                <div class="lp-rep-card lp-fas-card is-collapsed {{ $pub ? '' : 'is-inactive' }}" data-id="{{ $rowId }}" data-row-index="{{ $rowIndex }}">
                    <div class="lp-fas-head" data-role="toggle">
                        <div class="min-w-0 flex-grow-1">
                            <h6 class="lp-fas-head-title mb-0">{{ $titleVal ?: 'Fasilitas #' . $rowIndex }}</h6>
                        </div>
                        <div class="lp-fas-head-meta">
                            <span class="lp-fas-pill {{ $pub ? 'is-pub' : 'is-draft' }}">
                                <span class="material-symbols-rounded">{{ $pub ? 'cloud_done' : 'edit_note' }}</span>
                                {{ $pub ? 'Aktif' : 'Draft' }}
                            </span>
                        </div>
                        <div class="lp-fas-status">
                            <span class="lp-fas-chev" aria-hidden="true">
                                <span class="material-symbols-rounded" style="font-size:18px;">expand_more</span>
                            </span>
                        </div>
                    </div>

                    <div class="lp-fas-body-wrap" data-role="body-wrap">
                        <div class="lp-fas-body-inner">
                            <div class="lp-fas-body">
                                <div class="input-group input-group-outline mb-0 @if ($titleVal) is-filled @endif">
                                    <label class="form-label">Judul <span class="text-danger">*</span></label>
                                    <input type="text" name="rows[{{ $rowIndex }}][title]" class="form-control" required maxlength="150" value="{{ $titleVal }}" placeholder="Cth: Ruang Kelas Modern">
                                </div>

                                <div class="lp-fas-side">
                                    <div class="input-group input-group-outline mb-0 @if ($sortVal > 0) is-filled @endif">
                                        <label class="form-label">Urutan</label>
                                        <input type="number" name="rows[{{ $rowIndex }}][sort_order]" class="form-control" min="0" value="{{ $sortVal }}">
                                    </div>
                                </div>

                                <div class="lp-fas-icon-toggle-row">
                                    <div class="lp-icon-select-wrap">
                                        <label class="form-label">Icon</label>
                                        @include('admin-landing._komponen._pilih-icon', ['selectId' => 'lpFasIcon' . $rowIndex, 'selected' => $iconVal])
                                        <input type="hidden" name="rows[{{ $rowIndex }}][icon]" value="{{ $iconVal }}" data-role="icon-value">
                                    </div>

                                    <label class="lp-fas-toggle {{ $pub ? 'is-on' : 'is-off' }}"
                                           for="is_published_{{ $rowId }}">
                                        <input type="checkbox"
                                               name="rows[{{ $rowIndex }}][is_published]"
                                               id="is_published_{{ $rowId }}"
                                               value="1"
                                               {{ $pub ? 'checked' : '' }}
                                               data-role="published-toggle">
                                        <span class="lp-fas-toggle-track" aria-hidden="true"></span>
                                        <span class="lp-fas-toggle-icon" aria-hidden="true">
                                            <i class="bi {{ $pub ? 'bi-check-lg' : 'bi-x-lg' }}"></i>
                                        </span>
                                        <span class="lp-fas-toggle-text">{{ $pub ? 'Tampil di publik' : 'Disimpan draft' }}</span>
                                    </label>
                                </div>

                                <div class="lp-fas-full">
                                    <div class="input-group input-group-outline mb-0 @if ($descVal) is-filled @endif">
                                        <label class="form-label">Deskripsi</label>
                                        <textarea name="rows[{{ $rowIndex }}][description]" class="form-control" rows="2" placeholder="Cth: Ruang belajar nyaman dengan AC, proyektor...">{{ $descVal }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="lp-fas-foot">
                                <div class="lp-fas-foot-info">
                                    <span class="material-symbols-rounded">{{ $pub ? 'cloud_done' : 'edit_note' }}</span>
                                    {{ $pub ? 'Fasilitas ini tampil di halaman publik.' : 'Disimpan sebagai draft.' }}
                                </div>
                                <div class="d-flex gap-2 flex-wrap">
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-lp-fas-delete
                                            data-url="{{ route('app.admin-landing.fasilitas.destroy', $rowId) }}"
                                            data-name="{{ $titleVal }}">
                                        <span class="material-symbols-rounded">delete</span>
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="lp-rep-card lp-rep-empty" id="lpFasEmpty">
                    <span class="material-symbols-rounded">apartment</span>
                    <div class="mt-2 mb-2">Belum ada fasilitas.</div>
                    <div class="small text-muted">Tambah baris pertama di bawah untuk mulai.</div>
                </div>
            @endforelse
        </div>

        <div class="lp-rep-toolbar lp-fas-toolbar">
            <div class="lp-fas-toolbar-info">
                <span class="material-symbols-rounded">tips_and_updates</span>
                Tambah baris baru lalu klik <strong>Simpan Semua</strong> untuk mengirim perubahan.
            </div>
            <div class="lp-fas-toolbar-actions">
                <button type="button" class="btn btn-sm btn-outline-success" id="lpFasAddBtn">
                    <span class="material-symbols-rounded">add</span>
                    Tambah Baris
                </button>
                <button type="button" class="btn btn-sm lp-btn-primary" id="lpFasSaveAll">
                    <span class="material-symbols-rounded">save</span>
                    Simpan Semua
                </button>
            </div>
        </div>
    </div>

    <template id="lpFasRowTemplate">
        <div class="lp-rep-card lp-fas-card is-new is-collapsed" data-id="">
            <div class="lp-fas-head" data-role="toggle">
                <div class="min-w-0 flex-grow-1">
                    <h6 class="lp-fas-head-title mb-0">Fasilitas Baru</h6>
                </div>
                <div class="lp-fas-head-meta">
                    <span class="lp-fas-pill is-draft">
                        <span class="material-symbols-rounded">edit_note</span>
                        Draft
                    </span>
                </div>
                <div class="lp-fas-status">
                    <span class="lp-fas-chev" aria-hidden="true">
                        <span class="material-symbols-rounded" style="font-size:18px;">expand_more</span>
                    </span>
                </div>
            </div>

            <div class="lp-fas-body-wrap" data-role="body-wrap">
                <div class="lp-fas-body-inner">
                    <div class="lp-fas-body">
                        <div class="input-group input-group-outline mb-0">
                            <label class="form-label">Judul <span class="text-danger">*</span></label>
                            <input type="text" name="rows[__INDEX__][title]" class="form-control" required maxlength="150" value="" placeholder="Cth: Ruang Kelas Modern">
                        </div>

                        <div class="lp-fas-side">
                            <div class="input-group input-group-outline mb-0">
                                <label class="form-label">Urutan</label>
                                <input type="number" name="rows[__INDEX__][sort_order]" class="form-control" min="0" value="__SORT__">
                            </div>
                        </div>

                        <div class="lp-fas-icon-toggle-row">
                            <div class="lp-icon-select-wrap">
                                <label class="form-label">Icon</label>
                                <select class="form-select lp-icon-select" id="lpFasIconTemplate__INDEX__" data-placeholder="Pilih icon...">
                                    <option value=""></option>
                                </select>
                                <input type="hidden" name="rows[__INDEX__][icon]" value="bi-building" data-role="icon-value">
                            </div>

                            <label class="lp-fas-toggle is-on" for="is_published_new___INDEX__">
                                <input type="checkbox"
                                       name="rows[__INDEX__][is_published]"
                                       id="is_published_new___INDEX__"
                                       value="1"
                                       checked
                                       data-role="published-toggle">
                                <span class="lp-fas-toggle-track" aria-hidden="true"></span>
                                <span class="lp-fas-toggle-icon" aria-hidden="true">
                                    <i class="bi bi-check-lg"></i>
                                </span>
                                <span class="lp-fas-toggle-text">Tampil di publik</span>
                            </label>
                        </div>

                        <div class="lp-fas-full">
                            <div class="input-group input-group-outline mb-0">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="rows[__INDEX__][description]" class="form-control" rows="2" placeholder="Cth: Ruang belajar nyaman dengan AC, proyektor..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="lp-fas-foot">
                        <div class="lp-fas-foot-info">
                            <span class="material-symbols-rounded">edit_note</span>
                            Baris baru — belum tersimpan.
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-remove-row" data-role="remove">
                                <span class="material-symbols-rounded" style="font-size:16px;">close</span>
                                Hapus Baris
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
    </div>
        </div>
    </div>
</div>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof tinymce !== 'undefined') {
        tinymce.init({
            selector: 'textarea.lp-tinymce',
            height: 240,
            menubar: false,
            plugins: 'lists link',
            toolbar: 'undo redo | bold italic underline | bullist numlist | link | removeformat',
            branding: false,
            statusbar: true,
            elementpath: false,
            promotion: false,
        });
    }

    // Toggle custom sinkron (icon + class + label + card dim)
    document.querySelectorAll('.lp-ps-toggle input[type="checkbox"]').forEach(function (el) {
        var wrap = el.closest('.lp-ps-toggle');
        var card = el.closest('.lp-ps-card');
        var icon = wrap.querySelector('.lp-ps-toggle-icon i');
        var text = wrap.querySelector('.lp-ps-toggle-text');
        var update = function () {
            if (el.checked) {
                wrap.classList.remove('is-off');
                wrap.classList.add('is-on');
                icon.className = 'bi bi-check-lg';
                text.textContent = 'Aktif';
                if (card) card.classList.remove('is-inactive');
            } else {
                wrap.classList.remove('is-on');
                wrap.classList.add('is-off');
                icon.className = 'bi bi-x-lg';
                text.textContent = 'Non-aktif';
                if (card) card.classList.add('is-inactive');
            }
        };
        el.addEventListener('change', update);
        update();
    });

    // Mini toggle untuk is_published / is_lead (khusus Fasilitas, jika masih ada)
    document.querySelectorAll('.lp-ps-mini-toggle input[type="checkbox"]').forEach(function (el) {
        var wrap = el.closest('.lp-ps-mini-toggle');
        var card = el.closest('.lp-ps-list-card, .lp-fas-card');
        var text = wrap.querySelector('span:last-child');
        var update = function () {
            if (el.checked) {
                wrap.classList.remove('is-off');
                wrap.classList.add('is-on');
                if (text && wrap.dataset.dynamic !== '0') {
                    if (wrap.querySelector('[id$="_published"]') || wrap.querySelector('[id^="new_"][id$="_published"]')) {
                        text.textContent = 'Aktif';
                    }
                }
                if (card && el.name === 'is_published') card.classList.remove('is-inactive');
            } else {
                wrap.classList.remove('is-on');
                wrap.classList.add('is-off');
                if (text && wrap.dataset.dynamic !== '0') {
                    if (wrap.querySelector('[id$="_published"]') || wrap.querySelector('[id^="new_"][id$="_published"]')) {
                        text.textContent = 'Non-aktif';
                    }
                }
                if (card && el.name === 'is_published') card.classList.add('is-inactive');
            }
        };
        el.addEventListener('change', update);
        update();
    });

    // ============ FASILITAS — Icon Select2 (daftar icon Bootstrap + FontAwesome) ============
    var ICON_OPTIONS = [
        // Bootstrap Icons
        { id: 'bi-building',           text: 'Gedung / Bangunan (bi-building)' },
        { id: 'bi-apartment',          text: 'Apartemen (bi-apartment)' },
        { id: 'bi-house',              text: 'Rumah (bi-house)' },
        { id: 'bi-easel',              text: 'Papan / Kelas (bi-easel)' },
        { id: 'bi-mortarboard',        text: 'Topi Toga (bi-mortarboard)' },
        { id: 'bi-book',               text: 'Buku (bi-book)' },
        { id: 'bi-books',              text: 'Koleksi Buku (bi-books)' },
        { id: 'bi-library',            text: 'Perpustakaan (bi-library)' },
        { id: 'bi-journal-text',       text: 'Jurnal (bi-journal-text)' },
        { id: 'bi-cpu',                text: 'CPU / Lab Komputer (bi-cpu)' },
        { id: 'bi-pc-display',         text: 'PC Desktop (bi-pc-display)' },
        { id: 'bi-laptop',             text: 'Laptop (bi-laptop)' },
        { id: 'bi-wifi',               text: 'Wi-Fi (bi-wifi)' },
        { id: 'bi-globe',              text: 'Internet / Global (bi-globe)' },
        { id: 'bi-lightbulb',          text: 'Lampu / Ide (bi-lightbulb)' },
        { id: 'bi-lamp',               text: 'Lampu (bi-lamp)' },
        { id: 'bi-thermometer',        text: 'AC / Suhu (bi-thermometer)' },
        { id: 'bi-fan',                text: 'Kipas / Ventilasi (bi-fan)' },
        { id: 'bi-droplet',            text: 'Air (bi-droplet)' },
        { id: 'bi-cup-hot',            text: 'Kantin / Minuman (bi-cup-hot)' },
        { id: 'bi-cup-straw',          text: 'Minuman (bi-cup-straw)' },
        { id: 'bi-basket',             text: 'Basket (bi-basket)' },
        { id: 'bi-trophy',             text: 'Trofi / Prestasi (bi-trophy)' },
        { id: 'bi-medal',              text: 'Medali (bi-medal)' },
        { id: 'bi-star-fill',          text: 'Bintang (bi-star-fill)' },
        { id: 'bi-flag',               text: 'Bendera (bi-flag)' },
        { id: 'bi-megaphone',          text: 'Pengumuman (bi-megaphone)' },
        { id: 'bi-mic',                text: 'Mikrofon (bi-mic)' },
        { id: 'bi-camera',             text: 'Kamera (bi-camera)' },
        { id: 'bi-palette',            text: 'Seni / Warna (bi-palette)' },
        { id: 'bi-music-note-beamed',  text: 'Musik (bi-music-note-beamed)' },
        { id: 'bi-people',             text: 'Orang / Siswa (bi-people)' },
        { id: 'bi-person-arms-up',     text: 'Aktivitas (bi-person-arms-up)' },
        { id: 'bi-bicycle',            text: 'Sepeda (bi-bicycle)' },
        { id: 'bi-bus-front',          text: 'Bus / Transport (bi-bus-front)' },
        { id: 'bi-tree',               text: 'Pohon / Taman (bi-tree)' },
        { id: 'bi-flower1',            text: 'Bunga / Taman (bi-flower1)' },
        { id: 'bi-geo-alt',            text: 'Lokasi (bi-geo-alt)' },
        { id: 'bi-shield-check',       text: 'Keamanan (bi-shield-check)' },
        { id: 'bi-first-aid',          text: 'P3K / UKS (bi-first-aid)' },
        { id: 'bi-hospital',           text: 'Ruang UKS (bi-hospital)' },
        { id: 'bi-piggy-bank',         text: 'Tabungan (bi-piggy-bank)' },
        { id: 'bi-cash-coin',          text: 'Keuangan (bi-cash-coin)' },
        { id: 'bi-calculator',         text: 'Kalkulator (bi-calculator)' },
        { id: 'bi-clipboard-data',     text: 'Data / Nilai (bi-clipboard-data)' },

        // FontAwesome 6 Solid
        { id: 'fa-solid fa-school',           text: 'Sekolah (fa-school)' },
        { id: 'fa-solid fa-graduation-cap',   text: 'Wisuda (fa-graduation-cap)' },
        { id: 'fa-solid fa-chalkboard',       text: 'Papan Tulis (fa-chalkboard)' },
        { id: 'fa-solid fa-chalkboard-user',  text: 'Pengajar (fa-chalkboard-user)' },
        { id: 'fa-solid fa-book-open',        text: 'Buku Terbuka (fa-book-open)' },
        { id: 'fa-solid fa-flask',            text: 'Lab IPA (fa-flask)' },
        { id: 'fa-solid fa-microscope',       text: 'Mikroskop (fa-microscope)' },
        { id: 'fa-solid fa-laptop-code',      text: 'Lab Komputer (fa-laptop-code)' },
        { id: 'fa-solid fa-wifi',             text: 'Wi-Fi (fa-wifi)' },
        { id: 'fa-solid fa-bolt',             text: 'Listrik (fa-bolt)' },
        { id: 'fa-solid fa-faucet',           text: 'Air (fa-faucet)' },
        { id: 'fa-solid fa-fire-extinguisher',text: 'Pemadam (fa-fire-extinguisher)' },
        { id: 'fa-solid fa-medkit',           text: 'P3K (fa-medkit)' },
        { id: 'fa-solid fa-bus-school',       text: 'Bus Sekolah (fa-bus-school)' },
        { id: 'fa-solid fa-bicycle',          text: 'Sepeda (fa-bicycle)' },
        { id: 'fa-solid fa-futbol',           text: 'Olahraga Bola (fa-futbol)' },
        { id: 'fa-solid fa-basketball',       text: 'Basket (fa-basketball)' },
        { id: 'fa-solid fa-volleyball',       text: 'Voli (fa-volleyball)' },
        { id: 'fa-solid fa-person-running',   text: 'Lari (fa-person-running)' },
        { id: 'fa-solid fa-music',            text: 'Musik (fa-music)' },
        { id: 'fa-solid fa-palette',          text: 'Seni (fa-palette)' },
        { id: 'fa-solid fa-camera',           text: 'Kamera (fa-camera)' },
        { id: 'fa-solid fa-couch',            text: 'Ruang Tamu (fa-couch)' },
        { id: 'fa-solid fa-utensils',         text: 'Kantin (fa-utensils)' },
        { id: 'fa-solid fa-mug-hot',          text: 'Minuman (fa-mug-hot)' },
        { id: 'fa-solid fa-tree',             text: 'Pohon (fa-tree)' },
        { id: 'fa-solid fa-leaf',             text: 'Daun / Hijau (fa-leaf)' },
        { id: 'fa-solid fa-shield-halved',    text: 'Keamanan (fa-shield-halved)' },
        { id: 'fa-solid fa-door-open',        text: 'Pintu (fa-door-open)' },
        { id: 'fa-solid fa-lightbulb',        text: 'Lampu (fa-lightbulb)' },
        { id: 'fa-solid fa-trophy',           text: 'Trofi (fa-trophy)' },
        { id: 'fa-solid fa-medal',            text: 'Medali (fa-medal)' },
        { id: 'fa-solid fa-star',             text: 'Bintang (fa-star)' },
        { id: 'fa-solid fa-flag',             text: 'Bendera (fa-flag)' },
        { id: 'fa-solid fa-users',            text: 'Siswa (fa-users)' },
        { id: 'fa-solid fa-user-graduate',    text: 'Alumni (fa-user-graduate)' },
        { id: 'fa-solid fa-globe',            text: 'Global (fa-globe)' },
        { id: 'fa-solid fa-map-location-dot', text: 'Lokasi (fa-map-location-dot)' },
        { id: 'fa-solid fa-comments',         text: 'Diskusi (fa-comments)' },
        { id: 'fa-solid fa-megaphone',        text: 'Pengumuman (fa-megaphone)' },
        { id: 'fa-solid fa-calculator',       text: 'Kalkulator (fa-calculator)' },
        { id: 'fa-solid fa-coins',            text: 'Keuangan (fa-coins)' },
    ];

    function makeIconEl(value) {
        var span = document.createElement('span');
        span.className = 'lp-icon-preview';
        var cls = value || '';
        var i = document.createElement('i');
        if (cls.indexOf('fa-') === 0) {
            i.className = cls;
        } else if (cls.indexOf('bi-') === 0) {
            i.className = 'bi ' + cls;
        }
        span.appendChild(i);
        return span;
    }

    function iconLabelText(value) {
        var found = ICON_OPTIONS.filter(function (o) { return o.id === value; })[0];
        return found ? found.text : value;
    }

    function initIconSelect2($el, currentValue) {
        if (!$el.length) return;
        var initVal = currentValue !== undefined ? currentValue : $el.val();
        var data = [{ id: '', text: '' }];
        ICON_OPTIONS.forEach(function (o) {
            data.push({ id: o.id, text: o.text, icon: o.id });
        });
        $el.empty();
        $el.select2({
            theme: 'bootstrap-5',
            width: '100%',
            data: data,
            placeholder: $el.data('placeholder') || 'Pilih icon...',
            allowClear: true,
            minimumResultsForSearch: 0,
            templateResult: function (state) {
                if (!state.id) return state.text || '';
                var $wrap = jQuery('<span style="display:inline-flex;align-items:center;gap:.55rem;"></span>');
                $wrap.append(makeIconEl(state.id));
                $wrap.append(jQuery('<span></span>').text(state.text || iconLabelText(state.id)));
                return $wrap;
            },
            templateSelection: function (state) {
                if (!state.id) {
                    return jQuery('<span style="color:#94a3b8;font-weight:500;"></span>').text('Pilih icon...');
                }
                var $wrap = jQuery('<span style="display:inline-flex;align-items:center;gap:.55rem;"></span>');
                $wrap.append(makeIconEl(state.id));
                $wrap.append(jQuery('<span></span>').text(state.text || iconLabelText(state.id)));
                return $wrap;
            },
        });
        if (initVal) {
            $el.val(initVal).trigger('change');
        }
    }

    // Init semua select icon yang sudah ada di DOM (existing rows)
    if (window.jQuery && jQuery.fn.select2) {
        jQuery('#lpFasList .lp-icon-select').each(function () {
            var $sel = jQuery(this);
            // Cari hidden input untuk nilai awal
            var wrap = $sel.closest('.lp-icon-select-wrap');
            var hidden = wrap.find('input[data-role="icon-value"]');
            var initVal = hidden.length ? hidden.val() : '';
            initIconSelect2($sel, initVal);
            $sel.on('change', function () {
                if (hidden.length) hidden.val($sel.val() || '');
            });
        });
    }

    // ============ FASILITAS — Accordion toggle (collapse header) ============
    function bindFasToggle(card) {
        var head = card.querySelector('[data-role="toggle"]');
        if (!head || head.__lpFasBound) return;
        head.__lpFasBound = true;
        head.addEventListener('click', function (e) {
            if (e.target.closest('input, button, label, select, textarea, a, .select2')) return;
            card.classList.toggle('is-collapsed');
            card.classList.toggle('is-open');
        });
    }
    document.querySelectorAll('#lpFasList .lp-fas-card').forEach(function (card) {
        if (!card.classList.contains('is-open')) card.classList.add('is-collapsed');
        bindFasToggle(card);
    });

    // ============ FASILITAS — Toggle publish (konsisten dengan lp-ps-toggle) ============
    function bindFasPublishToggle(scope) {
        scope.querySelectorAll('.lp-fas-toggle input[type="checkbox"][data-role="published-toggle"]').forEach(function (el) {
            if (el.__lpFasPubBound) return;
            el.__lpFasPubBound = true;
            var wrap = el.closest('.lp-fas-toggle');
            var icon = wrap.querySelector('.lp-fas-toggle-icon i');
            var text = wrap.querySelector('.lp-fas-toggle-text');
            var update = function () {
                if (el.checked) {
                    wrap.classList.remove('is-off');
                    wrap.classList.add('is-on');
                    if (icon) icon.className = 'bi bi-check-lg';
                    if (text) text.textContent = 'Tampil di publik';
                } else {
                    wrap.classList.remove('is-on');
                    wrap.classList.add('is-off');
                    if (icon) icon.className = 'bi bi-x-lg';
                    if (text) text.textContent = 'Disimpan draft';
                }
            };
            el.addEventListener('change', update);
            update();
        });
    }
    bindFasPublishToggle(document);

    // ============ FASILITAS — Repeater (Tambah Baris + Simpan Semua) ============
    if (typeof lpRep !== 'undefined' && document.getElementById('lpFasList')) {
        lpRep.init({
            listId: 'lpFasList',
            addBtnId: 'lpFasAddBtn',
            saveBtnId: 'lpFasSaveAll',
            emptyId: 'lpFasEmpty',
            templateId: 'lpFasRowTemplate',
            storeUrl: @json(route('app.admin-landing.fasilitas.store')),
            updateUrlTpl: @json(route('app.admin-landing.fasilitas.update', ['item' => '__ID__'])),
            cardClass: 'lp-fas-card',
            removeBtnSelector: '[data-role="remove"], .btn-remove-row',
            wysiwyg: false,
            afterAppend: function (row) {
                row.classList.remove('is-open');
                row.classList.add('is-collapsed');
                bindFasToggle(row);
                bindFasPublishToggle(row);
                // Auto-fill is-filled class untuk Material Kit floating label
                row.querySelectorAll('.input-group-outline input, .input-group-outline textarea').forEach(function (el) {
                    if (el.type === 'checkbox' || el.type === 'radio' || el.type === 'file') return;
                    var wrap = el.closest('.input-group-outline');
                    if (!wrap) return;
                    var hasValue = el.value !== null && el.value !== '';
                    wrap.classList.toggle('is-filled', hasValue);
                });
                // Init Select2 untuk icon-select di baris baru
                if (window.jQuery && jQuery.fn.select2) {
                    row.querySelectorAll('.lp-icon-select').forEach(function (el) {
                        var $sel = jQuery(el);
                        var wrap = $sel.closest('.lp-icon-select-wrap');
                        var hidden = wrap.find('input[data-role="icon-value"]');
                        var initVal = hidden.length ? hidden.val() : 'bi-building';
                        initIconSelect2($sel, initVal);
                        $sel.on('change', function () {
                            if (hidden.length) hidden.val($sel.val() || '');
                        });
                    });
                }
            },
            gatherPayload: function (row) {
                var title = row.querySelector('input[name*="[title]"]');
                if (!title || !title.value.trim()) return null;
                return {
                    fd: lpRep.buildFormData(row, [
                        { name: 'title' },
                        { name: 'icon' },
                        { name: 'description' },
                        { name: 'sort_order' },
                        { name: 'is_published', type: 'checkbox' },
                    ])
                };
            },
        });
    }

    // ============ FASILITAS — Delete per-baris (existing item) ============
    document.querySelectorAll('[data-lp-fas-delete]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var url = btn.getAttribute('data-url');
            var name = btn.getAttribute('data-name') || 'ini';
            if (typeof Swal === 'undefined' || !Swal.fire) {
                if (confirm('Hapus fasilitas "' + name + '"?')) {
                    fetch(url, { method: 'POST', body: (function(){var fd=new FormData();fd.append('_method','DELETE');return fd;})(), headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json'} })
                        .then(function(){ window.location.reload(); });
                }
                return;
            }
            Swal.fire({
                title: 'Hapus fasilitas?',
                text: 'Fasilitas "' + name + '" akan dihapus permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then(function (r) {
                if (!r.isConfirmed) return;
                var meta = document.querySelector('meta[name="csrf-token"]');
                var token = meta ? meta.getAttribute('content') : '';
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                    },
                    body: (function () {
                        var fd = new FormData();
                        fd.append('_method', 'DELETE');
                        return fd;
                    })(),
                }).then(function (resp) {
                    if (resp.ok || resp.status === 200 || resp.status === 204) {
                        var card = btn.closest('.lp-fas-card');
                        if (card) {
                            card.style.transition = 'opacity .2s ease, transform .2s ease';
                            card.style.opacity = '0';
                            card.style.transform = 'translateX(-20px)';
                            setTimeout(function () {
                                card.remove();
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({ icon: 'success', title: 'Berhasil dihapus', timer: 1500, showConfirmButton: false });
                                }
                            }, 200);
                        } else {
                            window.location.reload();
                        }
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan.' });
                    }
                }).catch(function () {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Cek koneksi Anda.' });
                });
            });
        });
    });

    // ============ Override Swal.fire global → toast pojok kanan atas ============
    if (typeof Swal !== 'undefined') {
        var Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2200,
            timerProgressBar: true,
            showCloseButton: true,
            customClass: {
                popup: 'lp-ps-toast',
                title: 'lp-ps-toast-title',
            },
            didOpen: function (toast) {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        var originalFire = Swal.fire.bind(Swal);
        Swal.fire = function (opts) {
            // Hanya override notifikasi biasa (bukan konfirmasi/modal)
            if (opts && (opts.icon === 'success' || opts.icon === 'error' || opts.icon === 'info' || opts.icon === 'warning') && !opts.toast && !opts.showCancelButton) {
                Toast.fire({
                    icon: opts.icon,
                    title: opts.title || opts.text || 'Notifikasi',
                    html: opts.html || undefined,
                });
                return Promise.resolve({ isConfirmed: true });
            }
            // Biarkan confirmation dialog (delete dll) tetap modal
            return originalFire(opts);
        };
    }
});

    // ============ Section anchor: hash navigation (SOP pattern) ============
    (function () {
        var shell = document.querySelector('.bps-setting');
        if (!shell) return;

        var validIds = ['bps-overview', 'bps-sejarah', 'bps-visi_misi', 'bps-akreditasi', 'bps-fasilitas'];
        var defaultHash = '#bps-overview';

        // Auto-correct hash jika tidak valid
        var h = window.location.hash;
        if (!h || validIds.indexOf(h.substring(1)) === -1) {
            history.replaceState(null, '', defaultHash);
        }
    })();
</script>
<style>
.lp-ps-toast {
    border-radius: .65rem !important;
    box-shadow: 0 12px 28px -10px rgba(15, 23, 42, .25) !important;
    border: 1px solid #e2e8f0 !important;
    padding: .65rem .85rem !important;
    background: #fff !important;
}
.lp-ps-toast-title {
    font-size: .88rem !important;
    font-weight: 600 !important;
    color: #1f2937 !important;
}
.swal2-top-end { margin-top: 78px !important; }
</style>
@include('admin-landing._komponen._repeater-skrip')
@include('admin-landing._skrip')
@endsection