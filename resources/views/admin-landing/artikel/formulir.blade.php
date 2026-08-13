@extends('layouts.tenant.base')

@section('style')
    @include('admin-landing._gaya')
    <style>
        .lp-post-form-card {
            border: 1px solid #e2e8f0 !important;
            border-radius: .75rem !important;
            background: #fff;
        }
        .lp-post-form-card .card-body {
            padding: 1rem 1.1rem;
        }

        /* Field sederhana: label di atas, input standar, tinggi konsisten 42px */
        .lp-field {
            display: flex;
            flex-direction: column;
            gap: .35rem;
            margin-bottom: .9rem;
        }
        .lp-field > label {
            font-size: .82rem;
            font-weight: 600;
            color: #334155;
            margin: 0;
        }
        .lp-field .form-control,
        .lp-field textarea.form-control {
            height: 42px;
            padding: .5rem .75rem;
            border-radius: .5rem;
            border: 1px solid #d4d8dd;
            background: #fff;
            color: #1f2937;
            font-size: .92rem;
            transition: border-color .15s ease, box-shadow .15s ease;
            box-shadow: none !important;
        }
        .lp-field textarea.form-control {
            height: auto;
            min-height: 84px;
            line-height: 1.45;
        }
        .lp-field .form-control:focus,
        .lp-field textarea.form-control:focus {
            border-color: #1d4ed8;
            box-shadow: 0 0 0 3px rgba(29,78,216,.12) !important;
            outline: none;
        }
        .lp-field .help {
            font-size: .72rem;
            color: #94a3b8;
        }
        .lp-field.req > label::after {
            content: " *";
            color: #dc2626;
        }

        /* Kolom preview flex: box mengikuti tinggi konten kolom */
        .lp-form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem 1.1rem;
        }
        .lp-form-col {
            display: flex;
            flex-direction: column;
            gap: 0;
            min-width: 0;
        }
        .lp-form-col-main { flex: 1 1 56%; min-width: 280px; }
        .lp-form-col-side { flex: 1 1 40%; min-width: 260px; }

        /* Preview box */
        .lp-preview-wrap {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .lp-preview-wrap .form-label {
            font-size: .82rem;
            font-weight: 600;
            color: #334155;
            margin: 0 0 .35rem 0;
        }
        .lp-preview-box.lp-preview-cover {
            flex: 1 1 auto;
            min-height: 320px;
            aspect-ratio: auto;
            padding: .35rem;
        }
        .lp-preview-box.lp-preview-cover img {
            max-width: 70%;
            max-height: 75%;
        }

        /* Side card publish */
        .lp-publish-card {
            border: 1px dashed #e2e8f0;
            border-radius: .65rem;
            padding: .75rem .9rem;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            gap: .25rem;
        }
        .lp-switch-row {
            display: flex;
            gap: .65rem;
            align-items: center;
            justify-content: space-between;
            padding: .35rem 0;
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
            font-size: .85rem;
            color: #1f2937;
            line-height: 1.2;
        }
        .lp-switch-row .lp-switch-text small {
            font-size: .72rem;
            color: #64748b;
        }
        .lp-switch-row .form-check.form-switch {
            margin: 0;
            flex: 0 0 auto;
        }

        /* TinyMCE wrapper */
        .lp-tinymce-wrap .tox-tinymce {
            border-radius: .5rem !important;
            border-color: #d4d8dd !important;
        }

        /* Footer */
        .lp-post-foot {
            display: flex;
            flex-direction: column;
            gap: .35rem;
            padding: .65rem 1rem;
        }
        @media (min-width: 768px) {
            .lp-post-foot {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }
        .lp-post-foot .lp-foot-hint {
            font-size: .82rem;
            color: #475569;
        }

        @media (max-width: 767.98px) {
            .lp-form-col-main, .lp-form-col-side { flex-basis: 100%; }
        }
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
        $isEdit = ($post->exists ?? false);
        $titleSlot = '<h5 class="lp-page-title mb-1">'.e($title).'</h5>'
            .'<p class="text-muted small mb-0">'.($isEdit
                ? 'Perbarui artikel / berita yang sudah ada.'
                : 'Tulis artikel atau berita baru untuk ditampilkan di halaman publik.').'</p>';
    @endphp
    @include('admin-landing._header-halaman', [
        'subtitle' => 'Landing Page',
        'back' => route('app.admin-landing.posts'),
        'titleSlot' => $titleSlot,
    ])

    <form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="lp-ajax">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="card my-3 lp-post-form-card">
            <div class="card-body">
                <div class="lp-form-row">
                    {{-- KOLOM KIRI (lebih lebar) --}}
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
                            <textarea id="excerpt" name="excerpt" class="form-control" rows="3"
                                      placeholder="Cuplikan singkat yang tampil di daftar artikel (opsional)">{{ old('excerpt', $post->excerpt) }}</textarea>
                        </div>

                        <div class="lp-field req lp-tinymce-wrap">
                            <label for="content">Konten</label>
                            <textarea id="content" name="content" class="form-control lp-tinymce" rows="8"
                                      placeholder="Tulis isi artikel di sini...">{{ old('content', $post->content) }}</textarea>
                        </div>
                    </div>

                    {{-- KOLOM KANAN (lebih sempit) --}}
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

                        <div class="lp-field" style="margin-top: .9rem;">
                            <label for="tags">Tag</label>
                            <input id="tags" type="text" name="tags" class="form-control"
                                   value="{{ old('tags', $post->tags) }}"
                                   placeholder="pisahkan dengan koma">
                            <div class="help">Pisahkan beberapa tag dengan tanda koma.</div>
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
                <button type="submit" class="btn btn-info d-inline-flex align-items-center gap-1">
                    <span class="material-symbols-rounded align-middle" style="font-size:18px;">save</span>
                    <span class="align-middle">{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Artikel' }}</span>
                </button>
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
        height: 320,
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
