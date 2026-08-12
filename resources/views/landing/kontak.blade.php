@extends('landing.tata-letak')

@section('title', 'Kontak — ' . $setting->school_name)

@section('style')
<style>
    .lp-form-control {
        background: #ffffff;
        border: 1.5px solid rgba(15, 23, 42, 0.08);
        border-radius: var(--lp-radius-sm);
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.2s ease;
        width: 100%;
    }
    .lp-form-control:focus {
        outline: none;
        border-color: var(--lp-primary);
        background: #fff;
        box-shadow: 0 0 0 4px rgba(var(--lp-primary-rgb), 0.1);
    }
    .lp-form-label {
        font-weight: 500;
        font-size: 0.9rem;
        color: #334155;
        margin-bottom: 0.4rem;
        display: block;
    }
    .lp-info-row {
        display: flex;
        align-items: flex-start;
        gap: 0.85rem;
        padding: 0.85rem 0;
    }
    .lp-info-row + .lp-info-row {
        border-top: 1px solid rgba(15, 23, 42, 0.06);
    }
    .lp-info-icon {
        width: 38px;
        height: 38px;
        border-radius: var(--lp-radius-sm);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(var(--lp-primary-rgb), 0.15), rgba(var(--lp-primary-rgb), 0.3));
        color: var(--lp-primary);
        flex-shrink: 0;
        font-size: 1rem;
    }
    .lp-info-label { font-size: 0.78rem; color: var(--lp-muted); font-weight: 500; margin-bottom: 0.1rem; }
    .lp-info-value { color: var(--lp-text); font-weight: 500; font-size: 0.95rem; }
    .lp-map-wrap {
        border-radius: var(--lp-radius-xl);
        overflow: hidden;
        box-shadow: 0 20px 48px -16px rgba(15, 23, 42, 0.15);
        margin-top: 3rem;
    }
</style>
@endsection

@section('content')
<section class="lp-section">
    <div class="container">
        <div class="text-center lp-section-head lp-reveal" data-from="zoom">
            <span class="lp-section-eyebrow">Hubungi Kami</span>
            <h2 class="lp-section-title">Kontak</h2>
            <p class="lp-section-sub">Kami senang mendengar dari Anda. Kirim pesan atau datang langsung ke sekolah.</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-5">
                <div class="lp-glass lp-card lp-reveal h-100" data-from="left">
                    <h5 class="fw-bold mb-3">{{ $setting->school_name }}</h5>

                    @if ($setting->address)
                        <div class="lp-info-row">
                            <div class="lp-info-icon"><i class="bi bi-geo-alt-fill"></i></div>
                            <div>
                                <div class="lp-info-label">Alamat</div>
                                <div class="lp-info-value">{{ $setting->address }}</div>
                            </div>
                        </div>
                    @endif
                    @if ($setting->phone)
                        <div class="lp-info-row">
                            <div class="lp-info-icon"><i class="bi bi-telephone-fill"></i></div>
                            <div>
                                <div class="lp-info-label">Telepon</div>
                                <div class="lp-info-value">{{ $setting->phone }}</div>
                            </div>
                        </div>
                    @endif
                    @if ($setting->whatsapp)
                        <div class="lp-info-row">
                            <div class="lp-info-icon" style="background: linear-gradient(135deg, #d1fae5, #a7f3d0); color:#059669;">
                                <i class="bi bi-whatsapp"></i>
                            </div>
                            <div>
                                <div class="lp-info-label">WhatsApp</div>
                                <div class="lp-info-value">{{ $setting->whatsapp }}</div>
                            </div>
                        </div>
                    @endif
                    @if ($setting->email)
                        <div class="lp-info-row">
                            <div class="lp-info-icon"><i class="bi bi-envelope-fill"></i></div>
                            <div>
                                <div class="lp-info-label">Surel</div>
                                <div class="lp-info-value">{{ $setting->email }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-7">
                <div class="lp-glass lp-card-lg lp-reveal" data-from="right">
                    <h5 class="fw-bold mb-3">Kirim Pesan</h5>

                    @if (session('success'))
                        <div class="alert alert-success" style="border-radius:12px;">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger" style="border-radius:12px;">
                            <ul class="mb-0 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('landing.kontak.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="lp-form-label">Nama <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="lp-form-control"
                                       value="{{ old('name') }}" required maxlength="100">
                            </div>
                            <div class="col-md-6">
                                <label class="lp-form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="lp-form-control"
                                       value="{{ old('email') }}" required maxlength="150">
                            </div>
                            <div class="col-12">
                                <label class="lp-form-label">Subjek</label>
                                <input type="text" name="subject" class="lp-form-control"
                                       value="{{ old('subject') }}" maxlength="200">
                            </div>
                            <div class="col-12">
                                <label class="lp-form-label">Pesan <span class="text-danger">*</span></label>
                                <textarea name="message" class="lp-form-control" rows="5"
                                          required maxlength="5000">{{ old('message') }}</textarea>
                            </div>
                        </div>

                        <button type="submit" class="lp-cta mt-4" style="border:0;">
                            <i class="bi bi-send me-1"></i> Kirim Pesan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        @if ($setting->google_maps_url)
            <div class="lp-map-wrap lp-reveal" data-from="zoom">
                <div class="ratio ratio-21x9">
                    <iframe src="{{ $setting->google_maps_url }}" loading="lazy"
                            style="border:0" allowfullscreen></iframe>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
