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
        $addBtn = '<a href="'.e(route('app.admin-landing.struktur.create')).'" class="btn btn-sm btn-primary">'
            .'<span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle;">add</span> Tambah</a>';
        $titleSlot = '<h5 class="lp-page-title mb-1">'.e($title).'</h5>'
            .'<p class="text-muted small mb-0">Kelola struktur organisasi yang tampil di halaman Profil. Centang "Pimpinan" untuk tampil di baris atas (Kepala Sekolah).</p>';
    @endphp
    @include('admin-landing._header-halaman', [
        'subtitle' => 'Landing Page',
        'back' => route('app.admin-landing.index'),
        'actions' => $addBtn,
        'titleSlot' => $titleSlot,
    ])

    <div class="card mb-4">
        <div class="card-body p-3">
            @if ($items->isEmpty())
                <div class="lp-empty">
                    <span class="material-symbols-rounded">groups</span>
                    <div class="mt-2">Belum ada data struktur.</div>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th style="width:60px">Foto</th>
                                <th>Nama</th>
                                <th>Jabatan</th>
                                <th style="width:100px">Tipe</th>
                                <th style="width:80px">Urutan</th>
                                <th style="width:120px">Status</th>
                                <th style="width:150px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $row)
                                <tr>
                                    <td>
                                        @if ($row->photo)
                                            <img src="{{ Storage::disk('public')->url('landing/' . $row->photo) }}"
                                                 class="lp-thumb" style="width:42px;height:42px;border-radius:50%;object-fit:cover;" alt="">
                                        @else
                                            <span class="lp-thumb-empty material-symbols-rounded" style="width:42px;height:42px;border-radius:50%;">person</span>
                                        @endif
                                    </td>
                                    <td class="fw-semibold">{{ $row->name }}</td>
                                    <td>{{ $row->role }}</td>
                                    <td>
                                        @if ($row->is_lead)
                                            <span class="badge bg-primary">Pimpinan</span>
                                        @else
                                            <span class="badge bg-light text-dark">Anggota</span>
                                        @endif
                                    </td>
                                    <td>{{ $row->sort_order }}</td>
                                    <td>
                                        <span class="badge {{ $row->is_published ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $row->is_published ? 'Published' : 'Draft' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('app.admin-landing.struktur.edit', $row->id) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                <span class="material-symbols-rounded" style="font-size:16px;">edit</span>
                                            </a>
                                            @include('admin-landing._komponen.formulir-hapus', [
                                                'action' => route('app.admin-landing.struktur.destroy', $row->id),
                                                'confirm' => 'Hapus anggota struktur ini?',
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

@include('admin-landing._skrip')
