@extends('layouts.tenant.base')

@section('style')
    @include('admin-landing._gaya')
    <style>
        .lp-gal-toolbar .lp-search {
            position: relative;
            display: flex;
            align-items: center;
            background: #fff;
            border: 1px solid #d4d8dd;
            border-radius: .55rem;
            height: 44px;
            padding: 0 .65rem;
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        .lp-gal-toolbar .lp-search:focus-within {
            border-color: #1d4ed8;
            box-shadow: 0 0 0 3px rgba(29,78,216,.12);
        }
        .lp-gal-toolbar .lp-search .material-symbols-rounded {
            color: #64748b;
            font-size: 20px;
            margin-right: .5rem;
        }
        .lp-gal-toolbar .lp-search input {
            border: 0;
            outline: 0;
            background: transparent;
            flex: 1;
            height: 100%;
            font-size: .92rem;
            color: #1f2937;
            padding: 0;
        }
        .lp-gal-toolbar .lp-search input::placeholder { color: #94a3b8; }
        .lp-gal-toolbar .lp-search-clear {
            border: 0;
            background: transparent;
            color: #94a3b8;
            cursor: pointer;
            padding: .15rem;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .lp-gal-toolbar .lp-search-clear:hover { color: #1f2937; background: #f1f5f9; }
        .lp-gal-toolbar .lp-search-clear .material-symbols-rounded { font-size: 18px; margin: 0; }

        .lp-gal-toolbar .form-select {
            height: 44px;
            border-radius: .55rem;
            border-color: #d4d8dd;
            box-shadow: none !important;
            font-size: .92rem;
        }
        .lp-gal-toolbar .form-select:focus {
            border-color: #1d4ed8;
            box-shadow: 0 0 0 3px rgba(29,78,216,.12) !important;
        }
        .lp-gal-toolbar .select2-container .select2-selection {
            height: 44px !important;
            border-radius: .55rem !important;
            border-color: #d4d8dd !important;
            padding: 0 .65rem !important;
            display: flex !important;
            align-items: center !important;
        }
        .lp-gal-toolbar .select2-container .select2-selection__rendered {
            line-height: 44px !important;
            padding-left: 0 !important;
            font-size: .92rem;
            color: #1f2937;
        }
        .lp-gal-toolbar .select2-container .select2-selection__arrow {
            height: 42px !important;
        }
        .lp-gal-toolbar .select2-container--bootstrap-5.select2-container--focus .select2-selection,
        .lp-gal-toolbar .select2-container--bootstrap-5:focus-within .select2-selection {
            border-color: #1d4ed8 !important;
            box-shadow: 0 0 0 3px rgba(29,78,216,.12) !important;
        }

        .lp-gallery-thumb {
            width: 72px;
            height: 54px;
            border-radius: .5rem;
            object-fit: cover;
            background: #f1f5f9;
            display: block;
            border: 1px solid #e2e8f0;
        }
        .lp-gallery-thumb-empty {
            width: 72px;
            height: 54px;
            border-radius: .5rem;
            background: #f1f5f9;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            border: 1px solid #e2e8f0;
        }
        .lp-gallery-thumb-empty .material-symbols-rounded { font-size: 22px; }

        .lp-gallery-title {
            font-weight: 600;
            color: #1f2937;
            line-height: 1.25;
        }
        .lp-gallery-title .text-muted {
            font-weight: 400;
            font-size: .78rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .lp-table-actions {
            display: inline-flex;
            gap: .35rem;
            justify-content: flex-end;
        }
        .lp-table-actions .btn-icon {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: .4rem;
        }
        .lp-table-actions .btn-icon .material-symbols-rounded {
            font-size: 17px;
            line-height: 1;
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
            .'<p class="text-muted small mb-0">Kumpulan foto kegiatan yang tampil di halaman publik.</p>';

        $addBtn = '<a href="'.e(route('app.admin-landing.galleries.create')).'" class="btn btn-sm btn-primary">'
            .'<span class="material-symbols-rounded align-middle" style="font-size:16px;">add</span> '
            .'<span class="align-middle">Tambah Foto</span></a>';
    @endphp
    @include('admin-landing._header-halaman', [
        'subtitle' => 'Landing Page',
        'actions' => $addBtn,
        'titleSlot' => $titleSlot,
    ])

    <form method="GET" action="{{ route('app.admin-landing.galleries') }}" id="lpGalleryFilter" class="card mb-3 lp-gal-toolbar">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-9">
                    <div class="lp-search">
                        <span class="material-symbols-rounded">search</span>
                        <input type="text" name="q" id="lpGallerySearch" value="{{ e($q) }}"
                               placeholder="Cari judul, album, atau deskripsi..." autocomplete="off">
                        @if ($q !== '')
                            <button type="button" class="lp-search-clear" id="lpGallerySearchClear" title="Bersihkan pencarian">
                                <span class="material-symbols-rounded">close</span>
                            </button>
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" id="lpGalleryStatus" class="form-select">
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua status</option>
                        <option value="published" {{ $status === 'published' ? 'selected' : '' }}>Dipublikasikan</option>
                        <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>
            </div>
        </div>
    </form>

    <div class="card mb-4">
        <div class="card-body p-3">
            @if ($galleries->isEmpty())
                <div class="lp-empty">
                    <span class="material-symbols-rounded">photo_library</span>
                    <div class="mt-2">
                        @if ($q !== '' || $status !== 'all')
                            Tidak ada foto yang cocok dengan filter saat ini.
                        @else
                            Belum ada foto. Tambahkan foto pertama Anda.
                        @endif
                    </div>
                    <a href="{{ route('app.admin-landing.galleries.create') }}" class="btn btn-sm btn-primary mt-3">
                        <span class="material-symbols-rounded align-middle" style="font-size:16px;">add</span>
                        <span class="align-middle">Tambah Foto</span>
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="small text-muted">
                                <th style="width: 92px;">Foto</th>
                                <th>Judul</th>
                                <th style="width: 18%;">Album</th>
                                <th style="width: 14%;">Status</th>
                                <th style="width: 12%;" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($galleries as $g)
                                <tr>
                                    <td>
                                        @if ($g->image)
                                            <img src="{{ Storage::disk('public')->url('landing/' . $g->image) }}"
                                                 alt="" class="lp-gallery-thumb">
                                        @else
                                            <span class="lp-gallery-thumb-empty">
                                                <span class="material-symbols-rounded">image</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="lp-gallery-title">{{ $g->title }}</div>
                                        @if ($g->description)
                                            <div class="text-muted small mt-1">{{ \Illuminate\Support\Str::limit(strip_tags($g->description), 90) }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($g->album)
                                            <span class="badge text-bg-light border">{{ $g->album }}</span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($g->is_published)
                                            <span class="lp-status-badge is-published">Dipublikasikan</span>
                                        @else
                                            <span class="lp-status-badge is-draft">Draft</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="lp-table-actions">
                                            <a href="{{ route('app.admin-landing.galleries.edit', $g->id) }}"
                                               class="btn btn-sm btn-outline-primary btn-icon" title="Edit foto">
                                                <span class="material-symbols-rounded">edit</span>
                                            </a>
                                            @include('admin-landing._komponen.formulir-hapus', [
                                                'action' => route('app.admin-landing.galleries.destroy', $g->id),
                                                'confirm' => 'Hapus foto "' . $g->title . '"?',
                                                'iconOnly' => true,
                                                'btnClass' => 'btn btn-sm btn-outline-danger btn-icon',
                                            ])
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $galleries->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection

@include('admin-landing._skrip')
