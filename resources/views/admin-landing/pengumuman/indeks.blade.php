@extends('layouts.tenant.base')

@section('style')
    @include('admin-landing._gaya')
    <style>
        .lp-ann-row td { vertical-align: middle; }
        /* Samakan ukuran tombol icon aksi (edit & hapus) */
        .lp-ann-row .btn.btn-sm {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: .4rem;
        }
        .lp-ann-row .btn.btn-sm .material-symbols-rounded {
            font-size: 17px;
            line-height: 1;
        }
        .lp-ann-title {
            font-weight: 600;
            color: #1f2937;
            line-height: 1.25;
        }
        .lp-ann-content {
            color: #64748b;
            font-size: .82rem;
            max-width: 380px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .lp-ann-file {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            padding: .2rem .55rem;
            background: #f1f5f9;
            border-radius: .35rem;
            font-size: .75rem;
            color: #475569;
            max-width: 160px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .lp-ann-file .material-symbols-rounded { font-size: 16px; }
        .lp-ann-empty {
            padding: 3rem 1rem;
            text-align: center;
            color: #94a3b8;
        }
        .lp-ann-empty .material-symbols-rounded { font-size: 48px; opacity: .55; }
        .lp-ann-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }
    </style>
@endsection

@section('content')
<div class="px-2 py-2">
    @if (session('success'))
        <div class="alert alert-success py-2 small mb-3">{{ session('success') }}</div>
    @endif

    @php
        $titleSlot = '<h5 class="lp-page-title mb-1">'.e($title).'</h5>'
            .'<p class="text-muted small mb-0">Pengumuman tampil di halaman landing publik dan dashboard.</p>';
        $addBtn = '<a href="'.e(route('app.admin-landing.announcements.create')).'" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1">'
            .'<span class="material-symbols-rounded" style="font-size:16px;">add</span> Tambah Pengumuman</a>';
    @endphp
    @include('admin-landing._header-halaman', [
        'subtitle' => 'Landing Page',
        'actions' => $addBtn,
        'titleSlot' => $titleSlot,
    ])

    <div class="card mb-4">
        <div class="card-body p-3">
            @if ($announcements->isEmpty())
                <div class="lp-ann-empty">
                    <span class="material-symbols-rounded">campaign</span>
                    <div class="mt-2">Belum ada pengumuman.</div>
                    <a href="{{ route('app.admin-landing.announcements.create') }}" class="btn btn-sm btn-primary mt-3 d-inline-flex align-items-center gap-1">
                        <span class="material-symbols-rounded" style="font-size:16px;">add</span>
                        Tambah Pengumuman Pertama
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Pengumuman</th>
                                <th style="width:150px">Tanggal</th>
                                <th style="width:200px">Lampiran</th>
                                <th style="width:110px">Status</th>
                                <th style="width:120px" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($announcements as $item)
                                <tr class="lp-ann-row">
                                    <td>
                                        <div class="lp-ann-title">{{ $item->title }}</div>
                                        @if ($item->content)
                                            <div class="lp-ann-content">{!! \Illuminate\Support\Str::limit(strip_tags($item->content), 140) !!}</div>
                                        @endif
                                    </td>
                                    <td class="text-muted small">{{ $item->published_at?->format('d M Y') ?: '—' }}</td>
                                    <td>
                                        @if ($item->file)
                                            <span class="lp-ann-file" title="{{ $item->file }}">
                                                <span class="material-symbols-rounded">attach_file</span>
                                                <span>{{ $item->file }}</span>
                                            </span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @include('admin-landing.pengumuman._status', ['item' => $item])
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <a href="{{ route('app.admin-landing.announcements.edit', $item->id) }}"
                                               class="btn btn-sm btn-outline-primary" title="Edit">
                                                <span class="material-symbols-rounded" style="font-size:16px;">edit</span>
                                            </a>
                                            @include('admin-landing._komponen.formulir-hapus', [
                                                'action' => route('app.admin-landing.announcements.destroy', $item->id),
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

@include('admin-landing._skrip')
