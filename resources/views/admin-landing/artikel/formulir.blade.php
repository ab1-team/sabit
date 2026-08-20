@extends('layouts.tenant.base')

@section('style')
    @include('admin-landing._gaya')
    <style>
        .lp-post-form-card {
            border: 1px solid #e2e8f0 !important;
            border-radius: .75rem !important;
            background: #fff;
        }
        .lp-post-form-card > .card-body { padding: .9rem 1.05rem; }

        /* Field: ringkas, label kecil, input seragam */
        .lp-field {
            display: flex;
            flex-direction: column;
            gap: .3rem;
            margin-bottom: .7rem;
        }
        .lp-field > label {
            font-size: .8rem;
            font-weight: 600;
            color: #334155;
            margin: 0;
        }
        .lp-field .form-control,
        .lp-field textarea.form-control {
            height: 38px;
            padding: .45rem .7rem;
            border-radius: .5rem;
            border: 1px solid #d4d8dd;
            background: #fff;
            color: #1f2937;
            font-size: .9rem;
            transition: border-color .15s ease, box-shadow .15s ease;
            box-shadow: none !important;
        }
        .lp-field textarea.form-control {
            height: auto;
            min-height: 70px;
            line-height: 1.45;
        }
        .lp-field .form-control:focus,
        .lp-field textarea.form-control:focus {
            border-color: #1d4ed8;
            box-shadow: 0 0 0 3px rgba(29,78,216,.12) !important;
            outline: none;
        }
        .lp-field .help {
            font-size: .7rem;
            color: #94a3b8;
        }
        .lp-field.req > label::after { content: " *"; color: #dc2626; }

        /* Layout 2 kolom via CSS Grid agar field bisa sejajar horizontal */
        .lp-form-row {
            display: grid;
            grid-template-columns: minmax(0, 1.5fr) minmax(0, 1fr);
            gap: .9rem 1rem;
            align-items: stretch;
        }
        .lp-form-col {
            display: flex;
            flex-direction: column;
            gap: 0;
            min-width: 0;
        }
        .lp-form-col-main { grid-column: 1; }
        .lp-form-col-side { grid-column: 2; }

        @media (max-width: 767.98px) {
            .lp-form-row { grid-template-columns: 1fr; }
            .lp-form-col-main,
            .lp-form-col-side { grid-column: 1; }
        }

        /* Kolom kanan didistribusikan rata agar tidak ada space kosong
           jika Gambar Sampul lebih pendek dari kolom kiri. */
        .lp-form-col-side .lp-field:last-of-type { margin-bottom: 0; }
        .lp-form-col-side .lp-publish-card { margin-bottom: 0; }

        /* Preview box gambar sampul */
        .lp-preview-wrap {
            display: flex;
            flex-direction: column;
            margin-bottom: .7rem;
        }
        .lp-preview-wrap > .form-label {
            font-size: .8rem;
            font-weight: 600;
            color: #334155;
            margin: 0 0 .3rem 0;
        }
        .lp-preview-box.lp-preview-cover {
            position: relative;
            flex: 0 0 auto;
            height: 250px;
            min-height: 250px;
            padding: 0;
        }
        .lp-preview-box.lp-preview-cover img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }
        .lp-preview-box.lp-preview-cover .lp-preview-empty,
        .lp-preview-box.lp-preview-cover .lp-preview-hint {
            position: relative;
            z-index: 1;
        }
        .lp-preview-box.lp-preview-cover:not(:has(img)) .lp-preview-empty,
        .lp-preview-box.lp-preview-cover:not(:has(img)) .lp-preview-hint {
            position: absolute;
        }
        .lp-preview-box.lp-preview-cover:not(:has(img)) .lp-preview-empty {
            top: 50%;
            left: 50%;
            transform: translate(-50%, -65%);
        }
        .lp-preview-box.lp-preview-cover:not(:has(img)) .lp-preview-hint {
            top: 50%;
            left: 50%;
            transform: translate(-50%, 30%);
        }
        .lp-preview-box.lp-preview-cover .lp-preview-hint {
            position: absolute;
            top: auto;
            bottom: .5rem;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(15,23,42,.55);
            color: #fff;
            padding: .25rem .55rem;
            border-radius: .35rem;
            font-size: .72rem;
            font-weight: 500;
            white-space: nowrap;
            z-index: 2;
            opacity: .85;
            transition: opacity .15s ease, background .15s ease;
        }
        .lp-preview-box.lp-preview-cover:hover .lp-preview-hint {
            opacity: 1;
            background: rgba(15,23,42,.75);
        }

        /* Side card publish + featured: ringkas */
        .lp-publish-card {
            border: 1px solid #e2e8f0;
            border-radius: .65rem;
            padding: .15rem .9rem;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            gap: 0;
        }
        .lp-switch-row {
            display: flex;
            gap: .65rem;
            align-items: center;
            justify-content: space-between;
            padding: .55rem 0;
        }
        .lp-switch-row + .lp-switch-row {
            border-top: 1px dashed #e2e8f0;
        }
        .lp-switch-row .lp-switch-text {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }
        .lp-switch-row .lp-switch-text strong {
            font-size: .82rem;
            color: #1f2937;
            line-height: 1.2;
        }
        .lp-switch-row .lp-switch-text small {
            font-size: .7rem;
            color: #64748b;
        }
        .lp-switch-row .form-check.form-switch {
            margin: 0;
            flex: 0 0 auto;
        }

        /* TinyMCE: tinggi pas agar total form tidak overflow */
        .lp-tinymce-wrap .tox-tinymce {
            border-radius: .5rem !important;
            border-color: #d4d8dd !important;
        }
        .lp-tinymce-wrap .tox .tox-toolbar {
            background: #f8fafc !important;
        }

        /* Footer form */
        .lp-post-foot {
            display: flex;
            flex-direction: column;
            gap: .5rem;
            padding: .65rem 1.05rem;
            background: #f8fafc;
            border-radius: 0 0 .75rem .75rem;
        }
        @media (min-width: 768px) {
            .lp-post-foot {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }
        .lp-post-foot .lp-foot-hint {
            font-size: .8rem;
            color: #475569;
        }
        .lp-post-foot .btn {
            min-height: 38px;
            padding: .4rem 1rem;
            border-radius: .5rem;
            font-size: .88rem;
        }

        /* Kurangi padding wrapper luar halaman */
        .lp-post-page { padding: .35rem .5rem; }
        @media (max-width: 575.98px) {
            .lp-post-page { padding: .35rem .35rem; }
        }

        /* Batas tinggi konten kolom kanan: disesuaikan dengan kolom kiri */
        .lp-preview-empty { font-size: 36px; }
    </style>
@endsection

@section('content')
<div class="lp-post-page">
    @if (session('success'))
        <div class="alert alert-success py-2 small mb-2">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger py-2 small mb-2">
            <ul class="mb-0 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="alert alert-danger py-2 small mb-2 lp-ajax-errors" style="display:none;" role="alert">
        <strong class="lp-ajax-errors-title d-block mb-1">Data belum lengkap:</strong>
        <ul class="mb-0 ps-3 lp-ajax-errors-list"></ul>
    </div>

    @php $isEdit = ($post->exists ?? false); @endphp

    <form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="lp-ajax" id="lpPostForm">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        <div class="card my-2 lp-post-form-card">
            <div class="card-body">
                <div class="lp-form-row">
                    {{-- KOLOM KIRI --}}
                    <div class="lp-form-col lp-form-col-main">
                        <div class="lp-field req">
                            <label for="title">Judul</label>
                            <input id="title" type="text" name="title" class="form-control"
                                   value="{{ old('title', $post->title) }}"
                                   placeholder="Judul artikel / berita" required>
                        </div>

                        <div class="lp-field">
                            <label for="category">Kategori</label>
                            <input id="category" type="text" name="category" class="form-control"
                                   value="{{ old('category', $post->category) }}"
                                   placeholder="mis. Pengumuman, Kegiatan, Prestasi">
                        </div>

                        <div class="lp-field">
                            <label for="excerpt">Ringkasan</label>
                            <textarea id="excerpt" name="excerpt" class="form-control" rows="2"
                                      placeholder="Cuplikan singkat yang tampil di daftar artikel (opsional)">{{ old('excerpt', $post->excerpt) }}</textarea>
                        </div>

                        <div class="lp-field req lp-tinymce-wrap">
                            <label for="content">Konten</label>
                            <textarea id="content" name="content" class="form-control lp-tinymce" rows="6"
                                      placeholder="Tulis isi artikel di sini...">{{ old('content', $post->content) }}</textarea>
                        </div>
                    </div>

                    {{-- KOLOM KANAN --}}
                    <div class="lp-form-col lp-form-col-side">
                        <div class="lp-preview-wrap">
                            <label class="form-label">Gambar Sampul</label>
                            <label for="imageInput" class="lp-preview-box lp-preview-cover d-block" id="imagePreviewBox">
                                @if ($post->image)
                                    <img src="{{ Storage::disk('public')->url('landing/'.$post->image) }}" alt="Gambar Sampul" id="imagePreviewImg">
                                @else
                                    <span class="material-symbols-rounded lp-preview-empty" id="imagePreviewEmpty">add_photo_alternate</span>
                                @endif
                                <span class="lp-preview-hint">
                                    {{ $isEdit ? 'Klik untuk ganti gambar (opsional)' : 'Klik untuk pilih gambar (JPG/PNG/WEBP, maks 4MB)' }}
                                </span>
                            </label>
                            <input type="file" name="image" class="d-none" accept="image/*" id="imageInput">
                            @if ($post->image)
                                <div class="small text-muted mt-1">File saat ini: <code>{{ $post->image }}</code></div>
                            @endif
                        </div>

                        <div class="lp-field">
                            <label for="published_at">Tanggal Publikasi</label>
                            <input id="published_at" type="text" name="published_at" class="form-control lp-date"
                                   value="{{ old('published_at', $post->published_at?->format('Y-m-d H:i')) }}"
                                   placeholder="YYYY-MM-DD HH:MM">
                            <div class="help">Kosongkan untuk otomatis menggunakan waktu saat ini.</div>
                        </div>

                        <div class="lp-publish-card">
                            <div class="lp-switch-row">
                                <div class="lp-switch-text">
                                    <strong>Publish</strong>
                                    <small>Tampilkan artikel di halaman publik.</small>
                                </div>
                                <div class="form-check form-switch m-0">
                                    <input type="hidden" name="is_published" value="0">
                                    <input class="form-check-input" type="checkbox" name="is_published" value="1"
                                           id="is_pub_post" {{ ($post->is_published ?? true) ? 'checked' : '' }}>
                                </div>
                            </div>
                            <div class="lp-switch-row">
                                <div class="lp-switch-text">
                                    <strong>Tampilkan di Beranda</strong>
                                    <small>Sematkan sebagai artikel pilihan.</small>
                                </div>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="is_featured" value="1"
                                           id="is_featured_post" {{ ($post->is_featured ?? false) ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lp-post-foot border-top">
                <span class="lp-foot-hint">
                    Isi semua kolom bertanda <span class="text-danger">*</span>.
                </span>
                <div class="d-flex flex-wrap gap-2 justify-content-end">
                    <a href="{{ route('app.admin-landing.posts') }}" class="btn btn-light d-inline-flex align-items-center gap-1">
                        <span class="material-symbols-rounded align-middle" style="font-size:18px;">arrow_back</span>
                        <span class="align-middle">Kembali</span>
                    </a>
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1">
                        <span class="material-symbols-rounded align-middle" style="font-size:18px;">save</span>
                        <span class="align-middle">{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Artikel' }}</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
<input type="file" id="lpPostContentFile" accept="image/*,video/*" class="d-none">

<style>
    .lp-tinymce .lp-file-open-btn,
    .lp-tinymce .lp-yt-btn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .lp-tinymce .lp-tinymce-file-msg {
        position: fixed;
        right: 1rem;
        bottom: 1rem;
        z-index: 1080;
        background: #0f172a;
        color: #f8fafc;
        padding: .55rem .85rem;
        border-radius: .5rem;
        font-size: .8rem;
        box-shadow: 0 6px 18px rgba(15,23,42,.18);
        opacity: 0;
        transform: translateY(8px);
        transition: opacity .2s ease, transform .2s ease;
    }
    .lp-tinymce .lp-tinymce-file-msg.show {
        opacity: 1;
        transform: translateY(0);
    }
    .lp-tinymce .lp-tinymce-file-msg.error { background: #b91c1c; }
    .tox .tox-tbtn[aria-label="Sisipkan File"] .tox-icon::before,
    .tox .tox-tbtn[aria-label="Sisipkan Video YouTube"] .tox-icon::before {
        font-family: 'Material Symbols Rounded';
        font-size: 20px;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof tinymce === 'undefined') return;

    // Sinkronkan isi TinyMCE ke textarea SEBELUM submit agar FormData
    // tidak pernah mengirim konten kosong (mis. browser yang tidak
    // auto-save saat submit form via AJAX).
    const lpForm = document.getElementById('lpPostForm');
    if (lpForm) {
        lpForm.addEventListener('submit', function () {
            const editor = tinymce.get('content');
            if (editor) {
                try { editor.save(); } catch (e) { try { editor.triggerSave(); } catch (e2) {} }
            }
        }, true);
    }

    const fileInput = document.getElementById('lpPostContentFile');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        || document.querySelector('input[name="_token"]')?.value;
    const uploadUrl = @json(route('app.admin-landing.posts.upload-content'));

    function showMsg(text, isError) {
        let el = document.querySelector('.lp-tinymce-file-msg');
        if (!el) {
            el = document.createElement('div');
            el.className = 'lp-tinymce-file-msg';
            document.body.appendChild(el);
        }
        el.textContent = text;
        el.classList.toggle('error', !!isError);
        el.classList.add('show');
        clearTimeout(el._t);
        el._t = setTimeout(() => el.classList.remove('show'), 2400);
    }

    function buildHtml(kind, url) {
        if (kind === 'video') {
            return '<p><video controls playsinline preload="metadata" style="max-width:100%;height:auto;border-radius:.5rem;">'
                + '<source src="' + url + '">Browser Anda tidak mendukung tag video.</video></p><p></p>';
        }
        return '<p><img src="' + url + '" alt="" style="max-width:100%;height:auto;border-radius:.5rem;"></p><p></p>';
    }

    function uploadAndInsert(file) {
        if (!file) return;
        if (!csrf) { showMsg('CSRF token tidak ditemukan.', true); return; }
        if (file.size > 50 * 1024 * 1024) { showMsg('Ukuran file melebihi 50MB.', true); return; }

        const fd = new FormData();
        fd.append('file', file);
        if (file.type.startsWith('video/')) fd.append('type', 'video');
        else if (file.type.startsWith('image/')) fd.append('type', 'image');

        const editor = tinymce.get('content');
        const cursor = editor ? editor.selection.getBookmark() : null;
        showMsg('Mengunggah ' + (file.name.length > 28 ? file.name.slice(0, 25) + '...' : file.name) + '...');

        fetch(uploadUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: fd,
            credentials: 'same-origin',
        })
        .then(r => r.json().then(j => ({ ok: r.ok, status: r.status, body: j })))
        .then(({ ok, status, body }) => {
            if (!ok || !body.success) {
                showMsg(body.msg || ('Gagal mengunggah (' + status + ').'), true);
                return;
            }
            if (editor) {
                if (cursor) editor.selection.moveToBookmark(cursor);
                editor.insertContent(buildHtml(body.kind || (file.type.startsWith('video/') ? 'video' : 'image'), body.location));
                editor.focus();
            }
            showMsg('Berhasil disisipkan.');
        })
        .catch(() => showMsg('Gagal terhubung ke server.', true));
    }

    fileInput.addEventListener('change', function () {
        const f = fileInput.files && fileInput.files[0];
        uploadAndInsert(f);
        fileInput.value = '';
    });

    tinymce.init({
        selector: 'textarea.lp-tinymce',
        height: 320,
        menubar: false,
        plugins: 'lists link image media table code wordcount',
        toolbar: 'undo redo | bold italic underline | bullist numlist | link image media lpfileopen lpyt table | code',
        branding: false,
        promotion: false,
        file_picker_types: 'image media',
        image_caption: true,
        image_dimensions: true,
        media_live_embeds: true,
        media_alt_source: false,
        media_poster: false,
        setup: function (editor) {
            editor.ui.registry.addIcon('lpfileopen',
                '<svg width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6Zm-1 7V3.5L18.5 9H13Z"/></svg>');
            editor.ui.registry.addIcon('lpyt',
                '<svg width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M21.6 7.2a2.5 2.5 0 0 0-1.76-1.77C18.27 5 12 5 12 5s-6.27 0-7.84.43A2.5 2.5 0 0 0 2.4 7.2 26 26 0 0 0 2 12a26 26 0 0 0 .4 4.8 2.5 2.5 0 0 0 1.76 1.77C5.73 19 12 19 12 19s6.27 0 7.84-.43a2.5 2.5 0 0 0 1.76-1.77A26 26 0 0 0 22 12a26 26 0 0 0-.4-4.8ZM10 15V9l5.2 3L10 15Z"/></svg>');

            editor.ui.registry.addButton('lpfileopen', {
                icon: 'lpfileopen',
                tooltip: 'Sisipkan File (gambar / video dari komputer)',
                onAction: function () { fileInput.click(); },
            });

            editor.ui.registry.addButton('lpyt', {
                icon: 'lpyt',
                tooltip: 'Sisipkan Video YouTube (tempel URL)',
                onAction: function () {
                    const url = window.prompt('Tempel URL YouTube (mis. https://youtu.be/xxxx):');
                    if (!url) return;
                    const html = '<p><iframe width="560" height="315" src="' + url.replace('youtu.be/', 'youtube.com/embed/').replace('watch?v=', 'embed/') + '"'
                        + ' title="YouTube video" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></p><p></p>';
                    editor.insertContent(html);
                },
            });
        },
    });
});
</script>
@include('admin-landing._skrip')
@endsection
