@extends('layouts.tenant.base')

@section('style')
    @include('admin-landing._gaya')
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
        $titleSlot = '<h5 class="lp-page-title mb-1">'.e($title).'</h5>'
            .'<p class="text-muted small mb-0">Icon Bootstrap, mis. <code>bi-easel</code>, <code>bi-cpu</code>. Lihat referensi di <a href="https://icons.getbootstrap.com" target="_blank">icons.getbootstrap.com</a>.</p>';
    @endphp
    @include('admin-landing._header-halaman', [
        'subtitle' => 'Landing Page',
        'back' => route('app.admin-landing.fasilitas'),
        'titleSlot' => $titleSlot,
    ])

    <form action="{{ $action }}" method="POST" class="lp-ajax">
        @csrf
        @if (($item->exists ?? false))
            @method('PUT')
        @endif

        <div class="card my-3 shadow-sm">
            <div class="card-body p-3">
                <div class="row">
                    @include('admin-landing._komponen.input-teks', [
                        'name' => 'title', 'label' => 'Judul Fasilitas', 'required' => true,
                        'value' => old('title', $item->title), 'colClass' => 'col-md-6',
                    ])
                    @include('admin-landing._komponen.input-teks', [
                        'name' => 'icon', 'label' => 'Icon (class Bootstrap)',
                        'placeholder' => 'bi-easel',
                        'value' => old('icon', $item->icon), 'colClass' => 'col-md-3',
                    ])
                    @include('admin-landing._komponen.input-teks', [
                        'name' => 'sort_order', 'label' => 'Urutan', 'type' => 'number',
                        'value' => old('sort_order', $item->sort_order), 'min' => 0,
                        'colClass' => 'col-md-3',
                    ])
                    @include('admin-landing._komponen.input-teksarea', [
                        'name' => 'description', 'label' => 'Deskripsi',
                        'value' => old('description', $item->description), 'rows' => 3,
                    ])
                    @include('admin-landing._komponen.input-saklar', [
                        'name' => 'is_published', 'label' => 'Publish (tampilkan di halaman publik)',
                        'checkedDefault' => $item->is_published ?? true,
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

@include('admin-landing._skrip')
