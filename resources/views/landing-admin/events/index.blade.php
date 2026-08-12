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
        $addBtn = '<a href="'.e(route('app.landing.events.create')).'" class="btn btn-sm btn-primary">'
            .'<span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle;">add</span> Tambah</a>';
        $titleSlot = '<h5 class="lp-page-title mb-1">'.e($title).'</h5>'
            .'<p class="text-muted small mb-0">Kelola acara / agenda yang tampil di section Agenda halaman publik.</p>';
    @endphp
    @include('landing-admin._page-header', [
        'subtitle' => 'Landing Page',
        'back' => route('app.landing.index'),
        'actions' => $addBtn,
        'titleSlot' => $titleSlot,
    ])

    <div class="card mb-4">
        <div class="card-body p-3">
            @if ($events->isEmpty())
                <div class="lp-empty">
                    <span class="material-symbols-rounded">event</span>
                    <div class="mt-2">Belum ada acara.</div>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Judul</th>
                                <th>Lokasi</th>
                                <th style="width:120px">Status</th>
                                <th style="width:160px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($events as $event)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $event->start_date?->format('d M Y') }}</div>
                                        @if ($event->start_time)
                                            <div class="text-muted small">{{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }} WIB</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $event->title }}</div>
                                        @if ($event->end_date)
                                            <div class="text-muted small">s/d {{ $event->end_date->format('d M Y') }}</div>
                                        @endif
                                    </td>
                                    <td>{{ $event->location ?: '—' }}</td>
                                    <td>
                                        <span class="badge {{ $event->is_published ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $event->is_published ? 'Published' : 'Draft' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('app.landing.events.edit', $event->id) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                <span class="material-symbols-rounded" style="font-size:16px;">edit</span>
                                            </a>
                                            @include('landing-admin._components.delete-form', [
                                                'action' => route('app.landing.events.destroy', $event->id),
                                                'confirm' => 'Hapus acara ini?',
                                                'iconOnly' => true,
                                            ])
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $events->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection

@include('landing-admin._scripts')
