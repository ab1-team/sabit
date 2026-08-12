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
        $addBtn = '<a href="'.e(route('app.landing.posts.create')).'" class="btn btn-sm btn-primary">'
            .'<span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle;">add</span> Tambah</a>';
        $titleSlot = '<h5 class="lp-page-title mb-1">'.e($title).'</h5>'
            .'<p class="text-muted small mb-0">Kelola artikel / program yang tampil di halaman publik.</p>';
    @endphp
    @include('landing-admin._page-header', [
        'subtitle' => 'Landing Page',
        'back' => route('app.landing.index'),
        'actions' => $addBtn,
        'titleSlot' => $titleSlot,
    ])

    <div class="card mb-4">
        <div class="card-body p-3">
            @if ($posts->isEmpty())
                <div class="lp-empty">
                    <span class="material-symbols-rounded">article</span>
                    <div class="mt-2">Belum ada data.</div>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th style="width:90px">Gambar</th>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th style="width:140px">Tanggal</th>
                                <th style="width:130px">Status</th>
                                <th style="width:170px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($posts as $post)
                                <tr>
                                    <td>
                                        @if ($post->image)
                                            <img src="{{ Storage::disk('public')->url('landing/' . $post->image) }}"
                                                 class="lp-thumb" alt="">
                                        @else
                                            <span class="lp-thumb-empty material-symbols-rounded">image</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $post->title }}</div>
                                        <div class="text-muted small">/{{ $post->slug }}</div>
                                    </td>
                                    <td>{{ $post->category ?: '—' }}</td>
                                    <td>{{ $post->published_at?->format('d M Y') ?: '—' }}</td>
                                    <td>
                                        @include('landing-admin.posts._status', ['post' => $post])
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('app.landing.posts.edit', $post->id) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                <span class="material-symbols-rounded" style="font-size:16px;">edit</span>
                                            </a>
                                            @include('landing-admin._components.delete-form', [
                                                'action' => route('app.landing.posts.destroy', $post->id),
                                                'confirm' => 'Hapus program/berita ini?',
                                                'iconOnly' => true,
                                            ])
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $posts->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection

@include('landing-admin._scripts')
