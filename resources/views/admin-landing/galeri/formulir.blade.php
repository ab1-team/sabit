@extends('layouts.tenant.base')

@section('style')
    @include('admin-landing._gaya')
    <style>
        .lp-gal-form-card {
            border: 1px solid #e2e8f0 !important;
            border-radius: .75rem !important;
            background: #fff;
        }
        .lp-gal-form-card .card-body {
            padding: 1rem 1.1rem;
        }

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

        .lp-form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem 1.1rem;
        }
        .lp-form-col {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }
        .lp-form-col-main { flex: 1 1 58%; min-width: 280px; }
        .lp-form-col-side { flex: 1 1 38%; min-width: 260px; }

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
            padding: .35rem;
        }
        .lp-preview-box.lp-preview-cover img {
            max-width: 70%;
            max-height: 75%;
        }

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

        .lp-gal-foot {
            display: flex;
            flex-direction: column;
            gap: .35rem;
            padding: .65rem 1rem;
        }
        @media (min-width: 768px) {
            .lp-gal-foot {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }
        .lp-gal-foot .lp-foot-hint {
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
        $isEdit = ($gallery->exists ?? false);
        $titleSlot = '<h5 class="lp-page-title mb-1">'.e($title).'</h5>'
            .'<p class="text-muted small mb-0">'.($isEdit
                ? 'Perbarui detail foto yang sudah dipublikasikan.'
                : 'Tambahkan foto baru untuk ditampilkan di halaman publik.').'</p>';
    @endphp
    @include('admin-landing._header-halaman', [
        'subtitle' => 'Landing Page',
        'back' => route('app.admin-landing.galleries'),
        'titleSlot' => $titleSlot,
    ])

    <form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="lp-ajax">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="card my-3 lp-gal-form-card">
            <div class="card-body">
                <div class="lp-form-row">
                    {{-- KOLOM KIRI --}}
                    <div class="lp-form-col lp-form-col-main">
                        <div class="lp-field req">
                            <label for="title">Judul</label>
                            <input id="title" type="text" name="title" class="form-control"
                                   value="{{ old('title', $gallery->title) }}"
                                   placeholder="mis. Upacara Bendera Hari Senin" required>
                        </div>

                        <div class="lp-field">
                            <label for="album">Album</label>
                            <input id="album" type="text" name="album" class="form-control"
                                   value="{{ old('album', $gallery->album) }}"
                                   placeholder="mis. Upacara, Class Meeting">
                        </div>

                        <div class="lp-field">
                            <label for="description">Deskripsi</label>
                            <textarea id="description" name="description" class="form-control" rows="3"
                                      placeholder="Keterangan singkat tentang foto (opsional)">{{ old('description', $gallery->description) }}</textarea>
                        </div>
                    </div>

                    {{-- KOLOM KANAN --}}
                    <div class="lp-form-col lp-form-col-side">
                        <div class="lp-preview-wrap">
                            <label class="form-label">Foto @if(!$isEdit)<span class="text-danger">*</span>@endif</label>
                            <label for="imageInput" class="lp-preview-box lp-preview-cover d-block" id="imagePreviewBox">
                                @if ($gallery->image)
                                    <img src="{{ Storage::disk('public')->url('landing/'.$gallery->image) }}" alt="Foto" id="imagePreviewImg">
                                @else
                                    <span class="material-symbols-rounded lp-preview-empty" id="imagePreviewEmpty">add_photo_alternate</span>
                                @endif
                                <span class="lp-preview-hint">
                                    {{ $isEdit ? 'Klik untuk ganti foto (opsional)' : 'Klik untuk pilih foto (JPG/PNG/WEBP, maks 4MB)' }}
                                </span>
                            </label>
                            <input type="file" name="image" class="d-none" accept="image/*"
                                   id="imageInput" {{ $isEdit ? '' : 'required' }}>
                            @if ($gallery->image)
                                <div class="small text-muted mt-1">File saat ini: <code>{{ $gallery->image }}</code></div>
                            @endif
                        </div>

                        <div class="lp-field" style="margin-top: .9rem;">
                            <label for="sort_order">Urutan tampil</label>
                            <input id="sort_order" type="number" name="sort_order" class="form-control"
                                   value="{{ old('sort_order', $gallery->sort_order) }}" min="0"
                                   placeholder="0">
                            <div class="help">Angka lebih kecil tampil lebih dulu.</div>
                        </div>

                        <div class="lp-publish-card">
                            <div class="lp-switch-row">
                                <div class="lp-switch-text">
                                    <strong>Publish foto ini</strong>
                                    <small>Foto yang di-publish akan tampil di halaman publik.</small>
                                </div>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="is_published" value="1"
                                           id="is_pub_gallery" {{ ($gallery->is_published ?? true) ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lp-gal-foot border-top">
                <span class="lp-foot-hint">
                    Isi semua kolom bertanda <span class="text-danger">*</span>.
                </span>
                <button type="submit" class="btn btn-info d-inline-flex align-items-center gap-1">
                    <span class="material-symbols-rounded align-middle" style="font-size:18px;">save</span>
                    <span class="align-middle">{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Foto' }}</span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@include('admin-landing._skrip')
