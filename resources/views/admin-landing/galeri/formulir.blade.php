@extends('layouts.tenant.base')

@section('style')
    @include('admin-landing._gaya')
    <style>
        .lp-gal-form-card {
            border: 1px solid #e2e8f0 !important;
            border-radius: .75rem !important;
            background: #fff;
        }
        .lp-gal-form-card > .card-body { padding: .9rem 1.05rem; }

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
            min-height: 170px;
            line-height: 1.5;
            resize: vertical;
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
        .lp-field.req > label::after {
            content: " *";
            color: #dc2626;
        }

        /* Layout 2 kolom via CSS Grid (sejajar posts) */
        .lp-form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
            align-items: stretch;
        }
        @media (min-width: 768px) {
            .lp-form-grid {
                grid-template-columns: minmax(0, 1.5fr) minmax(0, 1fr);
                gap: 1rem;
            }
        }
        .lp-form-col {
            display: flex;
            flex-direction: column;
            gap: 0;
            min-width: 0;
        }

        /* Kolom kanan: field terakhir dan card publish rapat ke bawah */
        .lp-form-col-side .lp-field:last-of-type { margin-bottom: 0; }
        .lp-form-col-side .lp-publish-card { margin-top: 0; }

        /* Preview box gambar – tinggi tetap 180px (ringkas) */
        .lp-preview-wrap {
            display: flex;
            flex-direction: column;
            flex: 0 0 auto;
            margin-bottom: .7rem;
        }
        .lp-preview-wrap > .form-label {
            font-size: .8rem;
            font-weight: 600;
            color: #334155;
            margin: 0 0 .3rem 0;
            flex: 0 0 auto;
        }
        .lp-preview-box.lp-preview-cover {
            position: relative;
            height: 180px;
            min-height: 180px;
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
        .lp-preview-empty { font-size: 36px; }
        .lp-preview-title {
            display: block;
            font-size: .82rem;
            font-weight: 600;
            color: #475569;
            margin-top: .55rem;
        }

        /* Side: Urutan tampil + Publish jadi 1 baris inline minimalis */
        .lp-publish-card {
            display: flex;
            flex-wrap: wrap;
            gap: .9rem;
            align-items: center;
            justify-content: space-between;
            padding: .55rem .85rem;
            border-top: 1px dashed #e2e8f0;
            flex: 0 0 auto;
            background: transparent;
        }
        .lp-publish-card .lp-field {
            margin-bottom: 0;
            flex: 0 0 auto;
        }
        .lp-publish-card .lp-field-sort {
            min-width: 130px;
        }
        .lp-publish-card .lp-field-switch {
            padding-bottom: 0;
        }
        .lp-publish-card .lp-switch-inline {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
        }
        .lp-publish-card .lp-switch-text {
            display: flex;
            flex-direction: column;
            line-height: 1.15;
        }
        .lp-publish-card .lp-switch-text strong {
            font-size: .82rem;
            color: #1f2937;
        }
        .lp-publish-card .lp-switch-text small {
            font-size: .7rem;
            color: #64748b;
        }
        .lp-publish-card .form-check.form-switch {
            margin: 0;
            flex: 0 0 auto;
        }
        @media (max-width: 575.98px) {
            .lp-publish-card .lp-field-sort,
            .lp-publish-card .lp-field-switch { flex: 1 1 100%; }
        }

        /* Footer form: tombol tetap sejajar posts */
        .lp-gal-foot {
            display: flex;
            flex-direction: column;
            gap: .5rem;
            padding: .55rem 1.05rem;
            background: #f8fafc;
            border-radius: 0 0 .75rem .75rem;
        }
        @media (min-width: 768px) {
            .lp-gal-foot {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }
        .lp-gal-foot .lp-foot-hint { font-size: .8rem; color: #475569; }
        .lp-gal-foot .btn {
            min-height: 38px;
            padding: .4rem 1rem;
            border-radius: .5rem;
            font-size: .88rem;
        }

        .lp-gal-page { padding: .35rem .5rem; }
        @media (max-width: 575.98px) {
            .lp-gal-page { padding: .35rem .35rem; }
        }
    </style>
@endsection

@section('content')
<div class="lp-gal-page">
    @if (session('success'))
        <div class="alert alert-success py-2 small mb-2">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger py-2 small mb-2">
            <ul class="mb-0 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    @php $isEdit = ($gallery->exists ?? false); @endphp

    <form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="lp-ajax">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        <div class="card my-2 lp-gal-form-card">
            <div class="card-body">
                <div class="lp-form-grid">
                    {{-- KOLOM KIRI --}}
                    <div class="lp-form-col">
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
                            <textarea id="description" name="description" class="form-control" rows="5"
                                      placeholder="Keterangan singkat tentang foto (opsional)">{{ old('description', $gallery->description) }}</textarea>
                        </div>
                    </div>

                    {{-- KOLOM KANAN --}}
                    <div class="lp-form-col lp-form-col-side">
                        <div class="lp-preview-wrap">
                            <label for="imageInput" class="lp-preview-box lp-preview-cover d-block" id="imagePreviewBox"
                                   aria-label="Foto sampul">
                                @if ($gallery->image)
                                    <img src="{{ Storage::disk('public')->url('landing/'.$gallery->image) }}" alt="Foto" id="imagePreviewImg">
                                @else
                                    <span class="material-symbols-rounded lp-preview-empty" id="imagePreviewEmpty">add_photo_alternate</span>
                                    <span class="lp-preview-title">Foto sampul @if(!$isEdit)<span class="text-danger">*</span>@endif</span>
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

                        <div class="lp-publish-card">
                            <div class="lp-field lp-field-sort">
                                <label for="sort_order">Urutan tampil</label>
                                <input id="sort_order" type="number" name="sort_order" class="form-control"
                                       value="{{ old('sort_order', $gallery->sort_order) }}" min="0"
                                       placeholder="0">
                            </div>
                            <div class="lp-field lp-field-switch">
                                <div class="lp-switch-inline">
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" name="is_published" value="1"
                                               id="is_pub_gallery" {{ ($gallery->is_published ?? true) ? 'checked' : '' }}>
                                    </div>
                                    <div class="lp-switch-text">
                                        <strong>Publish</strong>
                                        <small>Tampilkan di halaman publik.</small>
                                    </div>
                                </div>
                            </div>
                            <div class="help w-100 mt-1 mb-0">Urutan lebih kecil tampil lebih dulu.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lp-gal-foot border-top">
                <span class="lp-foot-hint">
                    Isi semua kolom bertanda <span class="text-danger">*</span>.
                </span>
                <div class="d-flex flex-wrap gap-2 justify-content-end">
                    <a href="{{ route('app.admin-landing.galleries') }}" class="btn btn-light d-inline-flex align-items-center gap-1">
                        <span class="material-symbols-rounded align-middle" style="font-size:18px;">arrow_back</span>
                        <span class="align-middle">Kembali</span>
                    </a>
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1">
                        <span class="material-symbols-rounded align-middle" style="font-size:18px;">save</span>
                        <span class="align-middle">{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Foto' }}</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@include('admin-landing._skrip')
