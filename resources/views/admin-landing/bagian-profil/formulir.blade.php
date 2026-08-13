@extends('layouts.tenant.base')

@section('style')
    @include('admin-landing._gaya')
    <style>
        .lp-ps-form-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 320px;
            gap: 1.25rem;
        }
        @media (max-width: 991.98px) {
            .lp-ps-form-grid { grid-template-columns: 1fr; }
        }

        .lp-ps-preview-card {
            background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
            border: 1px solid #e2e8f0;
            border-radius: .85rem;
            padding: 1.15rem;
            position: sticky;
            top: 90px;
        }
        .lp-ps-preview-head {
            display: flex;
            align-items: center;
            gap: .5rem;
            padding-bottom: .85rem;
            margin-bottom: .85rem;
            border-bottom: 1px dashed #e2e8f0;
        }
        .lp-ps-preview-head .material-symbols-rounded { color: #1f9d57; }
        .lp-ps-preview-title { font-weight: 700; font-size: .92rem; color: #1f2937; margin: 0; }
        .lp-ps-preview-sub { font-size: .72rem; color: #94a3b8; margin-top: 2px; }

        .lp-ps-preview-hero {
            background: linear-gradient(135deg, #f1f5ff 0%, #ecfeff 100%);
            border: 1px solid rgba(15, 23, 42, .05);
            border-radius: .75rem;
            padding: 1.15rem;
        }
        .lp-ps-preview-hero h3 {
            font-size: 1.05rem;
            font-weight: 800;
            margin: 0 0 .35rem;
            color: #0f172a;
            line-height: 1.25;
        }
        .lp-ps-preview-hero p {
            margin: 0;
            font-size: .85rem;
            color: #334155;
            line-height: 1.6;
            word-wrap: break-word;
        }
        .lp-ps-preview-badges {
            display: flex;
            flex-wrap: wrap;
            gap: .4rem;
            margin-top: .75rem;
        }
        .lp-ps-preview-badge {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            padding: .25rem .6rem;
            background: #fff;
            border: 1px solid rgba(15, 23, 42, .08);
            border-radius: 999px;
            font-size: .72rem;
            font-weight: 600;
            color: #0f172a;
        }
        .lp-ps-preview-badge.is-green { color: #059669; }
        .lp-ps-preview-badge i { font-size: .85rem; color: #059669; }

        .lp-ps-section-h {
            display: flex;
            align-items: center;
            gap: .55rem;
            font-size: .82rem;
            font-weight: 700;
            color: #334155;
            margin: 0 0 .85rem;
        }
        .lp-ps-section-h .material-symbols-rounded { font-size: 18px; color: #1f9d57; }
        .lp-ps-section-h::after {
            content: "";
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, #e2e8f0, transparent);
        }

        .lp-ps-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: .85rem;
            padding: 1.15rem 1.25rem;
            margin-bottom: 1rem;
        }
        .lp-ps-card-title {
            font-size: .85rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0 0 .15rem;
        }
        .lp-ps-card-help {
            font-size: .75rem;
            color: #94a3b8;
            margin: 0 0 .85rem;
        }

        /* Badge row visual */
        .lp-ps-badge-mock {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .25rem .65rem;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 999px;
            font-size: .78rem;
            color: #475569;
            font-weight: 600;
        }
        .lp-ps-badge-mock i { color: #1f9d57; }
    </style>
@endsection

@section('content')
<div class="px-2 py-2">
    @if (session('success'))
        <div class="alert alert-success py-2 small mb-3">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger py-2 small mb-3">
            <ul class="mb-0 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    @php
        $sectionLabels = [
            'overview'   => 'Tinjauan',
            'sejarah'    => 'Sejarah',
            'visi_misi'  => 'Visi & Misi',
            'akreditasi' => 'Akreditasi',
        ];
        $label = $sectionLabels[$item->section_key] ?? $item->section_key;
    @endphp

    <form action="{{ $action }}" method="POST" class="lp-ajax" id="lp-ps-form" data-section-key="{{ $item->section_key }}">
        @csrf
        @method('PUT')

        <div class="lp-ps-form-grid">
            {{-- ============ KOLOM KIRI: FORM ============ --}}
            <div>
                {{-- Card: Konten Utama --}}
                <div class="lp-ps-card">
                    <h6 class="lp-ps-section-h">
                        <span class="material-symbols-rounded">edit_note</span>
                        Konten Utama
                    </h6>
                    <div class="lp-ps-card-title">Judul Section</div>
                    <p class="lp-ps-card-help">Judul utama yang ditampilkan di halaman publik.</p>
                    <div class="input-group input-group-outline mb-3 @if(old('title', $item->title)) is-filled @endif">
                        <label class="form-label">Judul <span class="text-danger">*</span></label>
                        <input type="text" name="title" required maxlength="200"
                               value="{{ old('title', $item->title) }}"
                               class="form-control" placeholder="Cth: Visi &amp; Misi Sekolah">
                    </div>

                    <div class="lp-ps-card-title mt-3">Isi Konten</div>
                    <p class="lp-ps-card-help">
                        @if ($item->section_key === 'visi_misi')
                            Gunakan editor WYSIWYG untuk memformat teks. Untuk Misi, gunakan daftar bernomor (numbered list).
                        @elseif ($item->section_key === 'overview')
                            Ceritakan ringkasan sekolah. Bisa beberapa paragraf.
                        @elseif ($item->section_key === 'sejarah')
                            Ceritakan perjalanan & pencapaian sekolah dari waktu ke waktu.
                        @elseif ($item->section_key === 'akreditasi')
                            Jelaskan status akreditasi, lembaga pemberi, dan manfaatnya.
                        @else
                            Konten utama section ini.
                        @endif
                    </p>
                    <textarea name="content" id="lp-ps-content" rows="10"
                              class="form-control lp-tinymce"
                              placeholder="Tulis konten di sini...">{{ old('content', $item->content) }}</textarea>
                </div>

                {{-- Card: Badge / Label Pendukung --}}
                <div class="lp-ps-card">
                    <h6 class="lp-ps-section-h">
                        <span class="material-symbols-rounded">verified</span>
                        Badge Pendukung
                    </h6>
                    <div class="lp-ps-card-title">Badge & Label Tambahan</div>
                    <p class="lp-ps-card-help">Badge kecil yang tampil sebagai chip/pill di halaman publik (mis. Akreditasi A, NPSN).</p>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group input-group-outline mb-3 @if(old('badge_text', $item->badge_text)) is-filled @endif">
                                <label class="form-label">Badge Text</label>
                                <input type="text" name="badge_text" maxlength="100"
                                       value="{{ old('badge_text', $item->badge_text) }}"
                                       class="form-control" placeholder="Cth: Akreditasi A">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-outline mb-3 @if(old('badge_extra', $item->badge_extra)) is-filled @endif">
                                <label class="form-label">Badge Extra</label>
                                <input type="text" name="badge_extra" maxlength="100"
                                       value="{{ old('badge_extra', $item->badge_extra) }}"
                                       class="form-control" placeholder="Cth: 20212345">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-outline mb-3 @if(old('extra_label', $item->extra_label)) is-filled @endif">
                                <label class="form-label">Label untuk Badge Extra</label>
                                <input type="text" name="extra_label" maxlength="100"
                                       value="{{ old('extra_label', $item->extra_label) }}"
                                       class="form-control" placeholder="Cth: NPSN">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-1">
                        <span class="lp-ps-badge-mock">
                            <i class="bi {{ $item->badge_icon ?: 'bi-patch-check-fill' }}"></i>
                            {{ $item->badge_text ?: 'Badge Text' }}
                        </span>
                        @if ($item->badge_extra)
                            <span class="lp-ps-badge-mock">
                                <i class="bi bi-hash"></i>
                                {{ $item->extra_label ?: 'NPSN' }}: {{ $item->badge_extra }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Card: Status --}}
                <div class="lp-ps-card">
                    <h6 class="lp-ps-section-h">
                        <span class="material-symbols-rounded">toggle_on</span>
                        Status Tampil
                    </h6>
                    @include('admin-landing._komponen.input-saklar', [
                        'name' => 'is_active', 'label' => 'Aktif (tampilkan section ini di halaman publik)',
                        'checkedDefault' => $item->is_active ?? true,
                    ])
                </div>

                {{-- Save Bar --}}
                <div class="card my-3 shadow-sm">
                    <div class="card-body d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-2 p-2 pb-1">
                        <span class="fw-bold" style="font-size: 14px;">
                            <span class="material-symbols-rounded align-middle" style="font-size:16px;">info</span>
                            Perubahan akan langsung tampil di halaman publik.
                        </span>
                        <div class="d-flex gap-2 w-100 w-md-auto">
                            <a href="{{ route('app.admin-landing.profile-sections') }}" class="btn btn-light w-100 w-md-auto">Batal</a>
                            <button type="submit" class="btn btn-info w-100 w-md-auto">
                                <span class="material-symbols-rounded align-middle" style="font-size:18px;">save</span>
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============ KOLOM KANAN: LIVE PREVIEW ============ --}}
            <aside class="lp-ps-preview-card">
                <div class="lp-ps-preview-head">
                    <span class="material-symbols-rounded">visibility</span>
                    <div>
                        <div class="lp-ps-preview-title">Preview Publik</div>
                        <div class="lp-ps-preview-sub">Tampilan di halaman /profil</div>
                    </div>
                </div>

                <div class="lp-ps-preview-hero" id="lp-ps-preview">
                    <h3 id="lp-ps-preview-title">{{ $item->title ?: $label }}</h3>
                    <div id="lp-ps-preview-content">{!! $item->content ?: '<em style="color:#94a3b8;">Belum ada konten.</em>' !!}</div>

                    <div class="lp-ps-preview-badges" id="lp-ps-preview-badges">
                        @if ($item->badge_text)
                            <span class="lp-ps-preview-badge is-green">
                                <i class="bi {{ $item->badge_icon ?: 'bi-patch-check-fill' }}"></i>
                                {{ $item->badge_text }}
                            </span>
                        @endif
                        @if ($item->badge_extra)
                            <span class="lp-ps-preview-badge">
                                <i class="bi bi-hash"></i>
                                {{ $item->extra_label ?: 'NPSN' }}: {{ $item->badge_extra }}
                            </span>
                        @endif
                    </div>
                </div>
            </aside>
        </div>
    </form>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof tinymce === 'undefined') return;

    const sectionKey = document.getElementById('lp-ps-form')?.dataset.sectionKey || '';
    const toolbarBase = 'undo redo | bold italic underline | bullist numlist | link | removeformat';
    const pluginsBase = 'lists link';

    tinymce.init({
        selector: 'textarea.lp-tinymce',
        height: 320,
        menubar: false,
        plugins: pluginsBase,
        toolbar: toolbarBase,
        branding: false,
        statusbar: true,
        elementpath: false,
        promotion: false,
        setup: function (editor) {
            editor.on('input keyup paste', function () {
                const content = editor.getContent();
                const preview = document.getElementById('lp-ps-preview-content');
                if (preview) preview.innerHTML = content || '<em style="color:#94a3b8;">Belum ada konten.</em>';
            });
        },
    });

    // Live preview sinkronisasi judul & badge
    const titleInput = document.querySelector('input[name="title"]');
    const badgeTextInput = document.querySelector('input[name="badge_text"]');
    const badgeExtraInput = document.querySelector('input[name="badge_extra"]');
    const extraLabelInput = document.querySelector('input[name="extra_label"]');

    function updatePreview() {
        const tEl = document.getElementById('lp-ps-preview-title');
        const bEl = document.getElementById('lp-ps-preview-badges');
        if (tEl && titleInput) tEl.textContent = titleInput.value || '{{ $label }}';

        if (bEl) {
            const bt = badgeTextInput?.value || '';
            const bi = 'bi-patch-check-fill'; // icon dikunci (hardcoded)
            const bx = badgeExtraInput?.value || '';
            const bl = extraLabelInput?.value || 'NPSN';
            let html = '';
            if (bt) html += `<span class="lp-ps-preview-badge is-green"><i class="bi ${bi}"></i> ${bt}</span>`;
            if (bx) html += `<span class="lp-ps-preview-badge"><i class="bi bi-hash"></i> ${bl}: ${bx}</span>`;
            bEl.innerHTML = html;
        }
    }

    [titleInput, badgeTextInput, badgeExtraInput, extraLabelInput]
        .filter(Boolean)
        .forEach(el => el.addEventListener('input', updatePreview));
});
</script>
@include('admin-landing._skrip')
@endsection