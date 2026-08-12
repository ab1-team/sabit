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
        $titleSlot = '<h5 class="lp-page-title mb-1">'.e($title).'</h5>'
            .'<p class="text-muted small mb-0">Section Profil seperti Tinjauan, Sejarah, dan Akreditasi. Sistem otomatis membuat 3 section default (overview, sejarah, akreditasi) saat pertama kali diakses.</p>';
    @endphp
    @include('landing-admin._page-header', [
        'subtitle' => 'Landing Page',
        'back' => route('app.landing.index'),
        'titleSlot' => $titleSlot,
    ])

    <div class="card mb-4">
        <div class="card-body p-3">
            @if ($items->isEmpty())
                <div class="lp-empty">
                    <span class="material-symbols-rounded">article</span>
                    <div class="mt-2">Belum ada section.</div>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Section</th>
                                <th>Judul</th>
                                <th>Badge</th>
                                <th style="width:110px">Status</th>
                                <th style="width:150px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $row)
                                <tr>
                                    <td><code>{{ $row->section_key }}</code></td>
                                    <td class="fw-semibold">{{ $row->title }}</td>
                                    <td class="text-muted small">{{ $row->badge_text ?: '—' }}</td>
                                    <td>
                                        <span class="badge {{ $row->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $row->is_active ? 'Aktif' : 'Non-aktif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('app.landing.profile-sections.edit', $row->id) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <span class="material-symbols-rounded" style="font-size:16px;">edit</span>
                                            Edit
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@include('landing-admin._scripts')
