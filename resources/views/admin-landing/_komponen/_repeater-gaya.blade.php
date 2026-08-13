<style>
    /* === Repeater card (shared) — seragam dengan tema template ppdb-setting === */
    .lp-rep-stack {
        display: flex;
        flex-direction: column;
        gap: .85rem;
    }

    .lp-rep-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: .85rem;
        padding: 0;
        transition: border-color .15s ease, box-shadow .15s ease;
        overflow: hidden;
    }
    .lp-rep-card:hover { border-color: #cbd5e1; }
    .lp-rep-card.is-new { border-style: dashed; border-color: #94a3b8; }

    /* Toggle pill (konsisten dengan ppdb-setting) */
    .lp-rep-toggle {
        display: inline-flex; align-items: center; gap: .55rem;
        padding: .35rem .55rem .35rem .45rem;
        background: #f1f5f9; border-radius: 999px;
        cursor: pointer; user-select: none;
        transition: background .2s ease;
        border: 1px solid transparent;
    }
    .lp-rep-toggle:hover { background: #e2e8f0; }
    .lp-rep-toggle.is-on  { background: rgba(25, 135, 84, .12); border-color: rgba(25, 135, 84, .25); }
    .lp-rep-toggle.is-off { background: rgba(100, 116, 139, .12); border-color: rgba(100, 116, 139, .2); }
    .lp-rep-toggle input[type="checkbox"] { position: absolute; opacity: 0; pointer-events: none; }
    .lp-rep-toggle-track {
        position: relative; width: 34px; height: 18px;
        background: #94a3b8; border-radius: 999px;
        transition: background .2s ease; flex-shrink: 0;
    }
    .lp-rep-toggle-track::after {
        content: ""; position: absolute; top: 2px; left: 2px;
        width: 14px; height: 14px; border-radius: 50%;
        background: #fff; box-shadow: 0 1px 2px rgba(0,0,0,.2);
        transition: transform .2s ease;
    }
    .lp-rep-toggle.is-on .lp-rep-toggle-track { background: #198754; }
    .lp-rep-toggle.is-on .lp-rep-toggle-track::after { transform: translateX(16px); }
    .lp-rep-toggle-icon {
        display: inline-flex; align-items: center; justify-content: center;
        width: 18px; height: 18px; border-radius: 50%;
        font-size: 12px; flex-shrink: 0;
    }
    .lp-rep-toggle.is-on .lp-rep-toggle-icon  { background: #198754; color: #fff; }
    .lp-rep-toggle.is-off .lp-rep-toggle-icon { background: #64748b; color: #fff; }
    .lp-rep-toggle-text { font-size: .78rem; font-weight: 700; letter-spacing: .02em; }
    .lp-rep-toggle.is-on .lp-rep-toggle-text  { color: #15803d; }
    .lp-rep-toggle.is-off .lp-rep-toggle-text { color: #475569; }
    .lp-rep-toggle:focus-within { outline: 2px solid rgba(25, 135, 84, .35); outline-offset: 2px; }

    .lp-rep-head {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .85rem 1.1rem;
        border-bottom: 1px dashed #e2e8f0;
        background: #fafbfc;
    }
    .lp-rep-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        display: inline-flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        background: linear-gradient(135deg, #ede9fe, #ddd6fe);
        color: #6d28d9;
        box-shadow: 0 3px 8px -4px rgba(15, 23, 42, .12);
    }
    .lp-rep-card.is-new .lp-rep-icon {
        background: linear-gradient(135deg, #e0f2fe, #bae6fd);
        color: #0369a1;
    }
    .lp-rep-title {
        font-weight: 700;
        font-size: .98rem;
        color: #1f2937;
        margin: 0;
        line-height: 1.2;
    }
    .lp-rep-key {
        font-size: .66rem;
        font-weight: 600;
        letter-spacing: .06em;
        color: #94a3b8;
        text-transform: uppercase;
        margin-top: 2px;
    }
    .lp-rep-status { margin-left: auto; flex-shrink: 0; }

    .lp-rep-body {
        padding: .9rem 1.1rem;
        display: grid;
        grid-template-columns: 1fr;
        gap: .65rem;
    }
    @media (min-width: 768px) {
        .lp-rep-body {
            grid-template-columns: 1fr 1fr;
            gap: .75rem 1rem;
        }
        .lp-rep-body .lp-rep-full { grid-column: 1 / -1; }
    }

    .lp-rep-body .input-group { margin-bottom: 0; }
    .lp-rep-body .form-label { font-size: .78rem; font-weight: 600; color: #334155; }
    .lp-rep-body textarea.form-control {
        font-size: .88rem; line-height: 1.55;
        padding: .65rem .85rem;
        resize: vertical; min-height: 80px;
    }

    /* WYSIWYG (Quill) editor */
    .lp-rep-editor {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: .55rem;
    }
    .lp-rep-editor .ql-toolbar.ql-snow {
        border: none;
        border-bottom: 1px solid #e2e8f0;
        border-top-left-radius: .55rem;
        border-top-right-radius: .55rem;
        background: #f8fafc;
        padding: .35rem .5rem;
    }
    .lp-rep-editor .ql-container.ql-snow {
        border: none;
        border-bottom-left-radius: .55rem;
        border-bottom-right-radius: .55rem;
        font-family: inherit;
        font-size: .9rem;
        min-height: 140px;
    }
    .lp-rep-editor .ql-editor {
        min-height: 140px;
        padding: .65rem .85rem;
    }
    .lp-rep-editor .ql-editor.ql-blank::before {
        font-style: normal;
        color: #94a3b8;
        left: .85rem;
    }
    .lp-rep-help {
        font-size: .72rem;
        color: #94a3b8;
        margin: .15rem 0 0;
        line-height: 1.45;
    }

    /* Footer kartu */
    .lp-rep-foot {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .65rem;
        flex-wrap: wrap;
        padding: .65rem 1.1rem;
        border-top: 1px dashed #e2e8f0;
        background: #fafbfc;
    }
    .lp-rep-foot-info {
        font-size: .72rem;
        color: #94a3b8;
        display: inline-flex;
        align-items: center;
        gap: .3rem;
    }
    .lp-rep-foot-info .material-symbols-rounded { font-size: 13px; }
    .lp-rep-foot .btn {
        min-width: 110px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .35rem;
        padding: .45rem 1rem;
        border-radius: .5rem;
        font-size: .85rem;
    }
    .lp-rep-foot .btn .material-symbols-rounded { font-size: 16px; line-height: 1; }

    /* Empty state dalam repeater */
    .lp-rep-empty {
        text-align: center;
        padding: 1.5rem 1rem;
        color: #94a3b8;
    }
    .lp-rep-empty .material-symbols-rounded { font-size: 42px; opacity: .55; }

    /* Toolbar utama repeater */
    .lp-rep-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .65rem;
        flex-wrap: wrap;
        margin-top: .35rem;
        padding: .85rem 1.1rem;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: .85rem;
    }
    .lp-rep-toolbar .lp-rep-toolbar-info {
        font-size: .78rem;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
    }
    .lp-rep-toolbar .lp-rep-toolbar-actions {
        display: flex;
        gap: .45rem;
        flex-wrap: wrap;
    }
    .lp-rep-toolbar .btn {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        font-weight: 600;
    }
</style>