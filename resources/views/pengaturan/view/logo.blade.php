<div class="card-body">
    <form action="/app/pengaturan/logo/{{ $profil->id }}"
      id="FormLogo"
      method="post"
      enctype="multipart/form-data">
    @csrf
    @method('PUT')

    @php
        $logoSrc = $profil->logo ? \App\Models\Profil::tenantStorageUrl('logo/' . $profil->logo, $profil->logo) : '';
    @endphp

    <div class="row g-4 align-items-stretch">
        <div class="col-lg-5">
            <input type="file" id="logoInput" name="logo" accept="image/png,image/jpeg,image/jpg,image/webp" class="d-none">
            <label for="logoInput" id="uploadZone" class="logo-zone {{ $logoSrc ? 'has-image' : '' }}" tabindex="0" role="button">
                <div class="logo-empty">
                    <div class="logo-icon"><i class="fas fa-cloud-arrow-up"></i></div>
                    <div class="fw-semibold mb-1">Klik atau seret logo ke sini</div>
                    <div class="text-xs text-muted">PNG, JPG, JPEG, WebP • Maks 2MB</div>
                </div>
                <img id="previewLogo"
                     src="{{ $logoSrc }}"
                     alt="Logo Lembaga"
                     loading="lazy"
                     decoding="async"
                     width="512" height="512">
                <div class="logo-overlay">
                    <div class="text-center">
                        <i class="fas fa-camera fa-2x mb-2"></i>
                        <div class="fw-semibold">Ganti Logo</div>
                    </div>
                </div>
            </label>
        </div>

        <div class="col-lg-7 d-flex flex-column">
            <div class="logo-info mb-3">
                <div class="logo-info-label">Rekomendasi</div>
                <ul class="logo-info-list">
                    <li><i class="fas fa-check-circle text-success me-1"></i> Format terbaik: <b>PNG transparan</b> atau <b>WebP</b></li>
                    <li><i class="fas fa-check-circle text-success me-1"></i> Ukuran ideal: <b>512×512 px</b> (persegi)</li>
                    <li><i class="fas fa-check-circle text-success me-1"></i> Disimpan terisolasi per tenant</li>
                    <li><i class="fas fa-check-circle text-success me-1"></i> Digunakan di kwitansi &amp; laporan</li>
                </ul>
            </div>

            <div class="mt-auto d-flex justify-content-end align-items-center gap-2">
                <button class="btn bg-gradient-success px-4 mb-0" type="submit" id="SimpanLogo">
                    <i class="fas fa-cloud-arrow-up me-1"></i> Unggah &amp; Simpan
                </button>
            </div>
        </div>
    </div>
    </form>
</div>

<style>
    .logo-zone {
        position: relative;
        width: 100%;
        aspect-ratio: 1 / 1;
        max-height: 320px;
        border: 2px dashed #cbd5e1;
        border-radius: 16px;
        cursor: pointer;
        overflow: hidden;
        background: repeating-linear-gradient(45deg, #f8fafc 0 10px, #fff 10px 20px);
        transition: border-color .2s ease, box-shadow .2s ease;
        display: flex; align-items: center; justify-content: center;
    }
    .logo-zone:hover, .logo-zone:focus-visible {
        border-color: #37d17c; box-shadow: 0 0 0 4px rgba(55,209,124,.15); outline: none;
    }
    .logo-zone img {
        width: 100%; height: 100%; object-fit: contain;
        background: #fff; padding: 12px; display: none;
    }
    .logo-zone.has-image img { display: block; }
    .logo-zone.has-image .logo-empty { display: none; }

    .logo-empty { text-align: center; color: #475569; padding: 16px; }
    .logo-icon {
        width: 56px; height: 56px; border-radius: 50%;
        background: rgba(55,209,124,.12); color: #37d17c;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px; margin: 0 auto 12px;
    }

    .logo-overlay {
        position: absolute; inset: 0;
        background: rgba(15,23,42,.55); color: #fff;
        display: flex; align-items: center; justify-content: center;
        opacity: 0; transition: opacity .2s ease; pointer-events: none;
    }
    .logo-zone.has-image:hover .logo-overlay,
    .logo-zone:focus-within .logo-overlay { opacity: 1; }

    .logo-info {
        background: linear-gradient(135deg, #f0fdf4 0%, #ecfeff 100%);
        border: 1px solid rgba(55,209,124,.25);
        border-radius: 12px; padding: 14px 16px;
    }
    .logo-info-label {
        font-size: 11px; text-transform: uppercase; letter-spacing: .08em;
        color: #0f766e; font-weight: 700; margin-bottom: 6px;
    }
    .logo-info-list { list-style: none; padding: 0; margin: 0; font-size: 13px; color: #334155; }
    .logo-info-list li { padding: 2px 0; }
</style>

<script>
(function () {
    const zone = document.getElementById('uploadZone');
    const input = document.getElementById('logoInput');
    const preview = document.getElementById('previewLogo');
    if (!zone || !input || !preview) return;

    const showPreview = (file) => {
        if (!file || !file.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            preview.src = e.target.result;
            zone.classList.add('has-image');
        };
        reader.readAsDataURL(file);
    };

    input.addEventListener('change', (e) => showPreview(e.target.files[0]));

    ['dragenter', 'dragover'].forEach((ev) =>
        zone.addEventListener(ev, (e) => { e.preventDefault(); zone.classList.add('drag-over'); })
    );
    ['dragleave', 'drop'].forEach((ev) =>
        zone.addEventListener(ev, (e) => { e.preventDefault(); zone.classList.remove('drag-over'); })
    );
    zone.addEventListener('drop', (e) => {
        const f = e.dataTransfer.files[0];
        if (f) {
            const dt = new DataTransfer();
            dt.items.add(f);
            input.files = dt.files;
            showPreview(f);
        }
    });
    zone.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); input.click(); }
    });
})();
</script>
