<style>
    .lp-page-header {
        gap: .75rem;
    }
    .lp-page-eyebrow {
        font-size: .72rem;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #94a3b8;
        font-weight: 600;
    }
    .lp-page-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
    }
    @media (min-width: 768px) {
        .lp-page-title { font-size: 1.25rem; }
    }

    .lp-status-badge {
        padding: .25rem .55rem;
        border-radius: 50rem;
        font-size: .7rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: .25rem;
    }
    .lp-status-badge::before {
        content: "";
        width: 6px; height: 6px;
        border-radius: 50%;
        background: currentColor;
        display: inline-block;
    }
    .lp-status-badge.is-published { background: rgba(55,209,124,.12); color: #1f9d57; }
    .lp-status-badge.is-draft     { background: rgba(100,116,139,.15); color: #475569; }
    .lp-status-badge.is-active    { background: rgba(55,209,124,.12); color: #1f9d57; }
    .lp-status-badge.is-inactive  { background: rgba(100,116,139,.15); color: #475569; }
    .lp-status-badge.is-featured  { background: rgba(245,158,11,.15); color: #b45309; }
    /* Status pesan kontak: baru/dibaca/selesai */
    .lp-status-badge.is-new        { background: rgba(37,99,235,.12);  color: #1d4ed8; }
    .lp-status-badge.is-read       { background: rgba(245,158,11,.15); color: #b45309; }
    .lp-status-badge.is-finished   { background: rgba(55,209,124,.12); color: #15803d; }

    .lp-thumb {
        width: 56px; height: 42px;
        border-radius: .35rem;
        object-fit: cover;
        background: #f1f5f9;
        display: block;
    }
    .lp-thumb-empty {
        width: 56px; height: 42px;
        border-radius: .35rem;
        background: #f1f5f9;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
    }

    .lp-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: .25rem;
        background: #f1f5f9;
        padding: .35rem;
        border-radius: .75rem;
        margin-bottom: 1rem;
    }
    .lp-tab {
        flex: 1 1 auto;
        min-width: 120px;
        padding: .55rem .9rem;
        border-radius: .55rem;
        background: transparent;
        border: none;
        color: #475569;
        font-weight: 600;
        font-size: .85rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .4rem;
        cursor: pointer;
        transition: all .15s ease;
    }
    .lp-tab:hover { color: #1f2937; }
    .lp-tab.is-active {
        background: #fff;
        color: #1f9d57;
        box-shadow: 0 2px 6px rgba(15,23,42,.06);
    }
    .lp-tab .material-symbols-rounded { font-size: 18px; }

    .lp-input-group {
        display: flex;
        flex-direction: column;
        gap: .35rem;
    }
    .lp-input-group > label {
        font-size: .78rem;
        font-weight: 600;
        color: #334155;
        margin: 0;
    }
    .lp-input-group .form-text {
        font-size: .72rem;
        color: #94a3b8;
    }

    .lp-gallery-card {
        transition: transform .15s ease, box-shadow .15s ease;
        border: 1px solid #e2e8f0 !important;
        background: #fff;
        border-radius: .75rem !important;
        overflow: hidden;
    }
    .lp-gallery-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 18px rgba(15,23,42,.08) !important;
    }
    .lp-gallery-card img {
        aspect-ratio: 1;
        object-fit: cover;
        width: 100%;
    }

    .lp-row-card {
        border: 1px solid #e2e8f0 !important;
        background: #fff;
        border-radius: .75rem !important;
        padding: .85rem;
        margin-bottom: .65rem;
    }

    .lp-empty {
        text-align: center;
        padding: 2.5rem 1rem;
        color: #94a3b8;
    }
    .lp-empty .material-symbols-rounded {
        font-size: 48px;
        opacity: .55;
    }

    /* Chip kategori kecil (dipakai daftar artikel/berita) */
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

    /* Sel tabel: judul + excerpt + slug (dipakai halaman artikel/berita) */
    .lp-row-title {
        font-weight: 600;
        color: #1f2937;
        line-height: 1.3;
    }
    .lp-row-excerpt {
        font-size: .78rem;
        color: #64748b;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin-top: .2rem;
    }
    .lp-row-slug {
        font-size: .7rem;
        color: #94a3b8;
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        margin-top: .15rem;
    }
    .lp-featured-star {
        color: #f59e0b;
        font-size: 18px;
        vertical-align: middle;
    }

    /* Aksi tombol icon (32x32) konsisten di seluruh tabel admin-landing */
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

    /* Thumbnail galeri (72x54) konsisten di tabel daftar */
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
    .lp-gallery-title small {
        display: block;
        font-weight: 400;
        font-size: .78rem;
        color: #64748b;
        margin-top: .15rem;
    }

    /* Section heading konsisten dengan halaman Pengaturan */
    .lp-section-title {
        font-size: .95rem;
        font-weight: 700;
        margin-bottom: .25rem;
        color: #1f2937;
    }
    .lp-section-title .material-symbols-rounded {
        font-size: 20px;
        vertical-align: middle;
        margin-right: .15rem;
    }

    /* Tombol Simpan standar (info, full-width mobile) */
    .lp-save-bar {
        margin-top: .5rem;
    }

    /* Style upload preview seperti halaman Pengaturan */
    .lp-preview-box {
        width: 100%;
        aspect-ratio: 1 / 1;
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
        position: relative;
    }
    .lp-preview-box:hover {
        border-color: #37d17c;
        background: #fff;
        transform: translateY(-1px);
    }
    .lp-preview-box img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        pointer-events: none;
    }
    .lp-preview-empty {
        font-size: 42px;
        color: #94a3b8;
        pointer-events: none;
    }
    .lp-preview-box.has-image .lp-preview-empty,
    .lp-preview-box.has-image .lp-preview-hint {
        display: none;
    }
    .lp-preview-box.has-image::after {
        content: 'Klik untuk ganti foto';
        position: absolute;
        left: 50%;
        bottom: .75rem;
        transform: translateX(-50%);
        background: rgba(15,23,42,.78);
        color: #fff;
        font-size: .75rem;
        font-weight: 500;
        padding: .25rem .65rem;
        border-radius: 999px;
        opacity: 0;
        transition: opacity .15s ease;
        pointer-events: none;
        z-index: 2;
    }
    .lp-preview-box.has-image:hover::after {
        opacity: 1;
    }
    .lp-preview-hint {
        font-size: .75rem;
        color: #64748b;
        font-weight: 500;
        pointer-events: none;
        background: rgba(255,255,255,.85);
        padding: .15rem .55rem;
        border-radius: 999px;
        position: relative;
        z-index: 1;
    }
    .lp-preview-box:hover .lp-preview-hint {
        color: #1f9d57;
    }

    /* ============================================================
       Layout Halaman Pengaturan (pola seperti /app/pengaturan/sop):
         - Sidebar kiri: card daftar anchor (halaman penuh 1 card aktif)
         - Konten kanan: satu card per section, ditampilkan via :target
       ============================================================ */
    .lp-pengaturan-shell {
        --lp-accent: #37d17c;
        --lp-accent-dark: #0f9b58;
        --lp-ink: #0f172a;
        --lp-muted: #64748b;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    .lp-pengaturan-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f766e 100%);
        border-radius: 18px;
        padding: 22px 26px;
        color: #fff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 12px 32px rgba(15, 23, 42, .18);
    }
    .lp-pengaturan-hero::after {
        content: "";
        position: absolute;
        right: -60px; top: -60px;
        width: 220px; height: 220px;
        background: radial-gradient(circle, rgba(55, 209, 124, .35), transparent 70%);
        pointer-events: none;
    }
    .lp-pengaturan-hero .crumb {
        font-size: 12px;
        color: #fff;
        text-transform: uppercase;
        letter-spacing: .12em;
    }
    .lp-pengaturan-hero h4 { font-weight: 700; margin: 4px 0 6px; color: #fff; }
    .lp-pengaturan-hero h4 i { color: #fff; }

    /* Sidebar card menu */
    .lp-anchor-card {
        border-radius: .75rem !important;
        border: none !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, .10), 0 2px 4px -1px rgba(0, 0, 0, .06) !important;
        overflow: hidden;
    }
    .lp-anchor-card .card-header {
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        padding: 14px 18px;
        display: flex; align-items: center; gap: 12px;
    }
    .lp-anchor-card .card-header h5 { margin: 0; font-weight: 700; color: var(--lp-ink); }
    .lp-anchor-card .card-header .sub {
        font-size: 12px; color: var(--lp-muted); margin-top: 2px;
    }
    .lp-anchor-header-icon {
        width: 32px; height: 32px;
        border-radius: 9px;
        background: linear-gradient(135deg, rgba(55, 209, 124, .15), rgba(15, 118, 110, .15));
        color: var(--lp-accent-dark);
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
    }

    /* Daftar anchor (mirip .sop-menu dari halaman SOP) */
    .lp-anchor-menu { gap: 4px !important; }
    .lp-anchor-menu .lp-anchor-link {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 6px 12px;
        color: var(--lp-ink);
        font-weight: 500;
        font-size: 13px;
        transition: all .2s ease;
        position: relative;
        text-decoration: none;
        width: 100%;
    }
    .lp-anchor-menu .lp-anchor-link .mi {
        width: 28px; height: 28px;
        border-radius: 8px;
        display: inline-flex; align-items: center; justify-content: center;
        background: #f1f5f9;
        color: #334155;
        font-size: 14px;
        transition: all .2s ease;
        flex-shrink: 0;
    }
    .lp-anchor-menu .lp-anchor-link .mi .material-symbols-rounded {
        font-size: 18px;
    }
    .lp-anchor-menu .lp-anchor-link:hover {
        border-color: var(--lp-accent);
        transform: translateY(-1px);
        box-shadow: 0 6px 14px rgba(15, 23, 42, .06);
        color: var(--lp-ink);
    }
    .lp-anchor-menu .lp-anchor-link:hover .mi {
        background: rgba(55, 209, 124, .15);
        color: var(--lp-accent-dark);
    }

    /* Active state dikontrol CSS :has(:target) — pola halaman SOP */
    .lp-pengaturan-wrapper:has(#lp-section-hero:target)       .lp-anchor-menu .lp-anchor-link[href="#lp-section-hero"],
    .lp-pengaturan-wrapper:has(#lp-section-identitas:target)  .lp-anchor-menu .lp-anchor-link[href="#lp-section-identitas"],
    .lp-pengaturan-wrapper:has(#lp-section-kontak:target)     .lp-anchor-menu .lp-anchor-link[href="#lp-section-kontak"],
    .lp-pengaturan-wrapper:has(#lp-section-medsos:target)     .lp-anchor-menu .lp-anchor-link[href="#lp-section-medsos"],
    .lp-pengaturan-wrapper:has(#lp-section-background:target) .lp-anchor-menu .lp-anchor-link[href="#lp-section-background"],
    .lp-pengaturan-wrapper:has(#lp-section-warna:target)      .lp-anchor-menu .lp-anchor-link[href="#lp-section-warna"],
    .lp-pengaturan-wrapper:has(#lp-section-sambutan:target)   .lp-anchor-menu .lp-anchor-link[href="#lp-section-sambutan"] {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #fff;
        border-color: transparent;
        box-shadow: 0 10px 22px rgba(15, 23, 42, .25);
    }
    .lp-pengaturan-wrapper:has(#lp-section-hero:target)       .lp-anchor-menu .lp-anchor-link[href="#lp-section-hero"] .mi,
    .lp-pengaturan-wrapper:has(#lp-section-identitas:target)  .lp-anchor-menu .lp-anchor-link[href="#lp-section-identitas"] .mi,
    .lp-pengaturan-wrapper:has(#lp-section-kontak:target)     .lp-anchor-menu .lp-anchor-link[href="#lp-section-kontak"] .mi,
    .lp-pengaturan-wrapper:has(#lp-section-medsos:target)     .lp-anchor-menu .lp-anchor-link[href="#lp-section-medsos"] .mi,
    .lp-pengaturan-wrapper:has(#lp-section-background:target) .lp-anchor-menu .lp-anchor-link[href="#lp-section-background"] .mi,
    .lp-pengaturan-wrapper:has(#lp-section-warna:target)      .lp-anchor-menu .lp-anchor-link[href="#lp-section-warna"] .mi,
    .lp-pengaturan-wrapper:has(#lp-section-sambutan:target)   .lp-anchor-menu .lp-anchor-link[href="#lp-section-sambutan"] .mi {
        background: rgba(255, 255, 255, .14);
        color: #fff;
    }
    .lp-pengaturan-wrapper:has(#lp-section-hero:target)       .lp-anchor-menu .lp-anchor-link[href="#lp-section-hero"]::after,
    .lp-pengaturan-wrapper:has(#lp-section-identitas:target)  .lp-anchor-menu .lp-anchor-link[href="#lp-section-identitas"]::after,
    .lp-pengaturan-wrapper:has(#lp-section-kontak:target)     .lp-anchor-menu .lp-anchor-link[href="#lp-section-kontak"]::after,
    .lp-pengaturan-wrapper:has(#lp-section-medsos:target)     .lp-anchor-menu .lp-anchor-link[href="#lp-section-medsos"]::after,
    .lp-pengaturan-wrapper:has(#lp-section-background:target) .lp-anchor-menu .lp-anchor-link[href="#lp-section-background"]::after,
    .lp-pengaturan-wrapper:has(#lp-section-warna:target)      .lp-anchor-menu .lp-anchor-link[href="#lp-section-warna"]::after,
    .lp-pengaturan-wrapper:has(#lp-section-sambutan:target)   .lp-anchor-menu .lp-anchor-link[href="#lp-section-sambutan"]::after {
        content: "";
        position: absolute;
        right: 14px; top: 50%;
        width: 8px; height: 8px;
        border-radius: 50%;
        background: var(--lp-accent);
        transform: translateY(-50%);
        box-shadow: 0 0 0 4px rgba(55, 209, 124, .25);
    }

    /* Default aktif = Hero (kalau URL tidak punya hash target yang cocok) */
    .lp-pengaturan-wrapper:not(:has(:target)) .lp-anchor-menu .lp-anchor-link[href="#lp-section-hero"] {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #fff;
        border-color: transparent;
        box-shadow: 0 10px 22px rgba(15, 23, 42, .25);
    }
    .lp-pengaturan-wrapper:not(:has(:target)) .lp-anchor-menu .lp-anchor-link[href="#lp-section-hero"] .mi {
        background: rgba(255, 255, 255, .14);
        color: #fff;
    }
    .lp-pengaturan-wrapper:not(:has(:target)) .lp-anchor-menu .lp-anchor-link[href="#lp-section-hero"]::after {
        content: "";
        position: absolute;
        right: 14px; top: 50%;
        width: 8px; height: 8px;
        border-radius: 50%;
        background: var(--lp-accent);
        transform: translateY(-50%);
        box-shadow: 0 0 0 4px rgba(55, 209, 124, .25);
    }

    /* Panel konten: satu konten tampil, sisanya hidden */
    .lp-pengaturan-content > .lp-content { display: none; }
    .lp-pengaturan-content > .lp-content:target { display: block; animation: lpFade .3s ease; }
    .lp-pengaturan-wrapper:not(:has(:target)) .lp-content#lp-section-hero { display: block; animation: lpFade .3s ease; }

    @keyframes lpFade {
        from { opacity: 0; transform: translateY(6px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* Style card di dalam konten (mirip .sop-card) */
    .lp-pengaturan-content > .lp-content > .card,
    .lp-pengaturan-content .lp-content .card {
        border-radius: .75rem !important;
        border: none !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, .10), 0 2px 4px -1px rgba(0, 0, 0, .06) !important;
        overflow: hidden;
    }
    .lp-pengaturan-content .card .card-header {
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
    }
    .lp-pengaturan-content .card .card-header h6 {
        font-weight: 700;
        color: var(--lp-ink);
        margin: 0;
    }

    /* Sticky sidebar pada flex-row yang punya tinggi sama */
    .lp-pengaturan-row { align-items: stretch; }
    .lp-pengaturan-aside-col,
    .lp-pengaturan-content-col {
        align-self: stretch;
    }
    /* Sticky di level aside col: tinggi mengikuti flex row (yang stretch
       menyamakan dengan col-content), dan tetap menempel saat scroll ke
       bawah dalam batas tinggi col-content. */
    .lp-pengaturan-aside {
        position: sticky;
        top: 1rem;
        align-self: stretch;
    }

    /* ---------- Responsive ---------- */
    @media (max-width: 991.98px) {
        .lp-pengaturan-aside {
            position: static;
            top: auto;
        }
    }
    @media (max-width: 767.98px) {
        .lp-pengaturan-hero { padding: 16px 18px; border-radius: 14px; }
        .lp-pengaturan-hero .crumb { font-size: 10px; letter-spacing: .1em; }
        .lp-pengaturan-hero h4 { font-size: 18px; margin: 2px 0; }
        .lp-pengaturan-hero::after { width: 140px; height: 140px; right: -40px; top: -40px; }

        .lp-anchor-menu .lp-anchor-link { padding: 10px 12px; font-size: 13px; }
        .lp-anchor-menu .lp-anchor-link .mi { width: 30px; height: 30px; font-size: 14px; }

        .lp-anchor-card .card-header { padding: 12px 14px; gap: 10px; }
        .lp-anchor-card .card-header h5 { font-size: 15px; }
        .lp-anchor-card .card-header .sub { font-size: 11px; }
        .lp-anchor-header-icon { width: 30px; height: 30px; font-size: 14px; }

        /* hide active dot on small screens */
        .lp-pengaturan-wrapper .lp-anchor-menu .lp-anchor-link::after { display: none !important; }
    }
    @media (max-width: 575.98px) {
        .lp-pengaturan-hero { padding: 14px 16px; }
        .lp-pengaturan-hero h4 { font-size: 17px; }
        .lp-pengaturan-content .card .card-body { padding: 14px; }
    }

    /* Input file dengan border tegas + hover/focus hijau tema */
    .lp-bordered-input {
        border: 1px solid #d0d5dd !important;
        border-radius: .5rem !important;
        padding: .55rem .85rem !important;
        background: #fff;
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    .lp-bordered-input:hover { border-color: #37d17c !important; }
    .lp-bordered-input:focus,
    .lp-bordered-input:focus-within {
        border-color: #37d17c !important;
        box-shadow: 0 0 0 .2rem rgba(55, 209, 124, .18) !important;
        outline: none !important;
    }

    /* Loading state tombol submit (anti double-click) */
    @keyframes lpSpin {
        from { transform: rotate(0deg); }
        to   { transform: rotate(360deg); }
    }
    .lp-spinner {
        display: inline-block;
        animation: lpSpin 0.85s linear infinite;
    }
    #submitBtn[disabled] {
        opacity: 0.75;
        cursor: wait !important;
        pointer-events: none;
    }
</style>
