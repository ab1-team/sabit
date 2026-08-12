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
            .'<p class="text-muted small mb-0">Centang "Pimpinan" untuk tampil di posisi atas struktur (mis. Kepala Sekolah). Upload foto opsional — jika kosong, sistem pakai inisial nama.</p>';
    @endphp
    @include('landing-admin._page-header', [
        'subtitle' => 'Landing Page',
        'back' => route('app.landing.struktur'),
        'titleSlot' => $titleSlot,
    ])

    <form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="lp-ajax">
        @csrf
        @if (($item->exists ?? false))
            @method('PUT')
        @endif

        <div class="card my-3 shadow-sm">
            <div class="card-body p-3">
                <div class="row">
                    @include('landing-admin._components.text-input', [
                        'name' => 'name', 'label' => 'Nama', 'required' => true,
                        'value' => old('name', $item->name), 'colClass' => 'col-md-6',
                    ])
                    @include('landing-admin._components.text-input', [
                        'name' => 'role', 'label' => 'Jabatan', 'required' => true,
                        'placeholder' => 'Kepala Sekolah / Wakil Kurikulum / ...',
                        'value' => old('role', $item->role), 'colClass' => 'col-md-6',
                    ])
                    @include('landing-admin._components.file-input', [
                        'name' => 'photo', 'label' => 'Foto',
                        'current' => $item->photo,
                        'currentUrl' => $item->photo ? Storage::disk('public')->url('landing/'.$item->photo) : null,
                        'emptyIcon' => 'person', 'colClass' => 'col-md-6',
                    ])
                    @include('landing-admin._components.text-input', [
                        'name' => 'sort_order', 'label' => 'Urutan', 'type' => 'number',
                        'value' => old('sort_order', $item->sort_order), 'min' => 0,
                        'colClass' => 'col-md-3',
                    ])
                    @include('landing-admin._components.switch-input', [
                        'name' => 'is_lead', 'label' => 'Pimpinan',
                        'checkedDefault' => $item->is_lead ?? false,
                        'inputId' => 'is_lead_struktur', 'colClass' => 'col-md-3',
                    ])
                    @include('landing-admin._components.switch-input', [
                        'name' => 'is_published', 'label' => 'Publish',
                        'checkedDefault' => $item->is_published ?? true,
                        'inputId' => 'is_pub_struktur', 'colClass' => 'col-md-12',
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
