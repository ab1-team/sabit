@extends('layouts.tenant.base')

@section('style')
    @include('landing-admin._styles')
@endsection

@section('content')
<div class="px-2 py-2">

    @php
        $titleSlot = '<h5 class="lp-page-title mb-1">'.e($title).'</h5>'
            .'<p class="text-muted small mb-0">Gambar utama di bagian atas halaman depan website.</p>';
    @endphp
    @include('landing-admin._page-header', [
        'subtitle' => 'Landing Page',
        'back' => route('app.landing.index'),
        'titleSlot' => $titleSlot,
    ])

    <div class="card my-3 shadow-sm">
        <div class="card-body p-3">
            <h6 class="lp-section-title mb-3">
                <span class="material-symbols-rounded align-middle">add_photo_alternate</span>
                Tambah Slide
            </h6>

            <form action="{{ route('app.landing.hero.store') }}" method="POST" enctype="multipart/form-data" class="lp-ajax">
                @csrf
                <div class="row">
                    @include('landing-admin._components.text-input', [
                        'name' => 'title', 'label' => 'Judul',
                        'value' => old('title'), 'colClass' => 'col-md-6',
                    ])
                    @include('landing-admin._components.text-input', [
                        'name' => 'subtitle', 'label' => 'Subjudul',
                        'value' => old('subtitle'), 'colClass' => 'col-md-6',
                    ])
                    @include('landing-admin._components.file-input', [
                        'name' => 'image', 'label' => 'Gambar', 'required' => true,
                        'colClass' => 'col-md-6',
                        'extraInfo' => 'JPG, JPEG, PNG, WEBP (maks 4MB).',
                    ])
                    @include('landing-admin._components.text-input', [
                        'name' => 'button_text', 'label' => 'Teks Tombol',
                        'value' => old('button_text'), 'colClass' => 'col-md-3',
                    ])
                    @include('landing-admin._components.text-input', [
                        'name' => 'button_url', 'label' => 'URL Tombol',
                        'value' => old('button_url'), 'colClass' => 'col-md-3',
                    ])
                    @include('landing-admin._components.text-input', [
                        'name' => 'sort_order', 'label' => 'Urutan', 'type' => 'number',
                        'value' => old('sort_order'), 'min' => 0, 'colClass' => 'col-md-6',
                    ])
                    @include('landing-admin._components.switch-input', [
                        'name' => 'is_active', 'label' => 'Aktif',
                        'checkedDefault' => true,
                        'inputId' => 'is_active_new', 'colClass' => 'col-md-6',
                    ])
                </div>

                <div class="card mt-3 mb-0 shadow-none" style="background: transparent; box-shadow: none !important;">
                    <div class="card-body d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-2 p-2 pb-1">
                        <span class="fw-bold" style="font-size: 14px;">
                            Isi semua kolom bertanda <span class="text-danger">*</span>.
                        </span>
                        <button type="submit" class="btn btn-info w-100 w-md-auto mb-1">
                            <span class="material-symbols-rounded align-middle" style="font-size:18px;">add</span>
                            Tambah Slide
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card my-3 shadow-sm">
        <div class="card-body p-3">
            <h6 class="lp-section-title mb-3">
                <span class="material-symbols-rounded align-middle">imagesmode</span>
                Daftar Slide
            </h6>

            @if ($slides->isEmpty())
                <div class="lp-empty">
                    <span class="material-symbols-rounded">image</span>
                    <div class="mt-2">Belum ada slide.</div>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th style="width:120px">Gambar</th>
                                <th>Judul</th>
                                <th>Tombol</th>
                                <th style="width:90px">Urutan</th>
                                <th style="width:110px">Status</th>
                                <th style="width:150px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($slides as $slide)
                                <tr>
                                    <td>
                                        @if ($slide->image)
                                            <img src="{{ Storage::disk('public')->url('landing/' . $slide->image) }}"
                                                 alt="" class="lp-thumb">
                                        @else
                                            <span class="lp-thumb-empty material-symbols-rounded">image</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $slide->title ?: '—' }}</div>
                                        <div class="text-muted small">{{ $slide->subtitle ?: '—' }}</div>
                                    </td>
                                    <td>
                                        @if ($slide->button_text)
                                            <div class="small fw-semibold">{{ $slide->button_text }}</div>
                                            <div class="text-muted small text-truncate" style="max-width:180px;">
                                                {{ $slide->button_url ?: '—' }}
                                            </div>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $slide->sort_order }}</td>
                                    <td>
                                        @include('landing-admin.hero._status', ['slide' => $slide])
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 align-items-center flex-wrap">
                                            <form action="{{ route('app.landing.hero.update', $slide->id) }}"
                                                  method="POST" enctype="multipart/form-data" class="lp-ajax d-flex gap-2 align-items-center">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="title" value="{{ $slide->title }}">
                                                <input type="hidden" name="subtitle" value="{{ $slide->subtitle }}">
                                                <input type="hidden" name="button_text" value="{{ $slide->button_text }}">
                                                <input type="hidden" name="button_url" value="{{ $slide->button_url }}">
                                                <input type="hidden" name="is_active" value="0">
                                                <input type="number" name="sort_order" class="form-control form-control-sm"
                                                       style="width:70px" value="{{ $slide->sort_order }}" title="Ubah urutan">
                                                <div class="form-check form-switch m-0" title="Aktifkan/nonaktifkan">
                                                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                                           {{ $slide->is_active ? 'checked' : '' }}>
                                                </div>
                                                <button type="submit" class="btn btn-sm btn-outline-primary" title="Simpan urutan & status">
                                                    <span class="material-symbols-rounded" style="font-size:16px;">save</span>
                                                </button>
                                            </form>

                                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#heroEditModal{{ $slide->id }}"
                                                    title="Edit detail slide">
                                                <span class="material-symbols-rounded" style="font-size:16px;">edit</span>
                                            </button>

                                            @include('landing-admin._components.delete-form', [
                                                'action' => route('app.landing.hero.destroy', $slide->id),
                                                'confirm' => 'Hapus slide ini?',
                                                'iconOnly' => true,
                                            ])
                                        </div>

                                        <div class="modal fade" id="heroEditModal{{ $slide->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                <div class="modal-content">
                                                    <form action="{{ route('app.landing.hero.update', $slide->id) }}" method="POST" enctype="multipart/form-data" class="lp-ajax">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-header py-2">
                                                            <h6 class="modal-title fw-bold">
                                                                <span class="material-symbols-rounded align-middle">edit</span>
                                                                Edit Slide
                                                            </h6>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body p-3">
                                                            <div class="row">
                                                                @include('landing-admin._components.text-input', [
                                                                    'name' => 'title', 'label' => 'Judul',
                                                                    'value' => $slide->title, 'colClass' => 'col-md-6',
                                                                ])
                                                                @include('landing-admin._components.text-input', [
                                                                    'name' => 'subtitle', 'label' => 'Subjudul',
                                                                    'value' => $slide->subtitle, 'colClass' => 'col-md-6',
                                                                ])
                                                                @include('landing-admin._components.text-input', [
                                                                    'name' => 'button_text', 'label' => 'Teks Tombol',
                                                                    'value' => $slide->button_text, 'colClass' => 'col-md-6',
                                                                ])
                                                                @include('landing-admin._components.text-input', [
                                                                    'name' => 'button_url', 'label' => 'URL Tombol',
                                                                    'value' => $slide->button_url, 'colClass' => 'col-md-6',
                                                                ])
                                                                @include('landing-admin._components.text-input', [
                                                                    'name' => 'sort_order', 'label' => 'Urutan', 'type' => 'number',
                                                                    'value' => $slide->sort_order, 'min' => 0, 'colClass' => 'col-md-6',
                                                                ])
                                                                <input type="hidden" name="is_active" value="{{ $slide->is_active ? 1 : 0 }}">
                                                                <div class="col-md-6 d-flex align-items-end pb-3">
                                                                    <span class="text-muted small">
                                                                        <span class="material-symbols-rounded align-middle" style="font-size:16px;">info</span>
                                                                        Status aktif/nonaktif & gambar diubah dari daftar slide di atas.
                                                                    </span>
                                                                </div>
                                                                @include('landing-admin._components.file-input', [
                                                                    'name' => 'image', 'label' => 'Ganti Gambar (opsional)',
                                                                    'current' => $slide->image,
                                                                    'currentUrl' => $slide->image ? Storage::disk('public')->url('landing/'.$slide->image) : null,
                                                                    'colClass' => 'col-12',
                                                                    'hint' => $slide->image ? 'Klik untuk ganti gambar' : 'Klik untuk pilih gambar',
                                                                ])
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer py-2">
                                                            <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-info btn-sm">
                                                                <span class="material-symbols-rounded align-middle" style="font-size:16px;">save</span>
                                                                Simpan Perubahan
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@include('landing-admin._scripts')
