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
        $addBtn = '<a href="'.e(route('app.landing.videos.create')).'" class="btn btn-sm btn-primary">'
            .'<span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle;">add</span> Tambah</a>';
        $titleSlot = '<h5 class="lp-page-title mb-1">'.e($title).'</h5>'
            .'<p class="text-muted small mb-0">Daftar video YouTube yang ditampilkan di halaman publik.</p>';
    @endphp
    @include('landing-admin._page-header', [
        'subtitle' => 'Landing Page',
        'back' => route('app.landing.index'),
        'actions' => $addBtn,
        'titleSlot' => $titleSlot,
    ])

    <div class="card mb-4">
        <div class="card-body p-3">
            @if ($videos->isEmpty())
                <div class="lp-empty">
                    <span class="material-symbols-rounded">play_circle</span>
                    <div class="mt-2">Belum ada video.</div>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>URL YouTube</th>
                                <th style="width:110px">Status</th>
                                <th style="width:170px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($videos as $v)
                                <tr>
                                    <td class="fw-semibold">{{ $v->title }}</td>
                                    <td>
                                        <a href="{{ $v->youtube_url }}" target="_blank" rel="noopener"
                                           class="text-truncate d-inline-block" style="max-width:340px">
                                            {{ $v->youtube_url }}
                                        </a>
                                    </td>
                                    <td>
                                        @include('landing-admin.videos._status', ['v' => $v])
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('app.landing.videos.edit', $v->id) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                <span class="material-symbols-rounded" style="font-size:16px;">edit</span>
                                            </a>
                                            @include('landing-admin._components.delete-form', [
                                                'action' => route('app.landing.videos.destroy', $v->id),
                                                'confirm' => 'Hapus video ini?',
                                                'iconOnly' => true,
                                            ])
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $videos->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection

@include('landing-admin._scripts')
