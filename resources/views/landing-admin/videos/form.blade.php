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
        'back' => route('app.landing.videos'),
        'titleSlot' => $titleSlot,
    ])

    <form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="lp-ajax">
        @csrf
        @if (($video->exists ?? false))
            @method('PUT')
        @endif

        <div class="card my-3 shadow-sm">
            <div class="card-body p-3">
                <div class="row">
                    @include('landing-admin._components.text-input', [
                        'name' => 'title', 'label' => 'Judul', 'required' => true,
                        'value' => old('title', $video->title), 'colClass' => 'col-md-8',
                    ])
                    @include('landing-admin._components.switch-input', [
                        'name' => 'is_published', 'label' => 'Publish',
                        'checkedDefault' => $video->is_published ?? true,
                        'inputId' => 'is_pub_video', 'colClass' => 'col-md-4',
                    ])
                    @include('landing-admin._components.text-input', [
                        'name' => 'youtube_url', 'label' => 'URL YouTube', 'type' => 'url',
                        'required' => true, 'placeholder' => 'https://www.youtube.com/watch?v=...',
                        'value' => old('youtube_url', $video->youtube_url), 'colClass' => 'col-12',
                    ])
                    @include('landing-admin._components.textarea-input', [
                        'name' => 'description', 'label' => 'Deskripsi',
                        'value' => old('description', $video->description), 'rows' => 3,
                    ])
                    @include('landing-admin._components.file-input', [
                        'name' => 'thumbnail', 'label' => 'Thumbnail (opsional)',
                        'current' => $video->thumbnail,
                        'currentUrl' => $video->thumbnail ? Storage::disk('public')->url('landing/'.$video->thumbnail) : null,
                        'emptyIcon' => 'movie', 'colClass' => 'col-md-6',
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
