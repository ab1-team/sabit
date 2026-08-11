@extends('layouts.tenant.base')

@section('content')
<div class="container-fluid px-4 py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Hero Slider</h4>
            <p class="text-muted mb-0">Gambar utama di bagian atas halaman depan website.</p>
        </div>
        <a href="{{ route('app.landing.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Tambah Slide</h5>

            <form action="{{ route('app.landing.hero.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Judul</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Subjudul</label>
                        <input type="text" name="subtitle" class="form-control" value="{{ old('subtitle') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Gambar <span class="text-danger">*</span></label>
                        <input type="file" name="image" class="form-control" accept="image/*" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Teks Tombol</label>
                        <input type="text" name="button_text" class="form-control" value="{{ old('button_text') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">URL Tombol</label>
                        <input type="text" name="button_url" class="form-control" value="{{ old('button_url') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Urutan</label>
                        <input type="number" name="sort_order" class="form-control" min="0" value="{{ old('sort_order') }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active_new" checked>
                            <label class="form-check-label" for="is_active_new">Aktif</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Tambah Slide</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-3">Daftar Slide</h5>

            @if ($slides->isEmpty())
                <p class="text-muted mb-0">Belum ada slide.</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th style="width:120px">Gambar</th>
                                <th>Judul</th>
                                <th style="width:90px">Urutan</th>
                                <th style="width:90px">Status</th>
                                <th style="width:220px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($slides as $slide)
                                <tr>
                                    <td>
                                        @if ($slide->image)
                                            <img src="{{ Storage::disk('public')->url('landing/' . $slide->image) }}"
                                                 alt="" class="img-fluid rounded" style="max-height:60px">
                                        @else
                                            <span class="text-muted small">Belum ada gambar</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $slide->title ?: '—' }}</div>
                                        <div class="text-muted small">{{ $slide->subtitle }}</div>
                                    </td>
                                    <td>{{ $slide->sort_order }}</td>
                                    <td>
                                        <span class="badge bg-{{ $slide->is_active ? 'success' : 'secondary' }}">
                                            {{ $slide->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <form action="{{ route('app.landing.hero.update', $slide->id) }}"
                                              method="POST" enctype="multipart/form-data" class="d-flex gap-2 align-items-center">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="title" value="{{ $slide->title }}">
                                            <input type="hidden" name="subtitle" value="{{ $slide->subtitle }}">
                                            <input type="hidden" name="button_text" value="{{ $slide->button_text }}">
                                            <input type="hidden" name="button_url" value="{{ $slide->button_url }}">
                                            <input type="number" name="sort_order" class="form-control form-control-sm"
                                                   style="width:80px" value="{{ $slide->sort_order }}">
                                            <input type="hidden" name="is_active" value="0">
                                            <div class="form-check">
                                                <input type="checkbox" name="is_active" value="1"
                                                       class="form-check-input" {{ $slide->is_active ? 'checked' : '' }}>
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-outline-primary">Simpan</button>
                                        </form>

                                        <form action="{{ route('app.landing.hero.destroy', $slide->id) }}"
                                              method="POST" class="mt-2"
                                              onsubmit="return confirm('Hapus slide ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                        </form>
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
