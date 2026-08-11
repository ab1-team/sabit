@extends('layouts.tenant.base')

@section('content')
<div class="container-fluid px-4 py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Pengaturan Landing Page</h4>
            <p class="text-muted mb-0">Identitas, kontak, dan SEO website sekolah.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('app.landing.index') }}" class="btn btn-outline-secondary">Kembali</a>
            @if ($landingUrl)
                <a href="{{ $landingUrl }}" target="_blank" rel="noopener" class="btn btn-outline-primary">
                    Lihat Website
                </a>
            @endif
        </div>
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

    <form action="{{ route('app.landing.pengaturan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title mb-3">Identitas Sekolah</h5>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Sekolah <span class="text-danger">*</span></label>
                        <input type="text" name="school_name" class="form-control"
                               value="{{ old('school_name', $setting->school_name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tagline</label>
                        <input type="text" name="tagline" class="form-control"
                               value="{{ old('tagline', $setting->tagline) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Logo</label>
                        <input type="file" name="logo" class="form-control" accept="image/*">
                        @if ($setting->logo)
                            <div class="form-text">File saat ini: {{ $setting->logo }}</div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Favicon</label>
                        <input type="file" name="favicon" class="form-control" accept="image/*">
                        @if ($setting->favicon)
                            <div class="form-text">File saat ini: {{ $setting->favicon }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title mb-3">Kontak</h5>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Surel</label>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email', $setting->email) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="phone" class="form-control"
                               value="{{ old('phone', $setting->phone) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">WhatsApp</label>
                        <input type="text" name="whatsapp" class="form-control"
                               value="{{ old('whatsapp', $setting->whatsapp) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Alamat</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address', $setting->address) }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Google Maps Embed URL</label>
                        <textarea name="google_maps_url" class="form-control" rows="2">{{ old('google_maps_url', $setting->google_maps_url) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title mb-3">Media Sosial</h5>

                <div class="row g-3">
                    @foreach (['facebook' => 'Facebook', 'instagram' => 'Instagram', 'youtube' => 'YouTube', 'tiktok' => 'TikTok'] as $field => $label)
                        <div class="col-md-6">
                            <label class="form-label">{{ $label }}</label>
                            <input type="url" name="{{ $field }}" class="form-control"
                                   placeholder="https://..."
                                   value="{{ old($field, $setting->{$field}) }}">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title mb-3">SEO</h5>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Meta Description</label>
                        <input type="text" name="meta_description" class="form-control" maxlength="255"
                               value="{{ old('meta_description', $setting->meta_description) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Meta Keywords</label>
                        <input type="text" name="meta_keywords" class="form-control" maxlength="255"
                               value="{{ old('meta_keywords', $setting->meta_keywords) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
            <a href="{{ route('app.landing.index') }}" class="btn btn-light">Batal</a>
        </div>
    </form>
</div>
@endsection
