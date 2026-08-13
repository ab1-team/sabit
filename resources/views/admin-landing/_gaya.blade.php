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
    .lp-preview-hint {
        font-size: .75rem;
        color: #64748b;
        font-weight: 500;
        pointer-events: none;
    }
    .lp-preview-box:hover .lp-preview-hint {
        color: #1f9d57;
    }
</style>
