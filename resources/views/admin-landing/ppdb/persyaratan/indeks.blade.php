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
            .'<p class="text-muted small mb-0">Kelola daftar persyaratan PPDB per gelombang / jenjang. Setiap entry adalah grup persyaratan dengan daftar item di dalamnya.</p>';
        $addBtn = '<button type="button" class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#lpNewRequirement">'
            .'<span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle;">add</span> Tambah</button>';
    @endphp
    @include('admin-landing._header-halaman', [
        'subtitle' => 'Landing Page',
        'back' => route('app.admin-landing.ppdb-cta'),
        'actions' => $addBtn,
        'titleSlot' => $titleSlot,
    ])

    <div id="lpNewRequirement" class="collapse mb-3">
        @include('admin-landing._komponen._formulir-sebaris', [
            'action' => route('app.admin-landing.ppdb.requirements.store'),
            'method' => 'POST',
            'fields' => [
                'group' => ['label' => 'Grup (opsional: umum / jenjang)', 'col' => 4],
                'title' => ['label' => 'Judul grup persyaratan', 'required' => true, 'col' => 8],
                'items' => ['label' => 'Daftar item (satu per baris)', 'type' => 'textarea', 'col' => 12, 'help' => 'Pisahkan tiap item dengan baris baru. Akan ditampilkan sebagai bullet list di halaman publik.'],
                'sort_order' => ['label' => 'Urutan tampil', 'type' => 'number', 'col' => 6],
                'is_published' => ['label' => 'Tampilkan di publik', 'type' => 'checkbox', 'col' => 6],
            ],
            'item' => (object) ['group' => '', 'title' => '', 'items' => '', 'sort_order' => $items->count() + 1, 'is_published' => true],
        ])
    </div>

    <div class="card mb-4">
        <div class="card-body p-3">
            @if ($items->isEmpty())
                <div class="lp-empty">
                    <span class="material-symbols-rounded">fact_check</span>
                    <div class="mt-2">Belum ada persyaratan.</div>
                </div>
            @else
                <div class="accordion" id="lpRequirementsAccordion">
                @foreach ($items as $row)
                    <div class="accordion-item mb-2 border rounded">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#req-{{ $row->id }}">
                                <span class="badge {{ $row->is_published ? 'bg-success' : 'bg-secondary' }} me-2">{{ $row->is_published ? 'Published' : 'Draft' }}</span>
                                <span class="fw-semibold">{{ $row->title }}</span>
                                @if ($row->group)
                                    <span class="badge bg-light text-dark ms-2">{{ $row->group }}</span>
                                @endif
                                <span class="text-muted small ms-2">(urutan: {{ $row->sort_order ?: 0 }})</span>
                            </button>
                        </h2>
                        <div id="req-{{ $row->id }}" class="accordion-collapse collapse" data-bs-parent="#lpRequirementsAccordion">
                            <div class="accordion-body">
                                @php
                                    $itemsList = $row->items_list ?? [];
                                @endphp
                                @if (!empty($itemsList))
                                    <ul class="small mb-3">
                                        @foreach ($itemsList as $it)
                                            <li>{{ $it }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="small text-muted mb-3">Belum ada item.</div>
                                @endif
                                @include('admin-landing._komponen._formulir-sebaris', [
                                    'action' => route('app.admin-landing.ppdb.requirements.update', $row->id),
                                    'method' => 'PUT',
                                    'fields' => [
                                        'group' => ['label' => 'Grup', 'col' => 4],
                                        'title' => ['label' => 'Judul', 'required' => true, 'col' => 8],
                                        'items' => ['label' => 'Daftar item (satu per baris)', 'type' => 'textarea', 'col' => 12],
                                        'sort_order' => ['label' => 'Urutan', 'type' => 'number', 'col' => 4],
                                        'is_published' => ['label' => 'Publish', 'type' => 'checkbox', 'col' => 4],
                                    ],
                                    'item' => $row,
                                ])
                                <div class="d-flex justify-content-end">
                                    @include('admin-landing._komponen.formulir-hapus', [
                                        'action' => route('app.admin-landing.ppdb.requirements.destroy', $row->id),
                                        'confirm' => 'Hapus grup persyaratan ini?',
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
