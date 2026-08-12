@extends('layouts.tenant.base')

@section('style')
    @include('landing-admin._styles')
@endsection

@section('content')
<div class="px-2 py-2">
    @if (session('success'))
        <div class="alert alert-success py-2 small mb-3">{{ session('success') }}</div>
    @endif

    @php
        $addBtn = '<a href="'.e(route('app.landing.galleries.create')).'" class="btn btn-sm btn-primary">'
            .'<span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle;">add</span> Tambah</a>';
        $titleSlot = '<h5 class="lp-page-title mb-1">'.e($title).'</h5>'
            .'<p class="text-muted small mb-0">Foto-foto kegiatan yang tampil di halaman publik.</p>';
    @endphp
    @include('landing-admin._page-header', [
        'subtitle' => 'Landing Page',
        'back' => route('app.landing.index'),
        'actions' => $addBtn,
        'titleSlot' => $titleSlot,
    ])

    <div class="card mb-4">
        <div class="card-body p-3">
            @if ($galleries->isEmpty())
                <div class="lp-empty">
                    <span class="material-symbols-rounded">photo_library</span>
                    <div class="mt-2">Belum ada foto.</div>
                </div>
            @else
                <div class="row g-3">
                    @foreach ($galleries as $g)
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="lp-gallery-card h-100">
                                @if ($g->image)
                                    <img src="{{ Storage::disk('public')->url('landing/' . $g->image) }}" alt="">
                                @endif
                                <div class="p-2">
                                    <div class="fw-semibold small text-truncate">{{ $g->title }}</div>
                                    @if ($g->album)
                                        <div class="text-muted small text-truncate">{{ $g->album }}</div>
                                    @endif
                                    <div class="d-flex gap-1 mt-2">
                                        <a href="{{ route('app.landing.galleries.edit', $g->id) }}" class="btn btn-sm btn-outline-primary flex-fill">
                                            <span class="material-symbols-rounded" style="font-size:16px;">edit</span>
                                        </a>
                                        @include('landing-admin._components.delete-form', [
                                            'action' => route('app.landing.galleries.destroy', $g->id),
                                            'confirm' => 'Hapus foto ini?',
                                            'iconOnly' => true,
                                        ])
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-3">{{ $galleries->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection

@include('landing-admin._scripts')
