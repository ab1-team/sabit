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
            .'<p class="text-muted small mb-0">Tahapan alur pendaftaran PPDB yang tampil di section "Alur Pendaftaran".</p>';
        $addBtn = '<button type="button" class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#lpNewStage">'
            .'<span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle;">add</span> Tambah</button>';
    @endphp
    @include('admin-landing._header-halaman', [
        'subtitle' => 'Landing Page',
        'back' => route('app.admin-landing.ppdb-cta'),
        'actions' => $addBtn,
        'titleSlot' => $titleSlot,
    ])

    <div id="lpNewStage" class="collapse mb-3">
        @include('admin-landing._komponen._formulir-sebaris', [
            'action' => route('app.admin-landing.ppdb.stages.store'),
            'method' => 'POST',
            'fields' => [
                'step_label' => ['label' => 'Label Tahap (mis. Step 1)', 'required' => true, 'col' => 4],
                'title' => ['label' => 'Judul', 'required' => true, 'col' => 8],
                'description' => ['label' => 'Deskripsi', 'type' => 'textarea', 'col' => 12],
                'sort_order' => ['label' => 'Urutan', 'type' => 'number', 'col' => 6],
                'is_published' => ['label' => 'Publish', 'type' => 'checkbox', 'col' => 6],
            ],
            'item' => (object) ['step_label' => '', 'title' => '', 'description' => '', 'sort_order' => $items->count() + 1, 'is_published' => true],
        ])
    </div>

    <div class="card mb-4">
        <div class="card-body p-3">
            @if ($items->isEmpty())
                <div class="lp-empty">
                    <span class="material-symbols-rounded">timeline</span>
                    <div class="mt-2">Belum ada tahapan.</div>
                </div>
            @else
                <div class="accordion" id="lpStagesAccordion">
                @foreach ($items as $row)
                    <div class="accordion-item mb-2 border rounded">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#stage-{{ $row->id }}">
                                <span class="badge {{ $row->is_published ? 'bg-success' : 'bg-secondary' }} me-2">{{ $row->is_published ? 'Published' : 'Draft' }}</span>
                                <span class="badge bg-primary me-2">{{ $row->step_label }}</span>
                                <span class="fw-semibold">{{ $row->title }}</span>
                                <span class="text-muted small ms-2">(urutan: {{ $row->sort_order ?: 0 }})</span>
                            </button>
                        </h2>
                        <div id="stage-{{ $row->id }}" class="accordion-collapse collapse" data-bs-parent="#lpStagesAccordion">
                            <div class="accordion-body">
                                <p class="small text-muted mb-3">{{ $row->description ?: '(tanpa deskripsi)' }}</p>
                                @include('admin-landing._komponen._formulir-sebaris', [
                                    'action' => route('app.admin-landing.ppdb.stages.update', $row->id),
                                    'method' => 'PUT',
                                    'fields' => [
                                        'step_label' => ['label' => 'Label Tahap', 'required' => true, 'col' => 4],
                                        'title' => ['label' => 'Judul', 'required' => true, 'col' => 8],
                                        'description' => ['label' => 'Deskripsi', 'type' => 'textarea', 'col' => 12],
                                        'sort_order' => ['label' => 'Urutan', 'type' => 'number', 'col' => 4],
                                        'is_published' => ['label' => 'Publish', 'type' => 'checkbox', 'col' => 4],
                                    ],
                                    'item' => $row,
                                ])
                                <div class="d-flex justify-content-end">
                                    @include('admin-landing._komponen.formulir-hapus', [
                                        'action' => route('app.admin-landing.ppdb.stages.destroy', $row->id),
                                        'confirm' => 'Hapus tahapan ini?',
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
