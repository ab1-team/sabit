@extends('layouts.tenant.base')

@section('style')
    @include('landing-admin._styles')
@endsection

@section('content')
<div class="row">

    {{-- ============ CARD: IDENTITAS SEKOLAH ============ --}}
    <div class="col-12">
        <form id="FormIdentitas" method="POST" action="{{ route('app.landing.pengaturan.store') }}"
              class="text-start lp-card-form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="section" value="identitas">
            <div class="card mt-1 mb-3 shadow-sm">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-3"><span class="material-symbols-rounded align-middle">badge</span> Identitas Sekolah</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group input-group-outline mb-3 @if(old('school_name', $setting->school_name)) is-filled @endif">
                                <label class="form-label">Nama Sekolah <span class="text-danger">*</span></label>
                                <input type="text" name="school_name" class="form-control" required
                                       value="{{ old('school_name', $setting->school_name) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-outline mb-3 @if(old('tagline', $setting->tagline)) is-filled @endif">
                                <label class="form-label">Tagline</label>
                                <input type="text" name="tagline" class="form-control"
                                       value="{{ old('tagline', $setting->tagline) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Logo</label>
                            <label for="logoInput" class="lp-preview-box d-block" id="logoPreviewBox">
                                @if ($setting->logo)
                                    <img src="{{ Storage::disk('public')->url('landing/' . $setting->logo) }}" alt="Logo" id="logoPreviewImg">
                                @else
                                    <span class="material-symbols-rounded lp-preview-empty" id="logoPreviewEmpty">add_photo_alternate</span>
                                @endif
                                <span class="lp-preview-hint">Klik untuk pilih logo</span>
                            </label>
                            <input type="file" name="logo" class="d-none" accept="image/*" id="logoInput">
                            @if ($setting->logo)
                                <div class="small text-muted mt-1 text-center">File: <code>{{ $setting->logo }}</code></div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Favicon</label>
                            <label for="faviconInput" class="lp-preview-box d-block" id="faviconPreviewBox">
                                @if ($setting->favicon)
                                    <img src="{{ Storage::disk('public')->url('landing/' . $setting->favicon) }}" alt="Favicon" id="faviconPreviewImg">
                                @else
                                    <span class="material-symbols-rounded lp-preview-empty" id="faviconPreviewEmpty">add_photo_alternate</span>
                                @endif
                                <span class="lp-preview-hint">Klik untuk pilih favicon</span>
                            </label>
                            <input type="file" name="favicon" class="d-none" accept="image/*" id="faviconInput">
                            @if ($setting->favicon)
                                <div class="small text-muted mt-1 text-center">File: <code>{{ $setting->favicon }}</code></div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-top p-2 d-flex justify-content-end">
                    <button type="submit" class="btn btn-info mb-0 lp-save-btn" data-form="FormIdentitas" id="simpan-identitas">
                        <span class="material-symbols-rounded align-middle" style="font-size:18px;">save</span>
                        <span class="lp-btn-label">Simpan Identitas</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- ============ CARD: KONTAK ============ --}}
    <div class="col-12">
        <form id="FormKontak" method="POST" action="{{ route('app.landing.pengaturan.store') }}"
              class="text-start lp-card-form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="section" value="kontak">
            <div class="card my-4 shadow-sm">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-3"><span class="material-symbols-rounded align-middle">contact_phone</span> Kontak</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="input-group input-group-outline mb-3 @if(old('email', $setting->email)) is-filled @endif">
                                <label class="form-label">Surel</label>
                                <input type="email" name="email" class="form-control"
                                       value="{{ old('email', $setting->email) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group input-group-outline mb-3 @if(old('phone', $setting->phone)) is-filled @endif">
                                <label class="form-label">Telepon</label>
                                <input type="text" name="phone" class="form-control"
                                       value="{{ old('phone', $setting->phone) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group input-group-outline mb-3 @if(old('whatsapp', $setting->whatsapp)) is-filled @endif">
                                <label class="form-label">WhatsApp</label>
                                <input type="text" name="whatsapp" class="form-control"
                                       value="{{ old('whatsapp', $setting->whatsapp) }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="input-group input-group-outline mb-3 @if(old('address', $setting->address)) is-filled @endif">
                                <label class="form-label">Alamat</label>
                                <textarea name="address" rows="2" class="form-control">{{ old('address', $setting->address) }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="input-group input-group-outline mb-3 @if(old('google_maps_url', $setting->google_maps_url)) is-filled @endif">
                                <label class="form-label">Google Maps Embed URL</label>
                                <textarea name="google_maps_url" rows="2" class="form-control">{{ old('google_maps_url', $setting->google_maps_url) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-top p-2 d-flex justify-content-end">
                    <button type="submit" class="btn btn-info mb-0 lp-save-btn" data-form="FormKontak" id="simpan-kontak">
                        <span class="material-symbols-rounded align-middle" style="font-size:18px;">save</span>
                        <span class="lp-btn-label">Simpan Kontak</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- ============ CARD: MEDIA SOSIAL ============ --}}
    <div class="col-12">
        <form id="FormMedsos" method="POST" action="{{ route('app.landing.pengaturan.store') }}"
              class="text-start lp-card-form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="section" value="medsos">
            <div class="card my-4 shadow-sm">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-3"><span class="material-symbols-rounded align-middle">share</span> Media Sosial</h6>
                    <div class="row">
                        @foreach (['facebook' => 'Facebook', 'instagram' => 'Instagram', 'youtube' => 'YouTube', 'tiktok' => 'TikTok'] as $field => $label)
                            <div class="col-md-6">
                                <div class="input-group input-group-outline mb-3 @if(old($field, $setting->{$field})) is-filled @endif">
                                    <label class="form-label">{{ $label }} (URL)</label>
                                    <input type="url" name="{{ $field }}" class="form-control"
                                           placeholder="https://..."
                                           value="{{ old($field, $setting->{$field}) }}">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="card-footer bg-white border-top p-2 d-flex justify-content-end">
                    <button type="submit" class="btn btn-info mb-0 lp-save-btn" data-form="FormMedsos" id="simpan-medsos">
                        <span class="material-symbols-rounded align-middle" style="font-size:18px;">save</span>
                        <span class="lp-btn-label">Simpan Media Sosial</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- ============ CARD: BACKGROUND TEMA ============ --}}
    @php
        $themeDefaults = \App\Models\Landing\LpSetting::themeBackgroundDefaults();
        $activeBgKey = old('hero_background_choice', $setting->activeThemeBackgroundKey());
        $isCustomActive = str_starts_with((string) $activeBgKey, 'custom:');
        $customImageUrl = null;
        $customFileName = null;
        $customFileSize = null;
        if ($isCustomActive) {
            $customFile = substr($activeBgKey, strlen('custom:'));
            if ($customFile !== '') {
                $customDisk = \Illuminate\Support\Facades\Storage::disk('public');
                $customImageUrl = $customDisk->url('landing/' . $customFile);
                $customFileName = $customFile;
                if ($customDisk->exists('landing/' . $customFile)) {
                    $fullPath = $customDisk->path('landing/' . $customFile);
                    $bytes = filesize($fullPath);
                    $customFileSize = $bytes >= 1048576
                        ? number_format($bytes / 1048576, 2) . ' MB'
                        : number_format($bytes / 1024, 0) . ' KB';
                }
            }
        }
    @endphp
    <div class="col-12">
        <form id="FormBackground" method="POST" action="{{ route('app.landing.pengaturan.store') }}"
              class="text-start lp-card-form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="section" value="background">
            <div class="card my-4 shadow-sm">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-1"><span class="material-symbols-rounded align-middle">image</span> Background Tema</h6>
                    <p class="text-muted small mb-3">Pilih gambar latar Hero section di halaman landing. Slot ke-5 untuk upload foto sekolah sendiri.</p>

                    <div class="lp-theme-grid">
                        @foreach ($themeDefaults as $bg)
                            <label class="lp-theme-card d-block @if($activeBgKey === $bg['key']) is-active @endif" data-bg-key="{{ $bg['key'] }}" title="{{ $bg['desc'] ?? '' }}">
                                <input type="radio" name="hero_background_choice" value="{{ $bg['key'] }}" class="d-none lp-theme-radio"
                                       @checked($activeBgKey === $bg['key'])>
                                <div class="lp-theme-thumb">
                                    <img src="/{{ ltrim($bg['image'], '/') }}" alt="{{ $bg['label'] }}">
                                </div>
                                <div class="lp-theme-foot">
                                    <span class="lp-theme-label">
                                        {{ $bg['label'] }}
                                        @if (!empty($bg['desc']))
                                            <small class="d-block text-muted" style="font-weight:400;font-size:.68rem;line-height:1.1;margin-top:1px;">{{ $bg['desc'] }}</small>
                                        @endif
                                    </span>
                                    <span class="material-symbols-rounded lp-theme-check">check_circle</span>
                                </div>
                            </label>
                        @endforeach

                        <label for="heroBackgroundCustomInput" class="lp-theme-card lp-theme-card-custom d-block @if($isCustomActive) is-active @endif" data-bg-key="custom">
                            <input type="radio" name="hero_background_choice" value="custom" class="d-none lp-theme-radio"
                                   @checked($isCustomActive)>
                            <div class="lp-theme-thumb" id="heroBackgroundCustomBox">
                                @if ($customImageUrl)
                                    <img src="{{ $customImageUrl }}" alt="Custom Background" id="heroBackgroundCustomImg">
                                @else
                                    <span class="material-symbols-rounded lp-theme-empty">add_photo_alternate</span>
                                @endif
                            </div>
                            <div class="lp-theme-foot">
                                <span class="lp-theme-label">
                                    Custom
                                    @if ($isCustomActive && $customFileSize)
                                        <small class="d-block text-muted" id="heroBackgroundCustomMeta" style="font-weight:400;font-size:.65rem;line-height:1.15;margin-top:2px;">
                                            {{ $customFileSize }}
                                        </small>
                                    @endif
                                </span>
                                <span class="material-symbols-rounded lp-theme-check">check_circle</span>
                            </div>
                            <input type="file" name="hero_background_custom" accept="image/jpeg,image/jpg,image/png,image/webp" class="d-none" id="heroBackgroundCustomInput">

                            <div class="lp-theme-status" id="heroBackgroundStatus" hidden>
                                <span class="material-symbols-rounded" style="font-size:14px;">check_circle</span>
                                <span id="heroBackgroundStatusText">Foto Custom</span>
                            </div>

                            @if ($isCustomActive && $customImageUrl)
                                <div class="lp-theme-actions" id="heroBackgroundCustomActions">
                                    <button type="button" class="lp-theme-action-btn" id="heroBackgroundViewBtn"
                                            data-bs-toggle="modal" data-bs-target="#heroBackgroundViewModal"
                                            data-image-url="{{ $customImageUrl }}"
                                            data-image-name="{{ $customFileSize ?: 'Custom' }}"
                                            title="Lihat gambar penuh">
                                        <span class="material-symbols-rounded" style="font-size:14px;">open_in_full</span>
                                    </button>
                                    <button type="button" class="lp-theme-action-btn is-danger" id="heroBackgroundRemoveBtn"
                                            title="Hapus custom, kembali ke default">
                                        <span class="material-symbols-rounded" style="font-size:14px;">delete</span>
                                    </button>
                                </div>
                            @endif
                        </label>
                    </div>

                    <div class="small text-muted mt-3">
                        <span class="material-symbols-rounded align-middle" style="font-size:16px">info</span>
                        Semua ukuran foto diterima. Sistem akan otomatis menyesuaikan ke <b>1920×1080</b> (crop tengah, seperti <code>background-size: cover</code>) dan mengkompres ke JPG. Format: JPG, PNG, WEBP. Maks 10&nbsp;MB.
                    </div>
                </div>
                <div class="card-footer bg-white border-top p-2 d-flex justify-content-end">
                    <button type="submit" class="btn btn-info mb-0 lp-save-btn" data-form="FormBackground" id="simpan-background">
                        <span class="material-symbols-rounded align-middle" style="font-size:18px;">save</span>
                        <span class="lp-btn-label">Simpan Background</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    @if ($isCustomActive && $customImageUrl)
        <div class="modal fade" id="heroBackgroundViewModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header py-2">
                        <h6 class="modal-title fw-bold">
                            <span class="material-symbols-rounded align-middle">image</span>
                            Preview Background Custom
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-2 text-center" style="background:#0f172a;">
                        <img id="heroBackgroundViewImg" src="{{ $customImageUrl }}" alt="Custom Background" class="img-fluid" style="max-height:70vh;border-radius:.5rem;">
                        <div class="text-white-50 small mt-2" id="heroBackgroundViewCaption">{{ $customFileSize ?: 'Custom' }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ============ CARD: WARNA TOMBOL & TEXT ============ --}}
    @php
        $buttonPresets = \App\Models\Landing\LpSetting::themeButtonColorDefaults();

        $activeButtonValue = $setting->activeThemeButtonColor();

        $activeButtonPreset = null;
        foreach ($buttonPresets as $preset) {
            if (strtoupper($preset['value']) === strtoupper($activeButtonValue)) {
                $activeButtonPreset = $preset['key'];
                break;
            }
        }
        $isButtonCustom = $activeButtonPreset === null;
        $oldButtonChoice = old('theme_button_color_choice', $isButtonCustom ? 'custom' : $activeButtonPreset);
        $oldButtonCustom = old('theme_button_color_custom', $isButtonCustom ? $activeButtonValue : '#2563eb');
    @endphp

    <div class="col-12">
        <form id="FormWarna" method="POST" action="{{ route('app.landing.pengaturan.store') }}"
              class="text-start lp-card-form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="section" value="warna">
            <div class="card my-4 shadow-sm">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-1"><span class="material-symbols-rounded align-middle">palette</span> Warna Tombol &amp; Text</h6>
                    <p class="text-muted small mb-3">Atur warna utama tombol dan teks di halaman landing. Slot ke-5 untuk pilih warna sendiri.</p>

                    <div>
                        <label class="form-label small fw-bold d-block mb-2">Warna Utama (Tombol &amp; Text)</label>
                        <div class="lp-color-grid">
                            @foreach ($buttonPresets as $preset)
                                <label class="lp-color-card @if($oldButtonChoice === $preset['key']) is-active @endif"
                                       data-color-key="{{ $preset['key'] }}"
                                       data-color-value="{{ $preset['value'] }}"
                                       style="--swatch:{{ $preset['value'] }};">
                                    <input type="radio" name="theme_button_color_choice" value="{{ $preset['key'] }}" class="d-none lp-color-radio"
                                           @checked($oldButtonChoice === $preset['key'])>
                                    <span class="lp-color-swatch"></span>
                                    <span class="lp-color-label">{{ $preset['label'] }}</span>
                                    <span class="material-symbols-rounded lp-color-check">check_circle</span>
                                </label>
                            @endforeach

                            <label class="lp-color-card lp-color-card-custom @if($oldButtonChoice === 'custom') is-active @endif"
                                   data-color-key="custom" style="--swatch:{{ $oldButtonCustom }};">
                                <input type="radio" name="theme_button_color_choice" value="custom" class="d-none lp-color-radio"
                                       @checked($oldButtonChoice === 'custom')>
                                <button type="button" class="lp-color-swatch lp-color-custom-trigger"
                                        id="themeButtonColorMount"
                                        aria-label="Pilih warna kustom"
                                        style="background-color:{{ $oldButtonCustom }};">
                                    <span class="material-symbols-rounded lp-color-custom-icon">tune</span>
                                </button>
                                <input type="text" name="theme_button_color_custom" value="{{ $oldButtonCustom }}"
                                       class="lp-color-input-hidden" id="themeButtonColorCustom">
                                <span class="lp-color-label">Custom</span>
                                <span class="material-symbols-rounded lp-color-check">check_circle</span>
                            </label>
                        </div>
                    </div>

                    <div class="small text-muted mt-3">
                        <span class="material-symbols-rounded align-middle" style="font-size:16px">info</span>
                        Warna ini berlaku sekaligus untuk tombol dan teks utama. Slot custom (ke-5) untuk pilih warna sendiri.
                    </div>
                </div>
                <div class="card-footer bg-white border-top p-2 d-flex justify-content-end">
                    <button type="submit" class="btn btn-info mb-0 lp-save-btn" data-form="FormWarna" id="simpan-warna">
                        <span class="material-symbols-rounded align-middle" style="font-size:18px;">save</span>
                        <span class="lp-btn-label">Simpan Warna</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- ============ CARD: SAMBUTAN KEPALA SEKOLAH ============ --}}
    @php
        $welcome = $setting->welcome ?: [];
        $welcomePhotoUrl = null;
        if (!empty($welcome['photo']) && is_string($welcome['photo']) && str_starts_with($welcome['photo'], 'uploaded:')) {
            $wf = substr($welcome['photo'], strlen('uploaded:'));
            if ($wf !== '') {
                $welcomePhotoUrl = \Illuminate\Support\Facades\Storage::disk('public')->url('landing/' . $wf);
            }
        } elseif (!empty($welcome['photo'])) {
            $welcomePhotoUrl = $welcome['photo'];
        }
    @endphp
    <div class="col-12">
        <form id="FormSambutan" method="POST" action="{{ route('app.landing.pengaturan.store') }}"
              class="text-start lp-card-form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="section" value="sambutan">
            <div class="card my-4 shadow-sm">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-3"><span class="material-symbols-rounded align-middle">person_celebrate</span> Sambutan Kepala Sekolah</h6>
                    <p class="text-muted small mb-3">Teks & foto yang tampil di section "Sambutan Kepala Sekolah" di halaman beranda. Gunakan <code>&#123;&#123;school&#125;&#125;</code> di paragraf untuk otomatis diganti nama sekolah.</p>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Foto Kepala Sekolah</label>
                            <label for="welcomePhotoInput" class="lp-preview-box d-block" id="welcomePhotoPreviewBox">
                                @if ($welcomePhotoUrl)
                                    <img src="{{ $welcomePhotoUrl }}" alt="Foto" id="welcomePhotoPreviewImg">
                                @else
                                    <span class="material-symbols-rounded lp-preview-empty" id="welcomePhotoPreviewEmpty">add_photo_alternate</span>
                                @endif
                                <span class="lp-preview-hint">Klik untuk pilih foto</span>
                            </label>
                            <input type="file" name="welcome_photo_upload" class="d-none" accept="image/*" id="welcomePhotoInput">
                            @if (!empty($welcome['photo']) && str_starts_with((string) $welcome['photo'], 'uploaded:'))
                                <div class="form-check mt-1 text-center">
                                    <input type="checkbox" name="welcome_photo_clear" value="1" class="form-check-input" id="welcomePhotoClear">
                                    <label class="form-check-label small" for="welcomePhotoClear">Hapus foto, pakai default</label>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <div class="input-group input-group-outline mb-3 @if(old('welcome_quote', $welcome['quote'] ?? '')) is-filled @endif">
                                <label class="form-label">Quote (kutipan singkat)</label>
                                <input type="text" name="welcome_quote" class="form-control" maxlength="255"
                                       value="{{ old('welcome_quote', $welcome['quote'] ?? '') }}">
                            </div>
                            <div class="input-group input-group-outline mb-3 @if(old('welcome_head_name', $welcome['head_name'] ?? '')) is-filled @endif">
                                <label class="form-label">Nama Kepala Sekolah</label>
                                <input type="text" name="welcome_head_name" class="form-control" maxlength="150"
                                       value="{{ old('welcome_head_name', $welcome['head_name'] ?? '') }}">
                            </div>
                            <div class="input-group input-group-outline mb-3 @if(old('welcome_head_role', $welcome['head_role'] ?? '')) is-filled @endif">
                                <label class="form-label">Jabatan</label>
                                <input type="text" name="welcome_head_role" class="form-control" maxlength="200"
                                       placeholder="Kepala Sekolah, &#123;&#123;school&#125;&#125;"
                                       value="{{ old('welcome_head_role', $welcome['head_role'] ?? '') }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="input-group input-group-outline mb-3 @if(old('welcome_paragraph_1', $welcome['paragraph_1'] ?? '')) is-filled @endif">
                                <label class="form-label">Paragraf 1</label>
                                <textarea name="welcome_paragraph_1" rows="3" class="form-control">{{ old('welcome_paragraph_1', $welcome['paragraph_1'] ?? '') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="input-group input-group-outline mb-3 @if(old('welcome_paragraph_2', $welcome['paragraph_2'] ?? '')) is-filled @endif">
                                <label class="form-label">Paragraf 2</label>
                                <textarea name="welcome_paragraph_2" rows="3" class="form-control">{{ old('welcome_paragraph_2', $welcome['paragraph_2'] ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-top p-2 d-flex justify-content-end">
                    <button type="submit" class="btn btn-info mb-0 lp-save-btn" data-form="FormSambutan" id="simpan-sambutan">
                        <span class="material-symbols-rounded align-middle" style="font-size:18px;">save</span>
                        <span class="lp-btn-label">Simpan Sambutan</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- ============ CARD: STATISTIK ============ --}}
    @php
        $statsExisting = $setting->stats ?: [];
        $statColorOptions = ['blue', 'green', 'amber', 'pink', 'purple', 'cyan'];
    @endphp
    <div class="col-12">
        <form id="FormStatistik" method="POST" action="{{ route('app.landing.pengaturan.store') }}"
              class="text-start lp-card-form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="section" value="statistik">
            <div class="card my-4 shadow-sm">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-3"><span class="material-symbols-rounded align-middle">monitoring</span> Statistik (3 Kartu)</h6>
                    <p class="text-muted small mb-3">Tiga kartu statistik di section "Stats" halaman beranda. Icon menggunakan Bootstrap Icon class (mis. <code>bi-people-fill</code>).</p>
                    @for ($i = 1; $i <= 3; $i++)
                        @php $cur = $statsExisting[$i - 1] ?? []; @endphp
                        <div class="row border rounded p-2 mb-2 mx-0 bg-light">
                            <div class="col-md-12"><small class="fw-bold text-muted">Kartu {{ $i }}</small></div>
                            <div class="col-md-3">
                                <div class="input-group input-group-outline mb-0 @if(old('stats_icon_'.$i, $cur['icon'] ?? '')) is-filled @endif">
                                    <label class="form-label small">Icon (bi-xxx)</label>
                                    <input type="text" name="stats_icon_{{ $i }}" class="form-control"
                                           placeholder="bi-people-fill"
                                           value="{{ old('stats_icon_'.$i, $cur['icon'] ?? '') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small mb-1 d-block">Warna</label>
                                <select name="stats_color_{{ $i }}" class="form-select form-select-sm">
                                    @foreach ($statColorOptions as $c)
                                        <option value="{{ $c }}" @selected(old('stats_color_'.$i, $cur['color'] ?? 'blue') === $c)>{{ ucfirst($c) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group input-group-outline mb-0 @if(old('stats_value_'.$i, $cur['value'] ?? '')) is-filled @endif">
                                    <label class="form-label small">Nilai</label>
                                    <input type="text" name="stats_value_{{ $i }}" class="form-control"
                                           placeholder="1.200+"
                                           value="{{ old('stats_value_'.$i, $cur['value'] ?? '') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group input-group-outline mb-0 @if(old('stats_label_'.$i, $cur['label'] ?? '')) is-filled @endif">
                                    <label class="form-label small">Label</label>
                                    <input type="text" name="stats_label_{{ $i }}" class="form-control"
                                           placeholder="Siswa Aktif"
                                           value="{{ old('stats_label_'.$i, $cur['label'] ?? '') }}">
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
                <div class="card-footer bg-white border-top p-2 d-flex justify-content-end">
                    <button type="submit" class="btn btn-info mb-0 lp-save-btn" data-form="FormStatistik" id="simpan-statistik">
                        <span class="material-symbols-rounded align-middle" style="font-size:18px;">save</span>
                        <span class="lp-btn-label">Simpan Statistik</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- ============ CARD: JENJANG ============ --}}
    @php
        $jenjangExisting = $setting->jenjang ?: [];
    @endphp
    <div class="col-12">
        <form id="FormJenjang" method="POST" action="{{ route('app.landing.pengaturan.store') }}"
              class="text-start lp-card-form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="section" value="jenjang">
            <div class="card my-4 shadow-sm">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-3"><span class="material-symbols-rounded align-middle">school</span> Jenjang Pendidikan (4 Kartu)</h6>
                    <p class="text-muted small mb-3">Empat kartu jenjang (TK, SD, SMP, SMA) yang tampil di halaman beranda.</p>
                    @for ($i = 1; $i <= 4; $i++)
                        @php $cur = $jenjangExisting[$i - 1] ?? []; @endphp
                        <div class="row border rounded p-2 mb-2 mx-0 bg-light">
                            <div class="col-md-12"><small class="fw-bold text-muted">Jenjang {{ $i }}</small></div>
                            <div class="col-md-3">
                                <div class="input-group input-group-outline mb-0 @if(old('jenjang_age_'.$i, $cur['age'] ?? '')) is-filled @endif">
                                    <label class="form-label small">Rentang Usia/Kelas</label>
                                    <input type="text" name="jenjang_age_{{ $i }}" class="form-control"
                                           placeholder="KELAS 1–6"
                                           value="{{ old('jenjang_age_'.$i, $cur['age'] ?? '') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-outline mb-0 @if(old('jenjang_title_'.$i, $cur['title'] ?? '')) is-filled @endif">
                                    <label class="form-label small">Judul</label>
                                    <input type="text" name="jenjang_title_{{ $i }}" class="form-control"
                                           placeholder="Sekolah Dasar"
                                           value="{{ old('jenjang_title_'.$i, $cur['title'] ?? '') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-outline mb-0 @if(old('jenjang_icon_'.$i, $cur['icon'] ?? '')) is-filled @endif">
                                    <label class="form-label small">Icon (bi-xxx)</label>
                                    <input type="text" name="jenjang_icon_{{ $i }}" class="form-control"
                                           placeholder="bi-mortarboard-fill"
                                           value="{{ old('jenjang_icon_'.$i, $cur['icon'] ?? '') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-outline mb-0 @if(old('jenjang_desc_'.$i, $cur['desc'] ?? '')) is-filled @endif">
                                    <label class="form-label small">Deskripsi</label>
                                    <input type="text" name="jenjang_desc_{{ $i }}" class="form-control"
                                           placeholder="Deskripsi singkat"
                                           value="{{ old('jenjang_desc_'.$i, $cur['desc'] ?? '') }}">
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
                <div class="card-footer bg-white border-top p-2 d-flex justify-content-end">
                    <button type="submit" class="btn btn-info mb-0 lp-save-btn" data-form="FormJenjang" id="simpan-jenjang">
                        <span class="material-symbols-rounded align-middle" style="font-size:18px;">save</span>
                        <span class="lp-btn-label">Simpan Jenjang</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- ============ CARD: KEUNGGULAN ============ --}}
    @php
        $keunggulanExisting = $setting->keunggulan ?: [];
    @endphp
    <div class="col-12">
        <form id="FormKeunggulan" method="POST" action="{{ route('app.landing.pengaturan.store') }}"
              class="text-start lp-card-form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="section" value="keunggulan">
            <div class="card my-4 shadow-sm">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-3"><span class="material-symbols-rounded align-middle">workspace_premium</span> Keunggulan Sekolah (6 Kartu)</h6>
                    <p class="text-muted small mb-3">Enam kartu keunggulan yang tampil di section "Mengapa Kami" halaman beranda.</p>
                    @for ($i = 1; $i <= 6; $i++)
                        @php $cur = $keunggulanExisting[$i - 1] ?? []; @endphp
                        <div class="row border rounded p-2 mb-2 mx-0 bg-light">
                            <div class="col-md-12"><small class="fw-bold text-muted">Kartu {{ $i }}</small></div>
                            <div class="col-md-3">
                                <label class="form-label small mb-1 d-block">Warna</label>
                                <select name="keunggulan_color_{{ $i }}" class="form-select form-select-sm">
                                    @foreach ($statColorOptions as $c)
                                        <option value="{{ $c }}" @selected(old('keunggulan_color_'.$i, $cur['color'] ?? 'blue') === $c)>{{ ucfirst($c) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-outline mb-0 @if(old('keunggulan_icon_'.$i, $cur['icon'] ?? '')) is-filled @endif">
                                    <label class="form-label small">Icon (bi-xxx)</label>
                                    <input type="text" name="keunggulan_icon_{{ $i }}" class="form-control"
                                           placeholder="bi-book-fill"
                                           value="{{ old('keunggulan_icon_'.$i, $cur['icon'] ?? '') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-outline mb-0 @if(old('keunggulan_title_'.$i, $cur['title'] ?? '')) is-filled @endif">
                                    <label class="form-label small">Judul</label>
                                    <input type="text" name="keunggulan_title_{{ $i }}" class="form-control"
                                           placeholder="Kurikulum Merdeka"
                                           value="{{ old('keunggulan_title_'.$i, $cur['title'] ?? '') }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="input-group input-group-outline mb-0 @if(old('keunggulan_desc_'.$i, $cur['desc'] ?? '')) is-filled @endif">
                                    <label class="form-label small">Deskripsi</label>
                                    <textarea name="keunggulan_desc_{{ $i }}" rows="2" class="form-control"
                                              placeholder="Penjelasan singkat keunggulan">{{ old('keunggulan_desc_'.$i, $cur['desc'] ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
                <div class="card-footer bg-white border-top p-2 d-flex justify-content-end">
                    <button type="submit" class="btn btn-info mb-0 lp-save-btn" data-form="FormKeunggulan" id="simpan-keunggulan">
                        <span class="material-symbols-rounded align-middle" style="font-size:18px;">save</span>
                        <span class="lp-btn-label">Simpan Keunggulan</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- ============ CARD: SEO ============ --}}
    <div class="col-12">
        <form id="FormSeo" method="POST" action="{{ route('app.landing.pengaturan.store') }}"
              class="text-start lp-card-form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="section" value="seo">
            <div class="card my-4 shadow-sm">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-3"><span class="material-symbols-rounded align-middle">search</span> SEO</h6>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="input-group input-group-outline mb-3 @if(old('meta_description', $setting->meta_description)) is-filled @endif">
                                <label class="form-label">Meta Description</label>
                                <input type="text" name="meta_description" class="form-control" maxlength="255"
                                       value="{{ old('meta_description', $setting->meta_description) }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="input-group input-group-outline mb-3 @if(old('meta_keywords', $setting->meta_keywords)) is-filled @endif">
                                <label class="form-label">Meta Keywords</label>
                                <input type="text" name="meta_keywords" class="form-control" maxlength="255"
                                       value="{{ old('meta_keywords', $setting->meta_keywords) }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-top p-2 d-flex justify-content-end">
                    <button type="submit" class="btn btn-info mb-0 lp-save-btn" data-form="FormSeo" id="simpan-seo">
                        <span class="material-symbols-rounded align-middle" style="font-size:18px;">save</span>
                        <span class="lp-btn-label">Simpan SEO</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

</div>
@endsection

@section('script')
@include('landing-admin._scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr@1.9.1/dist/themes/nano.min.css">
<script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr@1.9.1/dist/pickr.min.js"></script>
<script>
$(function () {
    // Material-style filled state for inputs/textareas (semua form).
    $('.lp-card-form input, .lp-card-form textarea, .lp-card-form select').each(function () {
        if ($(this).val() && $(this).val() !== '' && $(this).attr('type') !== 'password') {
            $(this).closest('.input-group').addClass('is-filled');
        }
    });
    $('.lp-card-form input, .lp-card-form textarea').on('input', function () {
        $(this).closest('.input-group').addClass('is-filled');
    });

    // Toast helper
    var Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3500,
        timerProgressBar: true,
    });

    // Generic per-form submit handler. Setiap form .lp-card-form submit
    // independen via endpoint pengaturan.store; field dari form lain ikut
    // terkirim tapi diabaikan server karena tidak ada di whitelist.
    $('.lp-card-form').on('submit', function (e) {
        e.preventDefault();

        var $form = $(this);
        var $btn = $form.find('.lp-save-btn');
        var $label = $btn.find('.lp-btn-label');
        var origLabel = $label.text();

        if ($btn.data('busy')) return;
        $btn.data('busy', true).prop('disabled', true);
        $label.text('Menyimpan...');

        var formEl = $form[0];
        var actionUrl = $form.attr('action');
        var formData = new FormData(formEl);

        $.ajax({
            type: 'POST',
            url: actionUrl,
            data: formData,
            contentType: false,
            processData: false,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (result) {
                $btn.data('busy', false).prop('disabled', false);
                $label.text(origLabel);

                if (result.success) {
                    Toast.fire({ icon: 'success', title: result.msg || 'Tersimpan' });

                    // Untuk card background, sinkronkan ulang meta bila server mengembalikan info.
                    if (result.hero_background_url) {
                        var heroUrl = result.hero_background_url;
                        var meta = result.hero_background_meta || null;
                        var $customMeta = $('#heroBackgroundCustomMeta');
                        if ($customMeta.length && meta && meta.size_label) {
                            $customMeta.text(meta.size_label);
                        }

                        // Tampilkan detail background yang baru di-update.
                        var isCustom = result.hero_background_key
                            && String(result.hero_background_key).indexOf('custom:') === 0;

                        var presetLabels = {
                            'default-1': 'Standar 1 — Biru Ceria',
                            'default-2': 'Standar 2 — Hijau Edukatif',
                            'default-3': 'Standar 3 — Emas Premium',
                            'default-4': 'Standar 4 — Ungu Kreatif',
                        };
                        var activeKey = result.hero_background_key || '';
                        var presetLabel = presetLabels[activeKey] || null;

                        var html = '';
                        if (heroUrl) {
                            html += '<div style="border-radius:.5rem;overflow:hidden;border:1px solid #e2e8f0;margin-bottom:.75rem;background:#0f172a;">'
                                + '<img src="' + heroUrl + '?v=' + Date.now() + '" alt="Background" style="width:100%;max-height:220px;object-fit:cover;display:block;">'
                                + '</div>';
                        }
                        if (isCustom) {
                            html += '<div class="text-start mb-2" style="font-size:.85rem;">'
                                + '<div class="d-flex align-items-center gap-1 mb-1">'
                                +   '<span class="material-symbols-rounded" style="font-size:16px;color:#1f9d57;">check_circle</span>'
                                +   '<b>Custom Upload</b>'
                                + '</div>';
                            if (meta && meta.size_label) {
                                html += '<div class="text-muted">Ukuran file: <b>' + meta.size_label + '</b></div>';
                            }
                            if (meta && meta.width && meta.height) {
                                html += '<div class="text-muted">Resolusi: <b>' + meta.width + ' × ' + meta.height + ' px</b></div>';
                            }
                            html += '</div>';
                        } else if (presetLabel) {
                            html += '<div class="text-start mb-2" style="font-size:.85rem;">'
                                + '<div class="d-flex align-items-center gap-1 mb-1">'
                                +   '<span class="material-symbols-rounded" style="font-size:16px;color:#1f9d57;">check_circle</span>'
                                +   '<b>' + presetLabel + '</b>'
                                + '</div>'
                                + '<div class="text-muted">Background tema standar aktif.</div>'
                                + '</div>';
                        }

                        var landingUrl = result.landing_url || null;
                        html += '<div class="d-flex gap-2 justify-content-center flex-wrap mt-3">';
                        if (landingUrl) {
                            html += '<a href="' + landingUrl + '" target="_blank" rel="noopener" class="btn btn-sm btn-success">'
                                + '<span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle;">open_in_new</span> '
                                + 'Lihat Landing'
                                + '</a>';
                        }
                        html += '<button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>';
                        html += '</div>';

                        Swal.fire({
                            title: 'Background Berhasil Diperbarui',
                            html: html,
                            icon: 'success',
                            showConfirmButton: false,
                            showCloseButton: true,
                            width: 520,
                        });
                    }

                    clearPendingCustom();
                } else {
                    Swal.fire('Gagal', result.msg || 'Terjadi kesalahan', 'error');
                }
            },
            error: function (xhr) {
                $btn.data('busy', false).prop('disabled', false);
                $label.text(origLabel);

                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = xhr.responseJSON.errors;
                    var list = Object.keys(errors)
                        .map(function (field) {
                            var label = $form.find('[name="' + field + '"]')
                                .closest('.input-group')
                                .find('.form-label').text().trim();
                            return '<li>' + (label || field) + '</li>';
                        })
                        .join('');
                    Swal.fire({
                        icon: 'error',
                        title: 'Data belum lengkap',
                        html: 'Inputan berikut masih kosong / tidak valid:<ul class="text-start">' + list + '</ul>'
                    });
                    $.each(errors, function (key) {
                        var el = $form.find('[name="' + key + '"]');
                        el.addClass('is-invalid');
                        el.closest('.input-group').addClass('is-invalid');
                    });
                } else if (xhr.responseJSON && xhr.responseJSON.msg) {
                    Swal.fire('Gagal', xhr.responseJSON.msg, 'error');
                } else {
                    Swal.fire('Galat', 'Cek kembali input yang anda masukkan', 'error');
                }
            }
        });
    });

    // Live preview untuk logo & favicon (di dalam FormIdentitas)
    function bindPreview(inputId, boxId) {
        var input = document.getElementById(inputId);
        var box = document.getElementById(boxId);
        if (!input || !box) return;
        input.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    box.innerHTML = '<img src="' + e.target.result + '" alt="preview">'
                        + '<span class="lp-preview-hint">Klik untuk ganti</span>';
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
    bindPreview('logoInput', 'logoPreviewBox');
    bindPreview('faviconInput', 'faviconPreviewBox');
    bindPreview('welcomePhotoInput', 'welcomePhotoPreviewBox');

    // Theme background picker (di dalam FormBackground)
    var themeCards = document.querySelectorAll('.lp-theme-card');
    var customInput = document.getElementById('heroBackgroundCustomInput');
    var customBox = document.getElementById('heroBackgroundCustomBox');

    function activateThemeCard(card) {
        themeCards.forEach(function (c) { c.classList.remove('is-active'); });
        if (card) card.classList.add('is-active');
    }

    themeCards.forEach(function (card) {
        var radio = card.querySelector('.lp-theme-radio');
        if (!radio) return;

        card.addEventListener('click', function (e) {
            if (e.target === customInput) return;
            if (e.target.closest('.lp-theme-actions')) return;
            themeCards.forEach(function (c) { c.querySelector('.lp-theme-radio').checked = false; });
            radio.checked = true;
            activateThemeCard(card);
        });
    });

    var pendingFile = null;

    function clearPendingCustom() {
        pendingFile = null;
        var statusEl = document.getElementById('heroBackgroundStatus');
        if (statusEl) statusEl.hidden = true;
        var customCard = document.querySelector('.lp-theme-card-custom');
        if (customCard) customCard.classList.remove('has-pending');
    }

    function showReadyToSave(file, width, height) {
        pendingFile = file;
        var statusEl = document.getElementById('heroBackgroundStatus');
        var statusText = document.getElementById('heroBackgroundStatusText');
        if (statusEl) statusEl.hidden = false;
        if (statusText) statusText.textContent = 'Foto Custom';

        var customCard = document.querySelector('.lp-theme-card-custom');
        if (customCard) customCard.classList.add('has-pending');

        Toast.fire({
            icon: 'success',
            title: 'Foto Custom siap disimpan',
        });
    }

    if (customInput) {
        customInput.addEventListener('change', function (e) {
            var file = e.target.files && e.target.files[0];
            if (!file) return;

            if (!/^image\/(jpeg|jpg|png|webp)$/.test(file.type)) {
                Toast.fire({ icon: 'error', title: 'Format tidak didukung', text: 'Gunakan JPG, JPEG, PNG, atau WEBP.' });
                customInput.value = '';
                clearPendingCustom();
                return;
            }
            if (file.size > 10 * 1024 * 1024) {
                Toast.fire({ icon: 'error', title: 'Ukuran terlalu besar', text: 'Maksimal 10 MB.' });
                customInput.value = '';
                clearPendingCustom();
                return;
            }

            var reader = new FileReader();
            reader.onload = function (ev) {
                if (customBox) {
                    customBox.innerHTML = '<img src="' + ev.target.result + '" alt="Custom Background" id="heroBackgroundCustomImg">';
                }
                var customCard = document.querySelector('.lp-theme-card-custom');
                if (customCard) {
                    var customRadio = customCard.querySelector('.lp-theme-radio');
                    if (customRadio) customRadio.checked = true;
                    activateThemeCard(customCard);
                }

                var img = new Image();
                img.onload = function () {
                    showReadyToSave(file, img.width, img.height);
                };
                img.onerror = function () {
                    showReadyToSave(file, 0, 0);
                };
                img.src = ev.target.result;
            };
            reader.readAsDataURL(file);
        });
    }

    // Tombol "Hapus Custom" - kembalikan ke default-1 via AJAX
    var removeBtn = document.getElementById('heroBackgroundRemoveBtn');
    if (removeBtn) {
        removeBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            Swal.fire({
                title: 'Hapus Background Custom?',
                text: 'Foto yang Anda upload akan dihapus dan Hero kembali ke Standar 1.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc2626',
            }).then(function (res) {
                if (!res.isConfirmed) return;
                $.ajax({
                    type: 'POST',
                    url: '{{ route('app.landing.pengaturan.custom.destroy') }}',
                    data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    success: function (result) {
                        Swal.fire({
                            title: result.msg || 'Berhasil',
                            icon: 'success',
                            confirmButtonText: 'OK',
                        }).then(function () {
                            window.location.reload();
                        });
                    },
                    error: function () {
                        Swal.fire('Gagal', 'Terjadi kesalahan saat menghapus.', 'error');
                    },
                });
            });
        });
    }

    // Reset pending state bila admin pilih preset default (bukan custom)
    document.querySelectorAll('.lp-theme-card:not(.lp-theme-card-custom) .lp-theme-radio').forEach(function (radio) {
        radio.addEventListener('change', function () {
            if (radio.checked) clearPendingCustom();
        });
    });

    // Modal preview - inject URL saat tombol diklik
    var viewBtn = document.getElementById('heroBackgroundViewBtn');
    var viewModal = document.getElementById('heroBackgroundViewModal');
    if (viewBtn && viewModal) {
        viewBtn.addEventListener('click', function () {
            var url = viewBtn.getAttribute('data-image-url');
            var name = viewBtn.getAttribute('data-image-name');
            var img = document.getElementById('heroBackgroundViewImg');
            var caption = document.getElementById('heroBackgroundViewCaption');
            if (img) img.src = url;
            if (caption) caption.textContent = name || '';
        });
    }

    // Color picker: preset + Pickr-based custom (di dalam FormWarna)
    function setupColorPicker(groupName) {
        var cards = document.querySelectorAll('.lp-color-card[data-color-key]:not(.lp-color-card-custom)');
        var customCardRadio = document.querySelector('input[name="' + groupName + '_choice"][value="custom"]');
        if (!customCardRadio) return;
        var customCardWrap = customCardRadio.closest('.lp-color-card-custom');
        var colorInput = customCardWrap ? customCardWrap.querySelector('.lp-color-input-hidden') : null;

        function setActive(card) {
            cards.forEach(function (c) { c.classList.remove('is-active'); });
            if (customCardWrap) customCardWrap.classList.remove('is-active');
            if (card) card.classList.add('is-active');
        }

        cards.forEach(function (card) {
            var radio = card.querySelector('input[type="radio"]');
            if (!radio) return;
            card.addEventListener('click', function (e) {
                if (customCardWrap && customCardWrap.contains(e.target) && e.target.tagName !== 'INPUT') return;
                cards.forEach(function (c) {
                    var r = c.querySelector('input[type="radio"]');
                    if (r) r.checked = false;
                });
                if (customCardRadio) customCardRadio.checked = false;
                radio.checked = true;
                setActive(card);
            });
        });

        var triggerBtn = customCardWrap.querySelector('.lp-color-custom-trigger');
        if (customCardWrap && colorInput && triggerBtn && window.Pickr) {
            var pickr = Pickr.create({
                el: triggerBtn,
                theme: 'nano',
                default: colorInput.value || '#2563eb',
                useAsButton: true,
                appendToBody: true,
                autoReposition: true,
                components: {
                    preview: true,
                    opacity: false,
                    hue: true,
                    interaction: {
                        hex: true,
                        rgba: false,
                        hsla: false,
                        hsva: false,
                        cmyk: false,
                        input: true,
                        clear: false,
                        save: true,
                    },
                },
                position: 'bottom-middle',
            });

            pickr.on('save', function (color) {
                if (!color) return;
                var hex = color.toHEXA().toString().toUpperCase();
                colorInput.value = hex;
                triggerBtn.style.backgroundColor = hex;
                customCardWrap.style.setProperty('--swatch', hex);
                customCardRadio.checked = true;
                setActive(customCardWrap);
                pickr.hide();
            });

            pickr.on('change', function (color) {
                if (!color) return;
                var hex = color.toHEXA().toString().toUpperCase();
                triggerBtn.style.backgroundColor = hex;
                customCardWrap.style.setProperty('--swatch', hex);
            });

            triggerBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                customCardRadio.checked = true;
                setActive(customCardWrap);
                pickr.show();
            });

            customCardWrap.addEventListener('click', function (e) {
                if (e.target.tagName === 'INPUT' || e.target === triggerBtn || triggerBtn.contains(e.target)) return;
                customCardRadio.checked = true;
                setActive(customCardWrap);
            });
        }
    }
    setupColorPicker('theme_button_color');
});
</script>

