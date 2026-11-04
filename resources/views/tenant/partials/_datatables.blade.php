{{--
    Gaya bersama untuk tabel DataTables di konsol pusat (tenant console).

    MASALAH YANG DISOLUSIKAN
    -------------------------
    Halaman konsol pusat hanya memuat Tailwind CDN — TIDAK memuat Bootstrap 5.
    Padahal renderer "Bootstrap 5" milik DataTables menghasilkan markup:

        <div class="row">
          <div class="col-sm-12 col-md-6">… "Tampilkan N data" …</div>
          <div class="col-sm-12 col-md-6">… "Cari:" …</div>
        </div>
        <div class="row dt-row"><div class="col-sm-12">… tabel …</div></div>
        <div class="row">
          <div class="col-sm-12 col-md-5">… "Menampilkan 1–10 dari N data" …</div>
          <div class="col-sm-12 col-md-7">… paginasi …</div>
        </div>

    Karena kelas .row / .col-* / .pagination / .page-link tidak punya definisi
    (Bootstrap tidak dimuat), terjadi:

      1. "Tampilkan N data" dan "Cari:" tersusun VERTIKAL melebar penuh
         sehingga toolbar terlihat tidak rapi.
      2. Paginasi kehilangan gaya: renderer Bootstrap 5 menandai halaman aktif
         dengan kelas .active (bukan .current seperti renderer lama) sehingga
         tombol aktif tidak pernah diberi warna, dan <a class="page-link">
         tampil seperti link biru bawaan browser.

    Partial ini meniru SEMUA yang dibutuhkan renderer Bootstrap 5 (grid +
    pagination) TANPA memuat Bootstrap, sehingga tidak bentrok dengan Tailwind.

    Dua bentuk DOM paginasi ditangani sekaligus:
      - Renderer Bootstrap 5 : <ul class="pagination"><li class="paginate_button
        page-item active"><a class="page-link">1</a></li></ul>
      - Renderer biasa        : <a class="paginate_button current">1</a>

    CARA PAKAI: sertakan partial ini SETELAH <style> bawaan halaman supaya
    aturan di sini (yang memakai !important) menang atas aturan lama.
--}}
<style>
    /* ================================================================
       1. Emulasi grid Bootstrap 5 (versi minimal)
       ================================================================ */
    .dataTables_wrapper > .row {
        display: flex;
        flex-wrap: wrap;
        margin: 0;
    }
    .dataTables_wrapper > .row > [class*="col-"] {
        flex: 0 0 100%;
        max-width: 100%;
        min-width: 0;
        padding: 0;
    }
    @media (min-width: 768px) {
        .dataTables_wrapper > .row > .col-md-5 { flex: 0 0 41.666667%; max-width: 41.666667%; }
        .dataTables_wrapper > .row > .col-md-6 { flex: 0 0 50%; max-width: 50%; }
        .dataTables_wrapper > .row > .col-md-7 { flex: 0 0 58.333333%; max-width: 58.333333%; }
    }

    /* ================================================================
       2. "Tampilkan N data"
       ================================================================ */
    .dataTables_wrapper .dataTables_length {
        margin: 0 0 .75rem;
        text-align: left;
    }
    .dataTables_wrapper .dataTables_length label {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        margin: 0;
        color: #475569;
        font-size: .8125rem;
        font-weight: 500;
        white-space: nowrap;
    }
    .dataTables_wrapper .dataTables_length select {
        box-sizing: border-box;
        appearance: none;
        -webkit-appearance: none;
        width: auto;
        min-width: 4.75rem;
        margin: 0;
        padding: .4rem 2rem .4rem .7rem !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: .625rem !important;
        background-color: #fff;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3e%3cpath d='M19 9l-7 7-7-7'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right .55rem center;
        background-size: 1rem;
        color: #334155 !important;
        font-size: .8125rem !important;
        font-weight: 600 !important;
        line-height: 1.4;
        cursor: pointer;
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    .dataTables_wrapper .dataTables_length select:focus {
        outline: none !important;
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, .15) !important;
    }

    /* ================================================================
       3. "Cari:"
       ================================================================ */
    .dataTables_wrapper .dataTables_filter {
        margin: 0 0 .75rem;
        text-align: right;
    }
    .dataTables_wrapper .dataTables_filter label {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        margin: 0;
        color: #475569;
        font-size: .8125rem;
        font-weight: 500;
        white-space: nowrap;
    }
    .dataTables_wrapper .dataTables_filter input[type="search"] {
        box-sizing: border-box;
        display: inline-block;
        width: 15rem;
        max-width: 100%;
        height: 2.35rem;
        margin: 0 !important;
        padding: .45rem .7rem .45rem 2.15rem !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: .625rem !important;
        background-color: #fff;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3ccircle cx='11' cy='11' r='7'/%3e%3cpath d='M20 20l-3.5-3.5'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: left .65rem center;
        background-size: 1rem;
        color: #1e293b !important;
        font-size: .8125rem !important;
        box-shadow: none !important;
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    .dataTables_wrapper .dataTables_filter input[type="search"]::placeholder { color: #94a3b8; }
    .dataTables_wrapper .dataTables_filter input[type="search"]:focus {
        outline: none !important;
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, .15) !important;
    }
    /* Sembunyikan tombol "x" bawaan browser (Chrome) */
    .dataTables_wrapper .dataTables_filter input[type="search"]::-webkit-search-cancel-button {
        -webkit-appearance: none;
        appearance: none;
    }

    /* ================================================================
       4. Keterangan jumlah data
       ================================================================ */
    .dataTables_wrapper .dataTables_info {
        margin: 0;
        padding: .25rem 0 0;
        color: #64748b !important;
        font-size: .8125rem;
    }

    /* ================================================================
       5. Paginasi (menutupi kedua bentuk DOM)
       ================================================================ */
    .dataTables_wrapper .dataTables_paginate {
        margin: 0;
        padding: .25rem 0 0;
        text-align: right;
    }
    .dataTables_wrapper .dataTables_paginate ul.pagination {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-end;
        gap: .25rem;
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .dataTables_wrapper .dataTables_paginate li.paginate_button,
    .dataTables_wrapper .dataTables_paginate a.paginate_button {
        box-sizing: border-box;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2.25rem;
        height: 2.25rem;
        margin: 0 !important;
        padding: 0 !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: .5rem !important;
        background: #fff !important;
        color: #475569 !important;
        font-size: .8125rem !important;
        font-weight: 600 !important;
        line-height: 1;
        list-style: none;
        text-decoration: none !important;
        cursor: pointer;
        transition: all .15s ease;
    }
    /* Link di dalam tombol paginasi dibuat transparan agar gaya tombol utuh
       (renderer Bootstrap 5 memakai <li class="paginate_button"><a class="page-link">). */
    .dataTables_wrapper .dataTables_paginate li.paginate_button > a,
    .dataTables_wrapper .dataTables_paginate li.paginate_button > a.page-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        margin: 0 !important;
        padding: 0 !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: none !important;
        box-shadow: none !important;
        color: inherit !important;
        font: inherit;
        line-height: 1;
        text-decoration: none !important;
    }
    .dataTables_wrapper .dataTables_paginate li.paginate_button:not(.disabled):not(.active):hover,
    .dataTables_wrapper .dataTables_paginate a.paginate_button:not(.disabled):not(.current):hover {
        background: #eef2ff !important;
        border-color: #c7d2fe !important;
        color: #4f46e5 !important;
    }
    /* Halaman aktif: renderer Bootstrap 5 memakai .active, renderer lama .current. */
    .dataTables_wrapper .dataTables_paginate li.paginate_button.active,
    .dataTables_wrapper .dataTables_paginate li.paginate_button.current,
    .dataTables_wrapper .dataTables_paginate a.paginate_button.current {
        background: #4f46e5 !important;
        border-color: #4f46e5 !important;
        color: #fff !important;
        box-shadow: 0 1px 2px rgba(79, 70, 229, .25);
    }
    .dataTables_wrapper .dataTables_paginate li.paginate_button.disabled,
    .dataTables_wrapper .dataTables_paginate a.paginate_button.disabled {
        background: #f8fafc !important;
        border-color: #f1f5f9 !important;
        color: #cbd5e1 !important;
        cursor: not-allowed;
    }

    /* ================================================================
       6. Layar kecil
       ================================================================ */
    @media (max-width: 767px) {
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter { text-align: left !important; }
        .dataTables_wrapper .dataTables_info { text-align: left !important; }
        .dataTables_wrapper .dataTables_paginate { text-align: center !important; }
        .dataTables_wrapper .dataTables_paginate ul.pagination { justify-content: center; }
        .dataTables_wrapper .dataTables_filter label { width: 100%; }
        .dataTables_wrapper .dataTables_filter input[type="search"] { width: 100%; }
    }
    @media (max-width: 480px) {
        .dataTables_wrapper .dataTables_paginate li.paginate_button,
        .dataTables_wrapper .dataTables_paginate a.paginate_button {
            min-width: 2rem;
            height: 2rem;
            font-size: .75rem !important;
        }
    }
</style>
