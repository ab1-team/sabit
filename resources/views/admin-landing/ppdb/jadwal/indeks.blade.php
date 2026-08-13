@extends('layouts.tenant.base')

@section('style')
    @include('admin-landing._gaya')
@endsection

@section('content')
<div class="px-2 py-2">
    @if (session('success'))
        <div class="alert alert-success py-2 small mb-3">{{ session('success') }}</div>
    @endif

    @php
        $titleSlot = '<h5 class="lp-page-title mb-1">'.e($title).'</h5>'
            .'<p class="text-muted small mb-0">Jadwal / gelombang pendaftaran PPDB beserta tanggal & biaya.</p>';
        $addBtn = '<button type="button" class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#lpNewSchedule">'
            .'<span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle;">add</span> Tambah</button>';
    @endphp
    @include('admin-landing._header-halaman', [
        'subtitle' => 'Landing Page',
        'back' => route('app.admin-landing.ppdb-cta'),
        'actions' => $addBtn,
        'titleSlot' => $titleSlot,
    ])

    <div id="lpNewSchedule" class="collapse mb-3">
        @include('admin-landing._komponen._formulir-sebaris', [
            'action' => route('app.admin-landing.ppdb.schedules.store'),
            'method' => 'POST',
            'fields' => [
                'gelombang' => ['label' => 'Gelombang', 'required' => true, 'col' => 12],
                'start_date' => ['label' => 'Tanggal Mulai', 'type' => 'date', 'required' => true, 'col' => 4],
                'end_date' => ['label' => 'Tanggal Selesai', 'type' => 'date', 'col' => 4],
                'biaya_daftar' => ['label' => 'Biaya Pendaftaran', 'col' => 6, 'help' => 'Bebas format teks, misal "Rp 100.000" atau "Gratis".'],
                'spp_bulanan' => ['label' => 'SPP Bulanan', 'col' => 6],
                'sort_order' => ['label' => 'Urutan', 'type' => 'number', 'col' => 4],
                'is_published' => ['label' => 'Publish', 'type' => 'checkbox', 'col' => 4],
            ],
            'item' => (object) ['gelombang' => '', 'start_date' => '', 'end_date' => '', 'biaya_daftar' => '', 'spp_bulanan' => '', 'sort_order' => $items->count() + 1, 'is_published' => true],
        ])
    </div>

    <div class="card mb-4">
        <div class="card-body p-3">
            @if ($items->isEmpty())
                <div class="lp-empty">
                    <span class="material-symbols-rounded">event</span>
                    <div class="mt-2">Belum ada jadwal.</div>
                </div>
            @else
                <div class="accordion" id="lpSchedulesAccordion">
                @foreach ($items as $row)
                    <div class="accordion-item mb-2 border rounded">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#sched-{{ $row->id }}">
                                <span class="badge {{ $row->is_published ? 'bg-success' : 'bg-secondary' }} me-2">{{ $row->is_published ? 'Published' : 'Draft' }}</span>
                                <span class="fw-semibold">{{ $row->gelombang }}</span>
                                <span class="text-muted small ms-2">{{ $row->start_date?->format('d M Y') }} @if ($row->end_date) — {{ $row->end_date->format('d M Y') }} @endif</span>
                            </button>
                        </h2>
                        <div id="sched-{{ $row->id }}" class="accordion-collapse collapse" data-bs-parent="#lpSchedulesAccordion">
                            <div class="accordion-body">
                                @include('admin-landing._komponen._formulir-sebaris', [
                                    'action' => route('app.admin-landing.ppdb.schedules.update', $row->id),
                                    'method' => 'PUT',
                                    'fields' => [
                                        'gelombang' => ['label' => 'Gelombang', 'required' => true, 'col' => 12],
                                        'start_date' => ['label' => 'Tanggal Mulai', 'type' => 'date', 'required' => true, 'col' => 4],
                                        'end_date' => ['label' => 'Tanggal Selesai', 'type' => 'date', 'col' => 4],
                                        'biaya_daftar' => ['label' => 'Biaya Pendaftaran', 'col' => 6],
                                        'spp_bulanan' => ['label' => 'SPP Bulanan', 'col' => 6],
                                        'sort_order' => ['label' => 'Urutan', 'type' => 'number', 'col' => 4],
                                        'is_published' => ['label' => 'Publish', 'type' => 'checkbox', 'col' => 4],
                                    ],
                                    'item' => $row,
                                ])
                                <div class="d-flex justify-content-end">
                                    @include('admin-landing._komponen.formulir-hapus', [
                                        'action' => route('app.admin-landing.ppdb.schedules.destroy', $row->id),
                                        'confirm' => 'Hapus jadwal ini?',
                                        'label' => 'Hapus',
                                    ])
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                </div>
                <div class="mt-3">{{ $items->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection

@include('admin-landing._skrip')
