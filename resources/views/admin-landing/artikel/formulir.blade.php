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

    @php $isEdit = ($post->exists ?? false); @endphp

    <form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="lp-ajax">
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
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof tinymce === 'undefined') return;
    tinymce.init({
        selector: 'textarea.lp-tinymce',
        height: 240,
        menubar: false,
        plugins: 'lists link image table code',
        toolbar: 'undo redo | bold italic underline | bullist numlist | link image table | code',
        branding: false,
        promotion: false,
    });
});
</script>
@include('admin-landing._skrip')
@endsection