<style>
    .lp-preview-box {
        width: 100%;
        height: 160px;
        border: 2px dashed #cbd5e1;
        border-radius: .75rem;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: .35rem;
        overflow: hidden;
        cursor: pointer;
        margin: 0;
        padding: .5rem;
        transition: border-color .15s ease, background .15s ease, transform .15s ease;
        text-align: center;
    }
    .lp-preview-box:hover {
        border-color: #37d17c;
        background: #fff;
        transform: translateY(-1px);
    }
    .lp-preview-box img {
        max-width: 70%;
        max-height: 70%;
        object-fit: contain;
        pointer-events: none;
    }
    .lp-preview-empty {
        font-size: 42px;
        color: #94a3b8;
        pointer-events: none;
    }
    .lp-preview-box-sm .lp-preview-empty {
        font-size: 36px;
    }
    .lp-preview-hint {
        font-size: .75rem;
        color: #64748b;
        font-weight: 500;
        pointer-events: none;
    }
    .lp-preview-box:hover .lp-preview-hint {
        color: #1f9d57;
    }

    .lp-theme-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: .75rem;
    }
    @media (max-width: 767.98px) {
        .lp-theme-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }
    @media (max-width: 480px) {
        .lp-theme-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    .lp-theme-card {
        cursor: pointer;
        border: 2px solid #e2e8f0;
        border-radius: .85rem;
        overflow: hidden;
        background: #fff;
        transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
        margin: 0;
        position: relative;
    }
    .lp-theme-card:hover {
        border-color: #37d17c;
        transform: translateY(-1px);
    }
    .lp-theme-card.is-active {
        border-color: #1f9d57;
        box-shadow: 0 0 0 3px rgba(31,157,87,.15);
    }
    .lp-theme-thumb {
        width: 100%;
        aspect-ratio: 4/3;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .lp-theme-thumb img {
        max-width: 100%;
        max-height: 100%;
        width: auto;
        height: auto;
        object-fit: contain;
        display: block;
    }
    .lp-theme-empty {
        font-size: 38px;
        color: #94a3b8;
    }
    .lp-theme-foot {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: .5rem .65rem;
        font-size: .78rem;
        font-weight: 600;
        color: #334155;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
    }
    .lp-theme-check {
        font-size: 18px;
        color: #cbd5e1;
        transition: color .15s ease, transform .15s ease;
    }
    .lp-theme-card.is-active .lp-theme-check {
        color: #1f9d57;
        transform: scale(1.15);
    }
    .lp-theme-card-custom .lp-theme-thumb {
        cursor: pointer;
    }
    .lp-theme-actions {
        position: absolute;
        top: 6px;
        left: 6px;
        right: 6px;
        display: flex;
        justify-content: flex-end;
        gap: 4px;
        z-index: 3;
        pointer-events: none;
    }
    .lp-theme-action-btn {
        all: unset;
        cursor: pointer;
        width: 26px;
        height: 26px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: rgba(15,23,42,.6);
        color: #fff;
        backdrop-filter: blur(4px);
        pointer-events: auto;
        transition: background .15s ease, transform .15s ease;
    }
    .lp-theme-action-btn:hover {
        background: rgba(15,23,42,.85);
        transform: scale(1.1);
    }
    .lp-theme-action-btn.is-danger:hover {
        background: #dc2626;
    }
    .lp-theme-status {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin: .35rem .5rem 0;
        padding: .25rem .55rem;
        border-radius: 50rem;
        background: rgba(31,157,87,.12);
        color: #1f9d57;
        font-size: .68rem;
        font-weight: 600;
        line-height: 1;
    }
    .lp-theme-status[hidden] {
        display: none !important;
    }
    .lp-theme-card.has-pending {
        border-color: #1f9d57;
        box-shadow: 0 0 0 3px rgba(31,157,87,.18);
    }

    .lp-color-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: .75rem;
    }
    @media (max-width: 767.98px) {
        .lp-color-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }
    @media (max-width: 480px) {
        .lp-color-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    .lp-color-card {
        cursor: pointer;
        border: 2px solid #e2e8f0;
        border-radius: .85rem;
        background: #fff;
        padding: .65rem .6rem .55rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: .45rem;
        position: relative;
        transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
    }
    .lp-color-card:hover {
        border-color: #37d17c;
        transform: translateY(-1px);
    }
    .lp-color-card.is-active {
        border-color: #1f9d57;
        box-shadow: 0 0 0 3px rgba(31,157,87,.15);
    }
    .lp-color-swatch {
        width: 100%;
        aspect-ratio: 1;
        border-radius: .65rem;
        background: var(--swatch, #cbd5e1);
        border: 1px solid rgba(15,23,42,.08);
        position: relative;
        overflow: hidden;
    }
    .lp-color-label {
        font-size: .8rem;
        font-weight: 600;
        color: #334155;
        text-align: center;
        line-height: 1.2;
    }
    .lp-color-check {
        position: absolute;
        top: 6px;
        right: 6px;
        font-size: 18px;
        color: #ffffff;
        background: rgba(15,23,42,.5);
        border-radius: 50%;
        padding: 1px;
        opacity: 0;
        transform: scale(.85);
        transition: opacity .15s ease, transform .15s ease;
    }
    .lp-color-card.is-active .lp-color-check {
        opacity: 1;
        transform: scale(1);
    }

    .lp-color-card-custom .lp-color-swatch {
        position: relative;
        cursor: pointer;
        width: 100%;
    }
    .lp-color-custom-trigger {
        all: unset;
        display: block;
        width: 100%;
        aspect-ratio: 1;
        border-radius: .65rem;
        border: 1px solid rgba(15,23,42,.08);
        cursor: pointer;
        position: relative;
        overflow: hidden;
        transition: transform .15s ease, box-shadow .15s ease;
    }
    .lp-color-custom-trigger:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 12px rgba(15,23,42,.12);
    }
    .lp-color-custom-trigger:active {
        transform: scale(.99);
    }
    .lp-color-custom-icon {
        position: absolute;
        bottom: 6px;
        right: 6px;
        background: rgba(255,255,255,.95);
        color: #334155;
        border-radius: 50%;
        font-size: 16px;
        padding: 3px;
        line-height: 1;
        pointer-events: none;
    }
    .lp-color-input-hidden {
        position: absolute;
        width: 1px;
        height: 1px;
        opacity: 0;
        pointer-events: none;
    }

    /* Pickr positioning only - no inline button */
    .pcr-app {
        z-index: 99999 !important;
        font-family: inherit !important;
    }
</style>
@endsection