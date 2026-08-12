@extends('layouts.tenant.base')

@section('style')
    @include('landing-admin._styles')
@endsection

@section('content')
<form id="FormPengaturan" method="POST" action="{{ route('app.landing.pengaturan.store') }}" class="text-start lp-ajax" enctype="multipart/form-data">
    @csrf

    <div class="row">
        <div class="col-12">
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
            </div>
        </div>

        <div class="col-12">
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
            </div>
        </div>

        <div class="col-12">
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
            </div>
        </div>

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
            </div>
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
            </div>
        </div>

        <div class="col-12">
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
            </div>
        </div>

        <div class="col-12 mb-1">
            <div class="card my-4 shadow-sm mb-1">
                <div class="card-body d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-2 p-2 pb-1">
                    <span class="fw-bold" style="font-size: 14px;">
                        Isi semua kolom bertanda <span class="text-danger">*</span>.
                    </span>
                    <button type="submit" class="btn btn-info w-100 w-md-auto mb-1" id="simpan">
                        Simpan Pengaturan
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('script')
@include('landing-admin._scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr@1.9.1/dist/themes/nano.min.css">
<script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr@1.9.1/dist/pickr.min.js"></script>
<script>
$(function () {
    $('#FormPengaturan input, #FormPengaturan textarea, #FormPengaturan select').each(function () {
        if ($(this).val() && $(this).val() !== '' && $(this).attr('type') !== 'password') {
            $(this).closest('.input-group').addClass('is-filled');
        }
    });

    $('#FormPengaturan input, #FormPengaturan textarea').on('input', function () {
        $(this).closest('.input-group').addClass('is-filled');
    });

    var sedangMenyimpan = false;
    $(document).on('click', '#simpan', function (e) {
        e.preventDefault();
        if (sedangMenyimpan) return;
        sedangMenyimpan = true;
        $('#simpan').prop('disabled', true).text('Menyimpan...');

        var form = $('#FormPengaturan')[0];
        var actionUrl = $('#FormPengaturan').attr('action');
        var formData = new FormData(form);

        $.ajax({
            type: 'POST',
            url: actionUrl,
            data: formData,
            contentType: false,
            processData: false,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (result) {
                sedangMenyimpan = false;
                $('#simpan').prop('disabled', false).text('Simpan Pengaturan');

                if (result.success) {
                    var landingUrl = result.landing_url || null;
                    var heroUrl = result.hero_background_url || null;
                    var isCustom = result.hero_background_key && String(result.hero_background_key).indexOf('custom:') === 0;
                    var meta = result.hero_background_meta || null;
                    var html = '';
                    if (heroUrl) {
                        html += '<div style="border-radius:.5rem;overflow:hidden;border:1px solid #e2e8f0;margin-bottom:.5rem;background:#0f172a;">'
                            + '<img src="' + heroUrl + '?v=' + Date.now() + '" alt="Background" style="width:100%;max-height:200px;object-fit:cover;display:block;">'
                            + '</div>';
                        if (meta && meta.size_label) {
                            html += '<div class="text-muted mb-3" style="font-size:.78rem;">Ukuran file: <b>' + meta.size_label + '</b></div>';
                        }
                    }
                    html += '<div class="d-flex gap-2 justify-content-center flex-wrap">';
                    if (landingUrl) {
                        html += '<a href="' + landingUrl + '" target="_blank" rel="noopener" class="btn btn-sm btn-success">'
                            + '<span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle;">open_in_new</span> '
                            + 'Lihat'
                            + '</a>';
                    }
                    html += '<button type="button" class="btn btn-sm btn-outline-secondary" id="btnTetapDisini">Tutup</button>';
                    html += '</div>';

                    Swal.fire({
                        title: isCustom ? 'Foto Custom Berhasil' : 'Background Tersimpan',
                        html: html,
                        icon: 'success',
                        showConfirmButton: false,
                        showCloseButton: true,
                        width: 460,
                    });

                    Toast.fire({
                        icon: 'success',
                        title: isCustom ? 'Foto Custom Berhasil' : 'Background Tersimpan',
                    });

                    clearPendingCustom();
                } else {
                    Swal.fire('Gagal', result.msg || 'Terjadi kesalahan', 'error');
                }
            },
            error: function (xhr) {
                sedangMenyimpan = false;
                $('#simpan').prop('disabled', false).text('Simpan Pengaturan');
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = xhr.responseJSON.errors;
                    var list = Object.keys(errors)
                        .map(function (field) {
                            var label = $('[name="' + field + '"]')
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
                        var el = $('[name="' + key + '"]');
                        el.addClass('is-invalid');
                        el.closest('.input-group').addClass('is-invalid');
                    });
                } else {
                    Swal.fire('Galat', 'Cek kembali input yang anda masukkan', 'error');
                }
            }
        });
    });

    // Live preview untuk logo & favicon
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

    // Theme background picker
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

    // Toast helper untuk notifikasi pre-save
    var Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3500,
        timerProgressBar: true,
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
            title: 'Foto Custom siap',
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

            // Baca dimensi via Image object untuk preview & info,
            // TIDAK reject foto kecil - server akan resize otomatis ke 1920×1080.
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

    // Color picker: preset + Pickr-based custom (hue/saturation/lightness sliders)
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

        // Init Pickr pada swatch custom (useAsButton: true -> tidak render button inline)
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
