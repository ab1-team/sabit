@extends('layouts.tenant.base')

@section('style')
    @include('admin-landing._gaya')
    <style>
        .lp-ps-stack { display: flex; flex-direction: column; gap: .75rem; }

        .lp-ps-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: .85rem;
            padding: .9rem 1.1rem;
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        .lp-ps-card:hover { border-color: #cbd5e1; }

        .lp-ps-head {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding-bottom: .7rem;
            margin-bottom: .75rem;
            border-bottom: 1px dashed #e2e8f0;
        }
        .lp-ps-icon {
            width: 40px; height: 40px;
            border-radius: 10px;
            display: inline-flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 3px 8px -4px rgba(15, 23, 42, .12);
        }
        .lp-ps-icon.is-hero     { background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #1d4ed8; }
        .lp-ps-icon.is-button   { background: linear-gradient(135deg, #dcfce7, #bbf7d0); color: #15803d; }
        .lp-ps-icon.is-bottom   { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #b45309; }

        .lp-ps-title { font-weight: 700; font-size: .98rem; color: #1f2937; margin: 0; line-height: 1.2; }
        .lp-ps-key   { font-size: .66rem; font-weight: 600; letter-spacing: .06em; color: #94a3b8; text-transform: uppercase; margin-top: 2px; }
        .lp-ps-status { margin-left: auto; flex-shrink: 0; }

        /* Toggle pill */
        .lp-ps-toggle {
            display: inline-flex; align-items: center; gap: .55rem;
            padding: .35rem .55rem .35rem .45rem;
            background: #f1f5f9; border-radius: 999px;
            cursor: pointer; user-select: none;
            transition: background .2s ease;
            border: 1px solid transparent;
        }
        .lp-ps-toggle:hover { background: #e2e8f0; }
        .lp-ps-toggle.is-on  { background: rgba(25, 135, 84, .12); border-color: rgba(25, 135, 84, .25); }
        .lp-ps-toggle.is-off { background: rgba(100, 116, 139, .12); border-color: rgba(100, 116, 139, .2); }
        .lp-ps-toggle input[type="checkbox"] { position: absolute; opacity: 0; pointer-events: none; }
        .lp-ps-toggle-track {
            position: relative; width: 34px; height: 18px;
            background: #94a3b8; border-radius: 999px;
            transition: background .2s ease; flex-shrink: 0;
        }
        .lp-ps-toggle-track::after {
            content: ""; position: absolute; top: 2px; left: 2px;
            width: 14px; height: 14px; border-radius: 50%;
            background: #fff; box-shadow: 0 1px 2px rgba(0,0,0,.2);
            transition: transform .2s ease;
        }
        .lp-ps-toggle.is-on .lp-ps-toggle-track { background: #198754; }
        .lp-ps-toggle.is-on .lp-ps-toggle-track::after { transform: translateX(16px); }
        .lp-ps-toggle-icon {
            display: inline-flex; align-items: center; justify-content: center;
            width: 18px; height: 18px; border-radius: 50%;
            font-size: 12px; flex-shrink: 0;
        }
        .lp-ps-toggle.is-on .lp-ps-toggle-icon  { background: #198754; color: #fff; }
        .lp-ps-toggle.is-off .lp-ps-toggle-icon { background: #64748b; color: #fff; }
        .lp-ps-toggle-text { font-size: .78rem; font-weight: 700; letter-spacing: .02em; }
        .lp-ps-toggle.is-on .lp-ps-toggle-text  { color: #15803d; }
        .lp-ps-toggle.is-off .lp-ps-toggle-text { color: #475569; }
        .lp-ps-toggle:focus-within { outline: 2px solid rgba(25, 135, 84, .35); outline-offset: 2px; }

        .lp-ps-form-grid {
            display: grid; grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .6rem .85rem;
        }
        @media (max-width: 575.98px) { .lp-ps-form-grid { grid-template-columns: 1fr; } }
        .lp-ps-form-grid .lp-ps-full { grid-column: 1 / -1; }
        .lp-ps-form-grid .input-group { margin-bottom: 0; }
        .lp-ps-form-grid .input-group .form-control { padding-top: .85rem; padding-bottom: .35rem; }
        .lp-ps-field-help {
            font-size: .72rem; color: #94a3b8;
            margin: 0 0 .5rem; line-height: 1.45;
        }

        .lp-ps-foot {
            display: flex; align-items: center; justify-content: space-between;
            gap: .65rem; flex-wrap: wrap;
            margin-top: .85rem; padding-top: .65rem;
            border-top: 1px dashed #e2e8f0;
        }
        .lp-ps-foot-info {
            font-size: .72rem; color: #94a3b8;
            display: inline-flex; align-items: center; gap: .3rem;
        }
        .lp-ps-foot-info .material-symbols-rounded { font-size: 13px; }
        .lp-ps-foot .btn {
            min-width: 110px; font-weight: 600;
            display: inline-flex; align-items: center; justify-content: center;
            gap: .35rem; padding: .45rem 1rem;
            border-radius: .5rem; font-size: .85rem;
        }
        .lp-ps-foot .btn .material-symbols-rounded { font-size: 16px; line-height: 1; }

        .lp-ps-card textarea.form-control {
            font-size: .88rem; line-height: 1.55;
            padding: .65rem .85rem; border-color: #e2e8f0;
            resize: vertical; min-height: 70px;
        }
        .lp-ps-card textarea.form-control:focus {
            border-color: #1f9d57;
            box-shadow: 0 0 0 2px rgba(31, 157, 87, .12);
        }
    </style>
@endsection

@section('content')
<div class="px-2 py-2">
    @php
        $titleSlot = '<p class="text-muted small mb-0">Atur judul, subjudul, dan tombol hero halaman publik PPDB (<code>/ppdb</code>). Teks di sini khusus halaman PPDB, tidak terkait dengan section CTA PPDB di beranda.</p>';
    @endphp
    @include('admin-landing._header-halaman', [
        'subtitle' => 'Landing Page',
        'titleSlot' => $titleSlot,
    ])

    <div class="lp-ps-stack">

        {{-- ============ CARD 1: HERO HALAMAN PPDB ============ --}}
        <form action="{{ $action }}" method="POST" class="lp-ajax lp-ps-card" data-section-key="hero">
            @csrf
            <div class="lp-ps-head">
                <div class="lp-ps-icon is-hero"><span class="material-symbols-rounded">campaign</span></div>
                <div class="min-w-0">
                    <h6 class="lp-ps-title">Hero Halaman PPDB</h6>
                    <div class="lp-ps-key">section: hero</div>
                </div>
                <div class="lp-ps-status">
                    <label class="lp-ps-toggle {{ old('is_active', $ppdb->is_active ?? true) ? 'is-on' : 'is-off' }}"
                           for="is_active_hero">
                        <input type="checkbox" name="is_active" id="is_active_hero" value="1"
                               {{ old('is_active', $ppdb->is_active ?? true) ? 'checked' : '' }}>
                        <span class="lp-ps-toggle-track" aria-hidden="true"></span>
                        <span class="lp-ps-toggle-icon" aria-hidden="true">
                            <i class="bi {{ old('is_active', $ppdb->is_active ?? true) ? 'bi-check-lg' : 'bi-x-lg' }}"></i>
                        </span>
                        <span class="lp-ps-toggle-text">
                            {{ old('is_active', $ppdb->is_active ?? true) ? 'Aktif' : 'Non-aktif' }}
                        </span>
                    </label>
                </div>
            </div>

            <div class="lp-ps-field-help">Teks hero yang tampil di halaman publik PPDB. Berbeda dengan <strong>CTA PPDB</strong> yang muncul di section beranda.</div>

            <div class="lp-ps-form-grid">
                <div class="input-group input-group-outline mb-0 @if(old('school_name', $ppdb->school_name)) is-filled @endif">
                    <label class="form-label">Nama Sekolah (header)</label>
                    <input type="text" name="school_name" maxlength="150"
                           value="{{ old('school_name', $ppdb->school_name) }}"
                           class="form-control" placeholder="{{ $setting->school_name ?? 'Nama Sekolah' }}">
                </div>
                <div class="input-group input-group-outline mb-0 @if(old('eyebrow', $ppdb->eyebrow)) is-filled @endif">
                    <label class="form-label">Eyebrow (kecil di atas judul)</label>
                    <input type="text" name="eyebrow" maxlength="100"
                           value="{{ old('eyebrow', $ppdb->eyebrow) }}"
                           class="form-control" placeholder="Penerimaan Peserta Didik Baru">
                </div>
                <div class="lp-ps-full input-group input-group-outline mb-0 @if(old('title', $ppdb->title)) is-filled @endif">
                    <label class="form-label">Judul Utama <span class="text-danger">*</span></label>
                    <input type="text" name="title" required maxlength="200"
                           value="{{ old('title', $ppdb->title) }}"
                           class="form-control" placeholder="PPDB 2026/2027">
                </div>
                <div class="lp-ps-full">
                    <textarea name="subtitle" rows="3" class="form-control"
                              placeholder="Mari bergabung bersama kami wujudkan pendidikan berkualitas.">{{ old('subtitle', $ppdb->subtitle) }}</textarea>
                </div>
            </div>

            <div class="lp-ps-foot">
                <div class="lp-ps-foot-info">
                    <span class="material-symbols-rounded">cloud_done</span>
                    Perubahan langsung tersinkron ke halaman publik.
                </div>
                <button type="submit" class="btn btn-info">
                    <span class="material-symbols-rounded" style="font-size:17px;">save</span>
                    Simpan
                </button>
            </div>
        </form>

        {{-- ============ CARD 2: TOMBOL HERO ============ --}}
        <form action="{{ $action }}" method="POST" class="lp-ajax lp-ps-card" data-section-key="hero-buttons">
            @csrf
            <div class="lp-ps-head">
                <div class="lp-ps-icon is-button"><span class="material-symbols-rounded">smart_button</span></div>
                <div class="min-w-0">
                    <h6 class="lp-ps-title">Tombol Hero</h6>
                    <div class="lp-ps-key">section: hero-buttons</div>
                </div>
            </div>

            <div class="lp-ps-field-help">Dua tombol di hero halaman PPDB.</div>

            <div class="lp-ps-form-grid">
                <div class="input-group input-group-outline mb-0 @if(old('cta_text', $ppdb->cta_text)) is-filled @endif">
                    <label class="form-label">Teks Tombol Utama</label>
                    <input type="text" name="cta_text" maxlength="100"
                           value="{{ old('cta_text', $ppdb->cta_text) }}"
                           class="form-control" placeholder="Formulir Pendaftaran Online">
                </div>
                <div class="input-group input-group-outline mb-0 @if(old('cta_url', $ppdb->cta_url)) is-filled @endif">
                    <label class="form-label">URL Tombol Utama</label>
                    <input type="text" name="cta_url" maxlength="255"
                           value="{{ old('cta_url', $ppdb->cta_url) }}"
                           class="form-control" placeholder="/ppdb atau https://...">
                </div>
                <div class="input-group input-group-outline mb-0 @if(old('secondary_text', $ppdb->secondary_text)) is-filled @endif">
                    <label class="form-label">Teks Tombol Sekunder</label>
                    <input type="text" name="secondary_text" maxlength="100"
                           value="{{ old('secondary_text', $ppdb->secondary_text) }}"
                           class="form-control" placeholder="Kontak Kami">
                </div>
                <div class="input-group input-group-outline mb-0 @if(old('secondary_url', $ppdb->secondary_url)) is-filled @endif">
                    <label class="form-label">URL Tombol Sekunder</label>
                    <input type="text" name="secondary_url" maxlength="255"
                           value="{{ old('secondary_url', $ppdb->secondary_url) }}"
                           class="form-control" placeholder="/kontak atau https://...">
                </div>
            </div>

            <div class="lp-ps-foot">
                <div class="lp-ps-foot-info">
                    <span class="material-symbols-rounded">cloud_done</span>
                    Perubahan langsung tersinkron ke halaman publik.
                </div>
                <button type="submit" class="btn btn-info">
                    <span class="material-symbols-rounded" style="font-size:17px;">save</span>
                    Simpan
                </button>
            </div>
        </form>

        {{-- ============ CARD 3: CTA BAWAH ============ --}}
        <form action="{{ $action }}" method="POST" class="lp-ajax lp-ps-card" data-section-key="bottom-cta">
            @csrf
            <div class="lp-ps-head">
                <div class="lp-ps-icon is-bottom"><span class="material-symbols-rounded">campaign</span></div>
                <div class="min-w-0">
                    <h6 class="lp-ps-title">CTA Bawah Halaman PPDB</h6>
                    <div class="lp-ps-key">section: bottom-cta</div>
                </div>
            </div>

            <div class="lp-ps-field-help">Strip ajakan di bagian bawah halaman PPDB: "Siap mendaftarkan putra/putri Anda?".</div>

            <div class="lp-ps-form-grid">
                <div class="input-group input-group-outline mb-0 @if(old('bottom_eyebrow', $ppdb->bottom_eyebrow)) is-filled @endif">
                    <label class="form-label">Eyebrow</label>
                    <input type="text" name="bottom_eyebrow" maxlength="100"
                           value="{{ old('bottom_eyebrow', $ppdb->bottom_eyebrow) }}"
                           class="form-control" placeholder="PPDB 2026/2027">
                </div>
                <div class="input-group input-group-outline mb-0 @if(old('bottom_title', $ppdb->bottom_title)) is-filled @endif">
                    <label class="form-label">Judul</label>
                    <input type="text" name="bottom_title" maxlength="200"
                           value="{{ old('bottom_title', $ppdb->bottom_title) }}"
                           class="form-control" placeholder="Siap mendaftarkan putra/putri Anda?">
                </div>
                <div class="lp-ps-full">
                    <textarea name="bottom_paragraph" rows="3" class="form-control"
                              placeholder="Tim PPDB siap membantu Anda. Hubungi kami atau mulai pendaftaran online sekarang.">{{ old('bottom_paragraph', $ppdb->bottom_paragraph) }}</textarea>
                </div>
                <div class="input-group input-group-outline mb-0 @if(old('bottom_primary_text', $ppdb->bottom_primary_text)) is-filled @endif">
                    <label class="form-label">Teks Tombol Utama</label>
                    <input type="text" name="bottom_primary_text" maxlength="100"
                           value="{{ old('bottom_primary_text', $ppdb->bottom_primary_text) }}"
                           class="form-control" placeholder="Mulai Pendaftaran Online">
                </div>
                <div class="input-group input-group-outline mb-0 @if(old('bottom_primary_url', $ppdb->bottom_primary_url)) is-filled @endif">
                    <label class="form-label">URL Tombol Utama</label>
                    <input type="text" name="bottom_primary_url" maxlength="255"
                           value="{{ old('bottom_primary_url', $ppdb->bottom_primary_url) }}"
                           class="form-control" placeholder="/ppdb atau https://...">
                </div>
                <div class="input-group input-group-outline mb-0 @if(old('bottom_secondary_text', $ppdb->bottom_secondary_text)) is-filled @endif">
                    <label class="form-label">Teks Tombol Sekunder</label>
                    <input type="text" name="bottom_secondary_text" maxlength="100"
                           value="{{ old('bottom_secondary_text', $ppdb->bottom_secondary_text) }}"
                           class="form-control" placeholder="Hubungi Tim PPDB">
                </div>
                <div class="input-group input-group-outline mb-0 @if(old('bottom_secondary_url', $ppdb->bottom_secondary_url)) is-filled @endif">
                    <label class="form-label">URL Tombol Sekunder</label>
                    <input type="text" name="bottom_secondary_url" maxlength="255"
                           value="{{ old('bottom_secondary_url', $ppdb->bottom_secondary_url) }}"
                           class="form-control" placeholder="/kontak atau https://...">
                </div>
                <div class="lp-ps-full input-group input-group-outline mb-0 @if(old('bottom_meta', $ppdb->bottom_meta)) is-filled @endif">
                    <label class="form-label">Teks Meta (di bawah tombol)</label>
                    <input type="text" name="bottom_meta" maxlength="150"
                           value="{{ old('bottom_meta', $ppdb->bottom_meta) }}"
                           class="form-control" placeholder="Konsultasi gratis sebelum mendaftar">
                </div>
            </div>

            <div class="lp-ps-foot">
                <div class="lp-ps-foot-info">
                    <span class="material-symbols-rounded">cloud_done</span>
                    Perubahan langsung tersinkron ke halaman publik.
                </div>
                <button type="submit" class="btn btn-info">
                    <span class="material-symbols-rounded" style="font-size:17px;">save</span>
                    Simpan
                </button>
            </div>
        </form>

    </div>
</div>
@endsection

@section('script')
    @include('admin-landing._skrip')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Sinkronkan toggle pill (lp-ps-toggle) supaya icon + label + warna
        // berubah seketika saat user klik checkbox.
        document.querySelectorAll('.lp-ps-toggle input[type="checkbox"]').forEach(function (el) {
            var wrap = el.closest('.lp-ps-toggle');
            var icon = wrap.querySelector('.lp-ps-toggle-icon i');
            var text = wrap.querySelector('.lp-ps-toggle-text');
            var update = function () {
                if (el.checked) {
                    wrap.classList.remove('is-off');
                    wrap.classList.add('is-on');
                    if (icon) icon.className = 'bi bi-check-lg';
                    if (text) text.textContent = 'Aktif';
                } else {
                    wrap.classList.remove('is-on');
                    wrap.classList.add('is-off');
                    if (icon) icon.className = 'bi bi-x-lg';
                    if (text) text.textContent = 'Non-aktif';
                }
            };
            el.addEventListener('change', update);
            update();
        });
    });
    </script>
@endsection
