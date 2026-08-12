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
        $titleSlot = '<h5 class="lp-page-title mb-1">'.e($title).'</h5>'
            .'<p class="text-muted small mb-0">Isi judul, lokasi, dan tanggal acara. Section Agenda hanya menampilkan acara dengan tanggal mulai ≥ hari ini.</p>';
    @endphp
    @include('landing-admin._page-header', [
        'subtitle' => 'Landing Page',
        'back' => route('app.landing.events'),
        'titleSlot' => $titleSlot,
    ])

    <form action="{{ $action }}" method="POST" class="lp-ajax">
        @csrf
        @if (($event->exists ?? false))
            @method('PUT')
        @endif

        <div class="card my-3 shadow-sm">
            <div class="card-body p-3">
                <div class="row">
                    @include('landing-admin._components.text-input', [
                        'name' => 'title', 'label' => 'Judul Acara', 'required' => true,
                        'value' => old('title', $event->title), 'colClass' => 'col-md-8',
                    ])
                    @include('landing-admin._components.text-input', [
                        'name' => 'location', 'label' => 'Lokasi',
                        'value' => old('location', $event->location), 'colClass' => 'col-md-4',
                    ])
                    @include('landing-admin._components.textarea-input', [
                        'name' => 'description', 'label' => 'Deskripsi Singkat',
                        'value' => old('description', $event->description), 'rows' => 3,
                    ])
                    @include('landing-admin._components.text-input', [
                        'name' => 'start_date', 'label' => 'Tanggal Mulai', 'type' => 'date',
                        'required' => true,
                        'value' => old('start_date', $event->start_date?->format('Y-m-d')),
                        'colClass' => 'col-md-4', 'inputClass' => 'lp-date-only',
                    ])
                    @include('landing-admin._components.text-input', [
                        'name' => 'end_date', 'label' => 'Tanggal Selesai', 'type' => 'date',
                        'value' => old('end_date', $event->end_date?->format('Y-m-d')),
                        'colClass' => 'col-md-4', 'inputClass' => 'lp-date-only',
                    ])
                    @include('landing-admin._components.text-input', [
                        'name' => 'start_time', 'label' => 'Jam Mulai', 'type' => 'time',
                        'value' => old('start_time', $event->start_time), 'colClass' => 'col-md-4',
                    ])
                    @include('landing-admin._components.switch-input', [
                        'name' => 'is_published', 'label' => 'Publish (tampilkan di halaman publik)',
                        'checkedDefault' => $event->is_published ?? true,
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
