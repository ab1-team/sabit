@extends('layouts.tenant.base')

@section('style')
    @include('landing-admin._styles')
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
        $titleSlot = '<h5 class="lp-page-title mb-1">'.e($title).'</h5>';
    @endphp
    @include('landing-admin._page-header', [
        'subtitle' => 'Landing Page',
        'back' => route('app.landing.galleries'),
        'titleSlot' => $titleSlot,
    ])

    <form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="lp-ajax">
        @csrf
        @if (($gallery->exists ?? false))
            @method('PUT')
        @endif

        <div class="card my-3 shadow-sm">
            <div class="card-body p-3">
                <div class="row">
                    @include('landing-admin._components.text-input', [
                        'name' => 'title', 'label' => 'Judul', 'required' => true,
                        'value' => old('title', $gallery->title), 'colClass' => 'col-md-8',
                    ])
                    @include('landing-admin._components.text-input', [
                        'name' => 'album', 'label' => 'Album',
                        'placeholder' => 'mis. Upacara, Class Meeting',
                        'value' => old('album', $gallery->album), 'colClass' => 'col-md-4',
                    ])
                    @include('landing-admin._components.textarea-input', [
                        'name' => 'description', 'label' => 'Deskripsi',
                        'value' => old('description', $gallery->description), 'rows' => 2,
                    ])
                    @php
                        $isEditGallery = ($gallery->exists ?? false);
                    @endphp
                    <div class="col-md-6">
                        <label class="form-label small fw-bold d-block">Foto {{ $isEditGallery ? '(opsional ganti)' : '' }} @if(!$isEditGallery)<span class="text-danger">*</span>@endif</label>
                        <label for="imageInput" class="lp-preview-box d-block" id="imagePreviewBox">
                            @if ($gallery->image)
                                <img src="{{ Storage::disk('public')->url('landing/'.$gallery->image) }}" alt="Foto" id="imagePreviewImg">
                            @else
                                <span class="material-symbols-rounded lp-preview-empty" id="imagePreviewEmpty">add_photo_alternate</span>
                            @endif
                            <span class="lp-preview-hint">Klik untuk pilih foto</span>
                        </label>
                        <input type="file" name="image" class="d-none" accept="image/*"
                               id="imageInput" {{ $isEditGallery ? '' : 'required' }}>
                        @if ($gallery->image)
                            <div class="small text-muted mt-1 text-center">File saat ini: <code>{{ $gallery->image }}</code></div>
                        @endif
                    </div>
                    @include('landing-admin._components.text-input', [
                        'name' => 'sort_order', 'label' => 'Urutan', 'type' => 'number',
                        'value' => old('sort_order', $gallery->sort_order), 'min' => 0,
                        'colClass' => 'col-md-3',
                    ])
                    @include('landing-admin._components.switch-input', [
                        'name' => 'is_published', 'label' => 'Publish',
                        'checkedDefault' => $gallery->is_published ?? true,
                        'inputId' => 'is_pub_gallery', 'colClass' => 'col-md-3',
                    ])
                </div>
            </div>
        </div>

        <div class="card my-3 shadow-sm">
            <div class="card-body d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-2 p-2 pb-1">
                <span class="fw-bold" style="font-size: 14px;">
                    Isi semua kolom bertanda <span class="text-danger">*</span>.
                </span>
                <button type="submit" class="btn btn-info w-100 w-md-auto mb-1">
                    <span class="material-symbols-rounded align-middle" style="font-size:18px;">save</span>
                    Simpan
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@include('landing-admin._scripts')
