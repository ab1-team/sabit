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
        $addBtn = '<a href="'.e(route('app.landing.fasilitas.create')).'" class="btn btn-sm btn-primary">'
            .'<span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle;">add</span> Tambah</a>';
        $titleSlot = '<h5 class="lp-page-title mb-1">'.e($title).'</h5>'
            .'<p class="text-muted small mb-0">Kelola daftar fasilitas yang tampil di section Fasilitas halaman Profil.</p>';
    @endphp
    @include('landing-admin._page-header', [
        'subtitle' => 'Landing Page',
        'back' => route('app.landing.index'),
        'actions' => $addBtn,
        'titleSlot' => $titleSlot,
    ])

    <div class="card mb-4">
        <div class="card-body p-3">
            @if ($items->isEmpty())
                <div class="lp-empty">
                    <span class="material-symbols-rounded">apartment</span>
                    <div class="mt-2">Belum ada fasilitas.</div>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th style="width:50px">#</th>
                                <th>Judul</th>
                                <th>Deskripsi</th>
                                <th style="width:90px">Icon</th>
                                <th style="width:80px">Urutan</th>
                                <th style="width:120px">Status</th>
                                <th style="width:150px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $row)
                                <tr>
                                    <td>{{ $row->sort_order }}</td>
                                    <td class="fw-semibold">{{ $row->title }}</td>
                                    <td class="text-muted small">{{ \Illuminate\Support\Str::limit($row->description, 80) }}</td>
                                    <td><code>{{ $row->icon ?: 'bi-building' }}</code></td>
                                    <td>{{ $row->sort_order }}</td>
                                    <td>
                                        <span class="badge {{ $row->is_published ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $row->is_published ? 'Published' : 'Draft' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('app.landing.fasilitas.edit', $row->id) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                <span class="material-symbols-rounded" style="font-size:16px;">edit</span>
                                            </a>
                                            @include('landing-admin._components.delete-form', [
                                                'action' => route('app.landing.fasilitas.destroy', $row->id),
                                                'confirm' => 'Hapus fasilitas ini?',
                                                'iconOnly' => true,
                                            ])
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $items->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection

@include('landing-admin._scripts')
