@extends('layouts.tenant.base')

@section('style')
    @include('admin-landing._gaya')
    <style>
        .lp-post-toolbar .lp-search {
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
        .lp-post-toolbar .lp-search:focus-within {
            border-color: #1d4ed8;
            box-shadow: 0 0 0 3px rgba(29,78,216,.12);
        }
        .lp-post-toolbar .lp-search .material-symbols-rounded {
            color: #64748b;
            font-size: 20px;
            margin-right: .5rem;
        }
        .lp-post-toolbar .lp-search input {
            border: 0;
            outline: 0;
            background: transparent;
            flex: 1;
            height: 100%;
            font-size: .92rem;
            color: #1f2937;
            padding: 0;
        }
        .lp-post-toolbar .lp-search input::placeholder { color: #94a3b8; }
        .lp-post-toolbar .lp-search-clear {
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
        .lp-post-toolbar .lp-search-clear:hover { color: #1f2937; background: #f1f5f9; }
        .lp-post-toolbar .lp-search-clear .material-symbols-rounded { font-size: 18px; margin: 0; }

        .lp-post-toolbar .form-select {
            height: 44px;
            border-radius: .55rem;
            border-color: #d4d8dd;
            box-shadow: none !important;
            font-size: .92rem;
        }
        .lp-post-toolbar .form-select:focus {
            border-color: #1d4ed8;
            box-shadow: 0 0 0 3px rgba(29,78,216,.12) !important;
        }
        .lp-post-toolbar .select2-container .select2-selection {
            height: 44px !important;
            border-radius: .55rem !important;
            border-color: #d4d8dd !important;
            padding: 0 .65rem !important;
            display: flex !important;
            align-items: center !important;
        }
        .lp-post-toolbar .select2-container .select2-selection__rendered {
            line-height: 44px !important;
            padding-left: 0 !important;
            font-size: .92rem;
            color: #1f2937;
        }
        .lp-post-toolbar .select2-container .select2-selection__arrow {
            height: 42px !important;
        }
        .lp-post-toolbar .select2-container--bootstrap-5.select2-container--focus .select2-selection,
        .lp-post-toolbar .select2-container--bootstrap-5:focus-within .select2-selection {
            border-color: #1d4ed8 !important;
            box-shadow: 0 0 0 3px rgba(29,78,216,.12) !important;
        }

        .lp-post-table th,
        .lp-post-table td { vertical-align: middle; }
        .lp-post-table thead th {
            font-size: .72rem;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 600;
        }
        .lp-post-title {
            font-weight: 600;
            color: #1f2937;
            line-height: 1.3;
        }
        .lp-post-title-excerpt {
            font-size: .78rem;
            color: #64748b;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-top: .2rem;
        }
        .lp-post-slug {
            font-size: .7rem;
            color: #94a3b8;
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
            margin-top: .15rem;
        }
        .lp-post-meta {
            font-size: .78rem;
            color: #475569;
        }
        .lp-cat-chip {
            display: inline-flex;
            align-items: center;
            gap: .25rem;
            padding: .2rem .55rem;
            background: #eff6ff;
            color: #1d4ed8;
            border-radius: 50rem;
            font-size: .72rem;
            font-weight: 600;
        }
        .lp-action-group {
            display: inline-flex;
            gap: .35rem;
            justify-content: flex-end;
        }
        .lp-action-btn {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            border-radius: .4rem;
        }
        .lp-action-btn .material-symbols-rounded {
            font-size: 17px;
            line-height: 1;
        }
        .lp-featured-star {
            color: #f59e0b;
            font-size: 18px;
            vertical-align: middle;
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
            .'<p class="text-muted small mb-0">Kelola program & berita yang tampil di halaman publik.</p>';

        $addBtn = '<a href="'.e(route('app.admin-landing.posts.create')).'" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1">'
            .'<span class="material-symbols-rounded align-middle" style="font-size:16px;">add</span>'
            .'<span class="align-middle">Tambah</span></a>';
    @endphp
    @include('admin-landing._header-halaman', [
        'subtitle' => 'Landing Page',
        'actions' => $addBtn,
        'titleSlot' => $titleSlot,
    ])

    <form method="GET" action="{{ route('app.admin-landing.posts') }}" id="lpPostFilter" class="card mb-3 lp-post-toolbar">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <div class="lp-search">
                        <span class="material-symbols-rounded">search</span>
                        <input type="text" name="q" id="lpPostSearch" value="{{ e($q) }}"
                               placeholder="Cari judul, kategori, tag, atau ringkasan..." autocomplete="off">
                        @if ($q !== '')
                            <button type="button" class="lp-search-clear" id="lpPostSearchClear" title="Bersihkan pencarian">
                                <span class="material-symbols-rounded">close</span>
                            </button>
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="category" id="lpPostCategory" class="form-select">
                        <option value="">Semua kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ e($cat) }}" {{ $category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" id="lpPostStatus" class="form-select">
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
            @if ($posts->isEmpty())
                <div class="lp-empty">
                    <span class="material-symbols-rounded">article</span>
                    <div class="mt-2">
                        @if ($q !== '' || $status !== 'all' || $category !== '')
                            Tidak ada artikel yang cocok dengan filter saat ini.
                        @else
                            Belum ada artikel. Tambahkan artikel pertama Anda.
                        @endif
                    </div>
                    <a href="{{ route('app.admin-landing.posts.create') }}" class="btn btn-sm btn-primary mt-3">
                        <span class="material-symbols-rounded align-middle" style="font-size:16px;">add</span>
                        <span class="align-middle">Tambah Artikel</span>
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle lp-post-table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:80px">Gambar</th>
                                <th>Judul</th>
                                <th style="width:140px">Kategori</th>
                                <th style="width:130px">Tanggal</th>
                                <th style="width:150px">Status</th>
                                <th style="width:90px" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($posts as $post)
                                <tr>
                                    <td>
                                        @if ($post->image)
                                            <img src="{{ Storage::disk('public')->url('landing/' . $post->image) }}"
                                                 class="lp-thumb" alt="">
                                        @else
                                            <span class="lp-thumb-empty material-symbols-rounded">image</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="lp-post-title">
                                            @if ($post->is_featured)
                                                <span class="material-symbols-rounded lp-featured-star" title="Ditampilkan di beranda">star</span>
                                            @endif
                                            {{ $post->title }}
                                        </div>
                                        @if ($post->excerpt)
                                            <div class="lp-post-title-excerpt">{{ \Illuminate\Support\Str::limit(strip_tags($post->excerpt), 100) }}</div>
                                        @endif
                                        <div class="lp-post-slug">/{{ $post->slug }}</div>
                                    </td>
                                    <td class="lp-post-meta">
                                        @if ($post->category)
                                            <span class="lp-cat-chip">{{ $post->category }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="lp-post-meta">{{ $post->published_at?->format('d M Y') ?: '—' }}</td>
                                    <td>
                                        @if ($post->is_published)
                                            <span class="lp-status-badge is-published">Dipublikasikan</span>
                                        @else
                                            <span class="lp-status-badge is-draft">Draft</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="lp-action-group">
                                            <a href="{{ route('app.admin-landing.posts.edit', $post->id) }}"
                                               class="btn btn-sm btn-outline-primary lp-action-btn" title="Edit artikel">
                                                <span class="material-symbols-rounded">edit</span>
                                            </a>
                                            @include('admin-landing._komponen.formulir-hapus', [
                                                'action' => route('app.admin-landing.posts.destroy', $post->id),
                                                'confirm' => 'Hapus artikel "' . $post->title . '"?',
                                                'iconOnly' => true,
                                                'btnClass' => 'btn btn-sm btn-outline-danger lp-action-btn',
                                            ])
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $posts->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection

@include('admin-landing._skrip')
