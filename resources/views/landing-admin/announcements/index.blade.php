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
        $addBtn = '<a href="'.e(route('app.landing.announcements.create')).'" class="btn btn-sm btn-primary">'
            .'<span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle;">add</span> Tambah</a>';
        $titleSlot = '<h5 class="lp-page-title mb-1">'.e($title).'</h5>'
            .'<p class="text-muted small mb-0">Pengumuman tampil di halaman publik dan dashboard.</p>';
    @endphp
    @include('landing-admin._page-header', [
        'subtitle' => 'Landing Page',
        'back' => route('app.landing.index'),
        'actions' => $addBtn,
        'titleSlot' => $titleSlot,
    ])

    <div class="card mb-4">
        <div class="card-body p-3">
            @if ($announcements->isEmpty())
                <div class="lp-empty">
                    <span class="material-symbols-rounded">campaign</span>
                    <div class="mt-2">Belum ada pengumuman.</div>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th style="width:140px">Tanggal</th>
                                <th style="width:180px">Lampiran</th>
                                <th style="width:110px">Status</th>
                                <th style="width:170px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($announcements as $item)
                                <tr>
                                    <td class="fw-semibold">{{ $item->title }}</td>
                                    <td>{{ $item->published_at?->format('d M Y') ?: '—' }}</td>
                                    <td>
                                        @if ($item->file)
                                            <span class="material-symbols-rounded align-middle" style="font-size:16px;">attach_file</span>
                                            {{ $item->file }}
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @include('landing-admin.announcements._status', ['item' => $item])
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('app.landing.announcements.edit', $item->id) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                <span class="material-symbols-rounded" style="font-size:16px;">edit</span>
                                            </a>
                                            @include('landing-admin._components.delete-form', [
                                                'action' => route('app.landing.announcements.destroy', $item->id),
                                                'confirm' => 'Hapus pengumuman ini?',
                                                'iconOnly' => true,
                                            ])
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $announcements->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection

@include('landing-admin._scripts')
