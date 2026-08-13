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

        /* ============ Sub-section divider (Fasilitas & Struktur) ============ */
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
        .lp-ps-divider-icon.is-amber { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #b45309; }
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

        /* ============ Struktur Diagram Preview ============ */
        .lp-org-preview {
            background: linear-gradient(180deg, #f6f8fb 0%, #ffffff 100%);
            border: 1px solid #e2e8f0;
            border-radius: .85rem;
            padding: 1.25rem 1rem 1.5rem;
            margin-bottom: .85rem;
        }
        .lp-org-row {
            display: flex;
            justify-content: center;
            gap: .65rem;
            flex-wrap: wrap;
            position: relative;
        }
        .lp-org-row + .lp-org-row { margin-top: 1.5rem; }
        .lp-org-row.is-top::before { display: none; }
        .lp-org-card-mini {
            background: #fff;
            border: 1px solid rgba(15, 23, 42, .08);
            border-radius: .55rem;
            padding: .55rem .65rem;
            text-align: center;
            min-width: 130px;
            max-width: 160px;
            flex: 1;
            box-shadow: 0 4px 10px -8px rgba(15, 23, 42, .12);
        }
        .lp-org-card-mini.is-lead {
            border-color: rgba(217, 119, 6, .25);
            background: linear-gradient(180deg, #fffbeb 0%, #ffffff 100%);
        }
        .lp-org-card-mini .avatar {
            width: 36px; height: 36px;
            margin: 0 auto .35rem;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(var(--bs-primary-rgb, 25, 135, 84), .15), rgba(var(--bs-primary-rgb, 25, 135, 84), .3));
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            font-size: .82rem;
            color: var(--bs-primary, #198754);
            font-weight: 700;
        }
        .lp-org-card-mini.is-lead .avatar {
            background: linear-gradient(135deg, #fde68a, #fcd34d);
            color: #b45309;
        }
        .lp-org-card-mini .avatar img { width: 100%; height: 100%; object-fit: cover; }
        .lp-org-card-mini .name { font-weight: 700; font-size: .78rem; color: #1f2937; }
        .lp-org-card-mini .role {
            font-size: .68rem;
            color: #475569;
            margin-top: 2px;
            display: block;
        }
        .lp-org-row:not(.is-top) .lp-org-card-mini::before {
            content: "";
            position: absolute;
            top: -.75rem;
            left: 50%;
            transform: translateX(-50%);
            width: 2px;
            height: .75rem;
            background: #cbd5e1;
        }
        .lp-org-row.multi::after {
            content: "";
            position: absolute;
            top: -.75rem;
            left: 16.66%;
            right: 16.66%;
            height: 2px;
            background: #cbd5e1;
        }

        /* ============ List items (Fasilitas & Struktur) ============ */
        .lp-ps-list-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: .85rem;
            padding: .85rem 1rem;
            transition: border-color .15s ease, box-shadow .15s ease;
            margin-bottom: .65rem;
        }
        .lp-ps-list-card.is-inactive { background: #f8fafc; opacity: .92; }
        .lp-ps-list-card:hover {
            border-color: #cbd5e1;
        }
        .lp-ps-list-head {
            display: flex;
            align-items: center;
            gap: .65rem;
            padding-bottom: .55rem;
            margin-bottom: .55rem;
            border-bottom: 1px dashed #e2e8f0;
        }
        .lp-ps-list-avatar {
            width: 38px; height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
            color: #475569;
            display: inline-flex; align-items: center; justify-content: center;
            font-weight: 700;
            font-size: .85rem;
            flex-shrink: 0;
            overflow: hidden;
        }
        .lp-ps-list-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .lp-ps-list-avatar.is-lead { background: linear-gradient(135deg, #fde68a, #fcd34d); color: #b45309; }
        .lp-ps-list-name {
            font-weight: 700;
            font-size: .92rem;
            color: #1f2937;
            margin: 0;
            line-height: 1.2;
        }
        .lp-ps-list-role {
            font-size: .72rem;
            color: #64748b;
            margin-top: 1px;
        }
        .lp-ps-list-status {
            margin-left: auto;
            flex-shrink: 0;
        }
        .lp-ps-list-fields {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: .6rem .75rem;
        }
        .lp-ps-list-fields .input-group { margin-bottom: 0; }
        .lp-ps-list-fields .input-group .form-control { padding-top: .75rem; padding-bottom: .3rem; font-size: .85rem; }
        .lp-ps-list-fields textarea.form-control { min-height: 38px; }
        .lp-ps-list-fields .lp-ps-full { grid-column: 1 / -1; }
        .lp-ps-list-foot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .55rem;
            margin-top: .7rem;
            padding-top: .55rem;
            border-top: 1px dashed #e2e8f0;
            flex-wrap: wrap;
        }
        .lp-ps-list-foot .btn {
            min-width: 95px;
            font-weight: 600;
            padding: .38rem .85rem;
            font-size: .82rem;
            border-radius: .5rem;
            display: inline-flex;
            align-items: center;
            gap: .3rem;
        }
        .lp-ps-list-foot .btn .material-symbols-rounded { font-size: 15px; line-height: 1; }
        .lp-ps-list-foot-info {
            font-size: .72rem;
            color: #94a3b8;
            display: inline-flex;
            align-items: center;
            gap: .25rem;
        }

        /* ============ Add new row inline form ============ */
        .lp-ps-add {
            background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
            border: 1px dashed #94a3b8;
            border-radius: .85rem;
            padding: .85rem 1rem;
            margin-bottom: .85rem;
            display: none;
        }
        .lp-ps-add.is-open { display: block; }
        .lp-ps-add-head {
            display: flex;
            align-items: center;
            gap: .5rem;
            font-weight: 700;
            font-size: .85rem;
            color: #334155;
            margin-bottom: .65rem;
        }
        .lp-ps-add-head .material-symbols-rounded { font-size: 18px; color: #198754; }
        .lp-ps-add-fields {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: .6rem .75rem;
        }
        .lp-ps-add-fields .input-group { margin-bottom: 0; }
        .lp-ps-add-fields .input-group .form-control { padding-top: .75rem; padding-bottom: .3rem; font-size: .85rem; }
        .lp-ps-add-fields textarea.form-control { min-height: 38px; }
        .lp-ps-add-fields .lp-ps-full { grid-column: 1 / -1; }
        .lp-ps-add-foot {
            display: flex;
            gap: .55rem;
            justify-content: flex-end;
            margin-top: .65rem;
        }
        .lp-ps-add-foot .btn {
            font-size: .82rem;
            font-weight: 600;
            padding: .38rem .85rem;
            border-radius: .5rem;
            display: inline-flex;
            align-items: center;
            gap: .3rem;
        }
        .lp-ps-add-foot .btn .material-symbols-rounded { font-size: 15px; }

        /* Toggle "is_lead" / "is_published" — pill style */
        .lp-ps-mini-toggle {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .25rem .5rem;
            border-radius: 999px;
            cursor: pointer;
            user-select: none;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .02em;
            transition: background .2s ease;
            border: 1px solid transparent;
        }
        .lp-ps-mini-toggle input { position: absolute; opacity: 0; pointer-events: none; }
        .lp-ps-mini-toggle .lp-ps-toggle-track {
            position: relative;
            width: 26px; height: 14px;
            background: #94a3b8;
            border-radius: 999px;
            flex-shrink: 0;
        }
        .lp-ps-mini-toggle .lp-ps-toggle-track::after {
            content: "";
            position: absolute;
            top: 2px; left: 2px;
            width: 10px; height: 10px;
            border-radius: 50%;
            background: #fff;
            transition: transform .2s ease;
            box-shadow: 0 1px 2px rgba(0,0,0,.2);
        }
        .lp-ps-mini-toggle.is-on .lp-ps-toggle-track { background: #198754; }
        .lp-ps-mini-toggle.is-on .lp-ps-toggle-track::after { transform: translateX(12px); }
        .lp-ps-mini-toggle.is-on { background: rgba(25, 135, 84, .12); color: #15803d; }
        .lp-ps-mini-toggle.is-off { background: rgba(100, 116, 139, .12); color: #475569; }
    </style>
@endsection

@section('content')
<div class="px-2 py-2">
    @if (session('success'))
        <div class="alert alert-success py-2 small mb-3">{{ session('success') }}</div>
    @endif

    @php
        $iconMap = [
            'overview'     => ['bi' => 'bi-eye-fill',           'css' => 'is-overview'],
            'sejarah'      => ['bi' => 'bi-clock-history',      'css' => 'is-sejarah'],
            'visi_misi'    => ['bi' => 'bi-bullseye',           'css' => 'is-visi_misi'],
            'akreditasi'   => ['bi' => 'bi-patch-check-fill',   'css' => 'is-akreditasi'],
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
    @endphp

    @if ($items->isEmpty())
        <div class="lp-ps-empty">
            <span class="material-symbols-rounded">article</span>
            <div class="mt-2">Belum ada section.</div>
        </div>
    @else
        <div class="lp-ps-stack">
            @foreach ($items as $row)
                @php
                    $ic = $iconMap[$row->section_key] ?? ['bi' => 'bi-file-text-fill', 'css' => ''];
                    $label = $labelMap[$row->section_key] ?? $row->section_key;
                    $help = $helpMap[$row->section_key] ?? null;
                    $k = $row->section_key;
                    $showBadge = in_array($k, ['overview', 'akreditasi']);
                @endphp

                <form action="{{ route('app.admin-landing.profile-sections.update', $row->id) }}"
                      method="POST" class="lp-ajax lp-ps-card {{ $ic['css'] }} {{ $row->is_active ? '' : 'is-inactive' }}"
                      data-section-key="{{ $k }}">
                    @csrf
                    @method('PUT')

                    {{-- ====== HEADER ====== --}}
                    <div class="lp-ps-head">
                        <div class="lp-ps-icon {{ $ic['css'] }}"><i class="bi {{ $ic['bi'] }}"></i></div>
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
            @endforeach
        </div>
    @endif

    <div class="lp-ps-divider" id="struktur-section">
        <div class="lp-ps-divider-icon is-amber">
            <i class="bi bi-diagram-3-fill"></i>
        </div>
        <div>
            <h6 class="lp-ps-divider-title">Struktur Organisasi</h6>
            <div class="lp-ps-divider-sub">Kotak & garis, urut sesuai sort_order. Toggle "Pimpinan" untuk baris atas.</div>
        </div>
        <div class="lp-ps-divider-spacer"></div>
        <button type="button" class="btn btn-sm btn-primary" data-lp-add-toggle="struktur">
            <span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle;">add</span>
            Tambah
        </button>
    </div>

    @php
        $strukturLeads = $strukturItems->where('is_lead', true)->values();
        $strukturMembers = $strukturItems->where('is_lead', false)->values();
    @endphp

    {{-- Preview diagram (read-only, selalu tampil) --}}
    <div class="lp-org-preview" id="lp-org-preview" data-empty="{{ $strukturItems->isEmpty() ? '1' : '0' }}">
        @if ($strukturItems->isEmpty())
            <div class="text-center text-muted py-3" style="font-size:.82rem;">
                <span class="material-symbols-rounded" style="font-size:32px;opacity:.4;display:block;">groups</span>
                Diagram akan muncul setelah Anda menambahkan anggota struktur.
            </div>
        @else
            @if ($strukturLeads->isNotEmpty())
                <div class="lp-org-row is-top" id="lp-org-row-leads">
                    @foreach ($strukturLeads as $p)
                        <div class="lp-org-card-mini is-lead" data-struktur-id="{{ $p->id }}">
                            <div class="avatar">
                                @if ($p->photoUrl())
                                    <img src="{{ $p->photoUrl() }}" alt="{{ $p->name }}">
                                @else
                                    {{ mb_substr($p->name, 0, 1) }}
                                @endif
                            </div>
                            <div class="name">{{ $p->name }}</div>
                            <span class="role">{{ $p->role }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="lp-org-row is-top" id="lp-org-row-leads" style="display:none;"></div>
            @endif
            @if ($strukturMembers->isNotEmpty())
                <div class="lp-org-row multi" id="lp-org-row-members">
                    @foreach ($strukturMembers as $p)
                        <div class="lp-org-card-mini" data-struktur-id="{{ $p->id }}">
                            <div class="avatar">
                                @if ($p->photoUrl())
                                    <img src="{{ $p->photoUrl() }}" alt="{{ $p->name }}">
                                @else
                                    {{ mb_substr($p->name, 0, 1) }}
                                @endif
                            </div>
                            <div class="name">{{ $p->name }}</div>
                            <span class="role">{{ $p->role }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="lp-org-row multi" id="lp-org-row-members"></div>
            @endif
        @endif
    </div>

    {{-- Form tambah baru (inline) --}}
    <form action="{{ route('app.admin-landing.struktur.store') }}" method="POST"
          class="lp-ajax lp-ps-add" enctype="multipart/form-data" data-lp-add-form="struktur">
        @csrf
        <div class="lp-ps-add-head">
            <span class="material-symbols-rounded">person_add</span>
            Tambah Anggota Struktur
        </div>
        <div class="lp-ps-add-fields">
            <div class="input-group input-group-outline mb-0">
                <label class="form-label">Nama <span class="text-danger">*</span></label>
                <input type="text" name="name" required maxlength="150" class="form-control" placeholder="Cth: Dr. Sarah Wijaya">
            </div>
            <div class="input-group input-group-outline mb-0">
                <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                <input type="text" name="role" required maxlength="150" class="form-control" placeholder="Cth: Kepala Sekolah">
            </div>
            <div class="input-group input-group-outline mb-0">
                <label class="form-label">Urutan</label>
                <input type="number" name="sort_order" min="0" class="form-control" placeholder="0">
            </div>
            <div class="input-group input-group-outline mb-0">
                <label class="form-label">Foto (opsional)</label>
                <input type="file" name="photo" accept="image/*" class="form-control">
            </div>
            <div class="lp-ps-full" style="display:flex;gap:.65rem;align-items:center;flex-wrap:wrap;">
                <label class="lp-ps-mini-toggle is-off" for="new_struktur_lead" style="position:relative;">
                    <input type="checkbox" name="is_lead" id="new_struktur_lead" value="1">
                    <span class="lp-ps-toggle-track"></span>
                    <span>Pimpinan (baris atas)</span>
                </label>
                <label class="lp-ps-mini-toggle is-on" for="new_struktur_published" style="position:relative;">
                    <input type="checkbox" name="is_published" id="new_struktur_published" value="1" checked>
                    <span class="lp-ps-toggle-track"></span>
                    <span>Tampilkan</span>
                </label>
            </div>
        </div>
        <div class="lp-ps-add-foot">
            <button type="button" class="btn btn-light" data-lp-add-cancel="struktur">Batal</button>
            <button type="submit" class="btn btn-info">
                <span class="material-symbols-rounded">save</span>
                Simpan
            </button>
        </div>
    </form>

    {{-- List struktur existing --}}
    @if ($strukturItems->isEmpty())
        <div class="lp-ps-empty">
            <span class="material-symbols-rounded">groups</span>
            <div class="mt-2">Belum ada anggota struktur.</div>
        </div>
    @else
        @foreach ($strukturItems as $row)
            @php
                $initials = mb_substr($row->name, 0, 1);
                $hasPhoto = (bool) $row->photo;
            @endphp
            <form action="{{ route('app.admin-landing.struktur.update', $row->id) }}" method="POST"
                  class="lp-ajax lp-ps-list-card {{ $row->is_published ? '' : 'is-inactive' }}"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="lp-ps-list-head">
                    <div class="lp-ps-list-avatar {{ $row->is_lead ? 'is-lead' : '' }}">
                        @if ($hasPhoto)
                            <img src="{{ Storage::disk('public')->url('landing/' . $row->photo) }}" alt="{{ $row->name }}">
                        @else
                            {{ $initials }}
                        @endif
                    </div>
                    <div class="min-w-0">
                        <div class="lp-ps-list-name">{{ $row->name }}</div>
                        <div class="lp-ps-list-role">{{ $row->role }}</div>
                    </div>
                    <div class="lp-ps-list-status" style="display:flex;gap:.35rem;flex-wrap:wrap;">
                        <label class="lp-ps-mini-toggle {{ $row->is_published ? 'is-on' : 'is-off' }}"
                               for="struktur_published_{{ $row->id }}" style="position:relative;">
                            <input type="checkbox" name="is_published" id="struktur_published_{{ $row->id }}"
                                   value="1" {{ $row->is_published ? 'checked' : '' }}>
                            <span class="lp-ps-toggle-track"></span>
                            <span>{{ $row->is_published ? 'Aktif' : 'Non-aktif' }}</span>
                        </label>
                    </div>
                </div>

                <div class="lp-ps-list-fields">
                    <div class="input-group input-group-outline mb-0">
                        <label class="form-label">Nama <span class="text-danger">*</span></label>
                        <input type="text" name="name" required maxlength="150"
                               value="{{ $row->name }}" class="form-control">
                    </div>
                    <div class="input-group input-group-outline mb-0">
                        <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                        <input type="text" name="role" required maxlength="150"
                               value="{{ $row->role }}" class="form-control">
                    </div>
                    <div class="input-group input-group-outline mb-0">
                        <label class="form-label">Urutan</label>
                        <input type="number" name="sort_order" min="0"
                               value="{{ $row->sort_order }}" class="form-control">
                    </div>
                    <div class="input-group input-group-outline mb-0">
                        <label class="form-label">Foto</label>
                        <input type="file" name="photo" accept="image/*" class="form-control">
                    </div>
                    <div class="lp-ps-full" style="display:flex;gap:.65rem;align-items:center;flex-wrap:wrap;">
                        <label class="lp-ps-mini-toggle {{ $row->is_lead ? 'is-on' : 'is-off' }}"
                               for="struktur_lead_{{ $row->id }}" style="position:relative;">
                            <input type="checkbox" name="is_lead" id="struktur_lead_{{ $row->id }}"
                                   value="1" {{ $row->is_lead ? 'checked' : '' }}>
                            <span class="lp-ps-toggle-track"></span>
                            <span>Pimpinan</span>
                        </label>
                    </div>
                </div>

                <div class="lp-ps-list-foot">
                    <div class="lp-ps-list-foot-info">
                        <span class="material-symbols-rounded" style="font-size:13px;">cloud_done</span>
                        Perubahan langsung tersinkron.
                    </div>
                    <div style="display:flex;gap:.35rem;">
                        <button type="submit" class="btn btn-info">
                            <span class="material-symbols-rounded">save</span>
                            Simpan
                        </button>
                        <button type="button" class="btn btn-outline-danger" data-lp-delete
                                data-url="{{ route('app.admin-landing.struktur.destroy', $row->id) }}"
                                data-msg="Hapus anggota struktur '{{ $row->name }}'?">
                            <span class="material-symbols-rounded">delete</span>
                        </button>
                    </div>
                </div>
            </form>
        @endforeach
    @endif

    {{-- ============ FASILITAS ============ --}}
    <div class="lp-ps-divider" id="fasilitas-section">
        <div class="lp-ps-divider-icon">
            <i class="bi bi-building"></i>
        </div>
        <div>
            <h6 class="lp-ps-divider-title">Fasilitas Sekolah</h6>
            <div class="lp-ps-divider-sub">Daftar fasilitas tampil di section Fasilitas halaman profil.</div>
        </div>
        <div class="lp-ps-divider-spacer"></div>
        <button type="button" class="btn btn-sm btn-primary" data-lp-add-toggle="fasilitas">
            <span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle;">add</span>
            Tambah
        </button>
    </div>

    {{-- Form tambah baru (inline) --}}
    <form action="{{ route('app.admin-landing.fasilitas.store') }}" method="POST"
          class="lp-ajax lp-ps-add" data-lp-add-form="fasilitas">
        @csrf
        <div class="lp-ps-add-head">
            <span class="material-symbols-rounded">add_business</span>
            Tambah Fasilitas
        </div>
        <div class="lp-ps-add-fields">
            <div class="input-group input-group-outline mb-0">
                <label class="form-label">Judul <span class="text-danger">*</span></label>
                <input type="text" name="title" required maxlength="150" class="form-control" placeholder="Cth: Ruang Kelas Modern">
            </div>
            <div class="input-group input-group-outline mb-0">
                <label class="form-label">Icon</label>
                <input type="text" name="icon" maxlength="80" class="form-control" placeholder="bi-easel">
            </div>
            <div class="input-group input-group-outline mb-0">
                <label class="form-label">Urutan</label>
                <input type="number" name="sort_order" min="0" class="form-control" placeholder="0">
            </div>
            <div class="lp-ps-full">
                <div class="input-group input-group-outline mb-0">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" rows="2" class="form-control" placeholder="Cth: Ruang belajar nyaman dengan AC, proyektor..."></textarea>
                </div>
            </div>
            <div class="lp-ps-full" style="display:flex;gap:.65rem;align-items:center;flex-wrap:wrap;">
                <label class="lp-ps-mini-toggle is-on" for="new_fas_published" style="position:relative;">
                    <input type="checkbox" name="is_published" id="new_fas_published" value="1" checked>
                    <span class="lp-ps-toggle-track"></span>
                    <span>Tampilkan</span>
                </label>
            </div>
        </div>
        <div class="lp-ps-add-foot">
            <button type="button" class="btn btn-light" data-lp-add-cancel="fasilitas">Batal</button>
            <button type="submit" class="btn btn-info">
                <span class="material-symbols-rounded">save</span>
                Simpan
            </button>
        </div>
    </form>

    {{-- List fasilitas existing --}}
    @if ($fasilitasItems->isEmpty())
        <div class="lp-ps-empty">
            <span class="material-symbols-rounded">apartment</span>
            <div class="mt-2">Belum ada fasilitas.</div>
        </div>
    @else
        @foreach ($fasilitasItems as $row)
            <form action="{{ route('app.admin-landing.fasilitas.update', $row->id) }}" method="POST"
                  class="lp-ajax lp-ps-list-card {{ $row->is_published ? '' : 'is-inactive' }}">
                @csrf
                @method('PUT')
                <div class="lp-ps-list-head">
                    <div class="lp-ps-list-avatar">
                        <i class="bi {{ $row->icon ?: 'bi-building' }}" style="font-size:1.1rem;"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="lp-ps-list-name">{{ $row->title }}</div>
                        <div class="lp-ps-list-role">#{{ $row->sort_order }} · {{ $row->icon ?: 'bi-building' }}</div>
                    </div>
                    <div class="lp-ps-list-status">
                        <label class="lp-ps-mini-toggle {{ $row->is_published ? 'is-on' : 'is-off' }}"
                               for="fas_published_{{ $row->id }}" style="position:relative;">
                            <input type="checkbox" name="is_published" id="fas_published_{{ $row->id }}"
                                   value="1" {{ $row->is_published ? 'checked' : '' }}>
                            <span class="lp-ps-toggle-track"></span>
                            <span>{{ $row->is_published ? 'Aktif' : 'Non-aktif' }}</span>
                        </label>
                    </div>
                </div>

                <div class="lp-ps-list-fields">
                    <div class="input-group input-group-outline mb-0">
                        <label class="form-label">Judul <span class="text-danger">*</span></label>
                        <input type="text" name="title" required maxlength="150"
                               value="{{ $row->title }}" class="form-control">
                    </div>
                    <div class="input-group input-group-outline mb-0">
                        <label class="form-label">Icon</label>
                        <input type="text" name="icon" maxlength="80"
                               value="{{ $row->icon }}" class="form-control" placeholder="bi-easel">
                    </div>
                    <div class="input-group input-group-outline mb-0">
                        <label class="form-label">Urutan</label>
                        <input type="number" name="sort_order" min="0"
                               value="{{ $row->sort_order }}" class="form-control">
                    </div>
                    <div class="lp-ps-full">
                        <div class="input-group input-group-outline mb-0">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" rows="2" class="form-control">{{ $row->description }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="lp-ps-list-foot">
                    <div class="lp-ps-list-foot-info">
                        <span class="material-symbols-rounded" style="font-size:13px;">cloud_done</span>
                        Perubahan langsung tersinkron.
                    </div>
                    <div style="display:flex;gap:.35rem;">
                        <button type="submit" class="btn btn-info">
                            <span class="material-symbols-rounded">save</span>
                            Simpan
                        </button>
                        <button type="button" class="btn btn-outline-danger" data-lp-delete
                                data-url="{{ route('app.admin-landing.fasilitas.destroy', $row->id) }}"
                                data-msg="Hapus fasilitas '{{ $row->title }}'?">
                            <span class="material-symbols-rounded">delete</span>
                        </button>
                    </div>
                </div>
            </form>
        @endforeach
    @endif
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

    // Mini toggle untuk is_published / is_lead
    document.querySelectorAll('.lp-ps-mini-toggle input[type="checkbox"]').forEach(function (el) {
        var wrap = el.closest('.lp-ps-mini-toggle');
        var card = el.closest('.lp-ps-list-card');
        var text = wrap.querySelector('span:last-child');
        var update = function () {
            if (el.checked) {
                wrap.classList.remove('is-off');
                wrap.classList.add('is-on');
                // Update label text kalau parent tidak punya label khusus
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

            // Live update preview struktur saat is_lead berubah
            if (el.name === 'is_lead' && card) {
                var id = card.querySelector('[data-lp-delete]')?.getAttribute('data-url')?.match(/\/(\d+)$/)?.[1];
                if (!id) {
                    var inp = card.querySelector('input[name="name"]');
                    id = inp ? 'new_' + Math.random().toString(36).slice(2, 8) : null;
                }
                var preview = document.getElementById('lp-org-preview');
                if (!preview || preview.dataset.empty === '1') {
                    // Preview masih kosong (placeholder) → reload halaman setelah save
                    return;
                }
                var rowLeads = document.getElementById('lp-org-row-leads');
                var rowMembers = document.getElementById('lp-org-row-members');
                if (!rowLeads || !rowMembers) return;

                var avatar = card.querySelector('.lp-ps-list-avatar');
                var name = card.querySelector('input[name="name"]')?.value || '?';
                var role = card.querySelector('input[name="role"]')?.value || '';
                var isLead = el.checked;

                // Cari card mini yang terkait (kalau ada)
                var mini = rowLeads.querySelector('[data-struktur-id="' + id + '"]') ||
                           rowMembers.querySelector('[data-struktur-id="' + id + '"]');
                var newMini = document.createElement('div');
                newMini.className = 'lp-org-card-mini' + (isLead ? ' is-lead' : '');
                newMini.setAttribute('data-struktur-id', id);
                var avatarHtml = avatar && avatar.querySelector('img')
                    ? '<img src="' + avatar.querySelector('img').getAttribute('src') + '" alt="' + name + '">'
                    : name.charAt(0).toUpperCase();
                newMini.innerHTML = '<div class="avatar">' + avatarHtml + '</div><div class="name">' +
                    name.replace(/</g, '&lt;') + '</div><span class="role">' +
                    role.replace(/</g, '&lt;') + '</span>';

                if (mini) {
                    mini.replaceWith(newMini);
                } else {
                    // Card baru (just added, belum ke-save)
                    (isLead ? rowLeads : rowMembers).appendChild(newMini);
                }

                // Show/hide rows
                rowLeads.style.display = rowLeads.children.length ? '' : 'none';
                if (!document.getElementById('lp-org-row-members').classList.contains('multi') &&
                    document.getElementById('lp-org-row-members').children.length > 0) {
                    document.getElementById('lp-org-row-members').classList.add('multi');
                }
            }
        };
        el.addEventListener('change', update);
        update();
    });

    // Toggle form tambah (inline)
    document.querySelectorAll('[data-lp-add-toggle]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var key = btn.getAttribute('data-lp-add-toggle');
            var form = document.querySelector('[data-lp-add-form="' + key + '"]');
            if (!form) return;
            var isOpen = form.classList.toggle('is-open');
            if (isOpen) {
                var first = form.querySelector('input, textarea');
                if (first) first.focus();
            }
        });
    });
    document.querySelectorAll('[data-lp-add-cancel]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var key = btn.getAttribute('data-lp-add-cancel');
            var form = document.querySelector('[data-lp-add-form="' + key + '"]');
            if (form) {
                form.classList.remove('is-open');
                form.reset();
                // Reset mini toggle defaults
                form.querySelectorAll('.lp-ps-mini-toggle input[type="checkbox"]').forEach(function (cb) {
                    cb.checked = cb.defaultChecked;
                    cb.dispatchEvent(new Event('change'));
                });
            }
        });
    });

    // Delete handler (custom — pakai Swal modal, AJAX DELETE)
    document.querySelectorAll('[data-lp-delete]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var url = btn.getAttribute('data-url');
            var msg = btn.getAttribute('data-msg') || 'Yakin ingin menghapus data ini?';
            if (typeof Swal === 'undefined') return;
            Swal.fire({
                title: 'Hapus data?',
                text: msg,
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
                        // Hapus card dari DOM
                        var card = btn.closest('.lp-ps-list-card');
                        if (card) {
                            card.style.transition = 'opacity .2s ease, transform .2s ease';
                            card.style.opacity = '0';
                            card.style.transform = 'translateX(-20px)';
                            setTimeout(function () { card.remove(); }, 200);
                        }
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil dihapus',
                            timer: 1500,
                            showConfirmButton: false,
                        });
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
@include('admin-landing._skrip')
@endsection