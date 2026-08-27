@extends('layouts.tenant.base')

@section('style')
    @include('admin-landing._gaya')
    <style>
        /* Bungkus TinyMCE di dalam .input-group-outline supaya pojok kiri
           konsisten melengkung mengikuti border-radius Material Dashboard. */
        .lp-tinymce-wrap.input-group.input-group-outline {
            border: 1px solid #d4d8dd;
            border-radius: .5rem;
            overflow: hidden;
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        .lp-tinymce-wrap.input-group.input-group-outline.is-filled,
        .lp-tinymce-wrap.input-group.input-group-outline:focus-within {
            border-color: #1d4ed8;
            box-shadow: 0 0 0 3px rgba(29,78,216,.12);
        }
        .lp-tinymce-wrap.input-group.input-group-outline .tox-tinymce {
            border: none !important;
            border-radius: 0 !important;
        }
        .lp-tinymce-wrap.input-group.input-group-outline .tox .tox-toolbar__primary {
            background: #f8fafc !important;
        }
    </style>
@endsection

@section('content')
<div class="px-2 py-2">
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
                <div class="row g-3 align-items-start">
                    <div class="col-md-8 d-flex flex-column">
                        <div class="input-group input-group-outline mb-2 @if(old('title', $post->title)) is-filled @endif">
                            <label class="form-label">Judul <span class="text-danger">*</span></label>
                            <input id="title" type="text" name="title" class="form-control"
                                   value="{{ old('title', $post->title) }}" required>
                        </div>

                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="input-group input-group-outline mb-2 @if(old('category', $post->category)) is-filled @endif">
                                    <label class="form-label">Kategori</label>
                                    <input id="category" type="text" name="category" class="form-control"
                                           value="{{ old('category', $post->category) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-outline mb-2 @if(old('published_at', $post->published_at?->format('Y-m-d'))) is-filled @endif">
                                    <label class="form-label">Tanggal Publikasi</label>
                                    <input id="published_at" type="text" name="published_at" class="form-control datepicker"
                                           value="{{ old('published_at', $post->published_at?->format('Y-m-d')) }}">
                                </div>
                            </div>
                        </div>

                        <div class="input-group input-group-outline mb-2 @if(old('excerpt', $post->excerpt)) is-filled @endif">
                            <label class="form-label">Ringkasan</label>
                            <textarea id="excerpt" name="excerpt" class="form-control" rows="2">{{ old('excerpt', $post->excerpt) }}</textarea>
                        </div>

                        <div class="input-group input-group-outline mb-0 @if(old('content', $post->content)) is-filled @endif lp-tinymce-wrap flex-grow-1 d-flex flex-column">
                            <label class="form-label">Konten <span class="text-danger">*</span></label>
                            <textarea id="content" name="content" class="form-control lp-tinymce flex-grow-1">{{ old('content', $post->content) }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-4 d-flex flex-column">
                        <div class="lp-preview-wrap mb-0">
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

                        <div class="lp-publish-card mt-2">
                            <div class="form-check form-switch mb-2">
                                <input type="hidden" name="is_published" value="0">
                                <input class="form-check-input" type="checkbox" name="is_published" value="1"
                                       id="is_pub_post" {{ ($post->is_published ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_pub_post">Publish (tampilkan di halaman publik)</label>
                            </div>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" name="is_featured" value="1"
                                       id="is_featured_post" {{ ($post->is_featured ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_featured_post">Tampilkan di Beranda (artikel pilihan)</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body border-top d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-2 p-2 pb-1">
                <span class="fw-bold" style="font-size: 14px;">
                    Isi semua kolom bertanda <span class="text-danger">*</span>.
                </span>
                <div class="d-flex flex-wrap gap-2 justify-content-end">
                    <a href="{{ route('app.admin-landing.posts') }}" class="btn btn-light d-inline-flex align-items-center gap-1">
                        <span class="material-symbols-rounded align-middle" style="font-size:18px;">arrow_back</span>
                        <span class="align-middle">Kembali</span>
                    </a>
                    <button type="submit" class="btn btn-info d-inline-flex align-items-center gap-1">
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
    // Flatpickr datepicker (gaya siswa)
    if (window.flatpickr) {
        flatpickr('.datepicker', { dateFormat: "Y-m-d" });
        $('.datepicker').on('change', function () {
            $(this).closest('.input-group').addClass('is-filled');
        });
    }

    if (typeof tinymce === 'undefined') return;

    // Sinkronkan isi TinyMCE ke textarea SEBELUM submit
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
        min_height: 280,
        height: 'auto',
        autoresize_bottom_margin: 8,
        menubar: false,
        plugins: 'lists link image media table code wordcount autoresize',
        toolbar: 'undo redo | bold italic underline | bullist numlist | link image media lpfileopen lpyt table | code',
        branding: false,
        promotion: false,
        relative_urls: false,
        remove_script_host: false,
        convert_urls: true,
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
