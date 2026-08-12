@extends('landing.tata-letak')

@section('title', 'PPDB ' . ($setting->school_name ?? 'Sekolah'))

@section('style')
<style>
    /* ====== Hero strip ====== */
    .lp-ppdb-hero {
        position: relative;
        background: linear-gradient(135deg, rgba(var(--lp-primary-rgb), 0.92), rgba(6, 182, 212, 0.88));
        color: #fff;
        padding: 5rem 0 4.5rem;
        overflow: hidden;
        isolation: isolate;
    }
    .lp-ppdb-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background-image: radial-gradient(circle at 20% 30%, rgba(255, 255, 255, 0.12) 0%, transparent 40%),
                          radial-gradient(circle at 80% 70%, rgba(255, 255, 255, 0.08) 0%, transparent 40%);
        z-index: -1;
    }
    .lp-ppdb-hero h1 {
        font-weight: 800;
        font-size: clamp(1.85rem, 3.6vw, 2.6rem);
        line-height: 1.15;
        margin: 2.5rem 0 0.85rem;
        letter-spacing: -0.02em;
    }
    .lp-ppdb-hero p {
        opacity: 0.95;
        font-size: 1.02rem;
        max-width: 640px;
        margin: 0 auto 1.75rem;
    }
    .lp-ppdb-cta {
        display: inline-flex;
        gap: 0.7rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    /* ====== Layout 2 kolom ====== */
    .lp-ppdb-wrap {
        display: grid;
        grid-template-columns: 260px 1fr;
        gap: 2rem;
        align-items: start;
    }
    @media (max-width: 991.98px) {
        .lp-ppdb-wrap { grid-template-columns: 1fr; }
    }

    /* ====== Sidebar ====== */
    .lp-ppdb-side {
        position: sticky;
        top: 110px;
        background: #ffffff;
        border: 1px solid rgba(15, 23, 42, 0.06);
        border-radius: var(--lp-radius-lg);
        box-shadow: var(--lp-shadow);
        padding: 0.75rem;
    }
    @media (max-width: 991.98px) {
        .lp-ppdb-side { position: static; }
    }
    .lp-ppdb-side a {
        display: flex;
        align-items: center;
        gap: 0.7rem;
        padding: 0.7rem 0.85rem;
        border-radius: var(--lp-radius-md);
        color: #334155;
        font-weight: 500;
        font-size: 0.92rem;
        transition: background 0.2s ease, color 0.2s ease;
        text-decoration: none;
    }
    .lp-ppdb-side a:hover {
        background: rgba(var(--lp-primary-rgb), 0.06);
        color: var(--lp-primary);
    }
    .lp-ppdb-side a.active {
        background: var(--lp-primary);
        color: #fff;
        box-shadow: 0 6px 18px rgba(var(--lp-primary-rgb), 0.25);
    }
    .lp-ppdb-side a i { font-size: 1rem; }

    /* ====== Content area ====== */
    .lp-ppdb-content {
        background: transparent;
    }
    .lp-ppdb-card {
        background: #ffffff;
        border: 1px solid rgba(15, 23, 42, 0.06);
        border-radius: var(--lp-radius-lg);
        box-shadow: var(--lp-shadow);
        padding: 1.5rem 1.5rem;
        margin-bottom: 1.25rem;
    }
    .lp-ppdb-card h5 {
        font-weight: 700;
        margin-bottom: 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.55rem;
    }
    .lp-ppdb-card h5 .bi {
        color: var(--lp-primary);
        font-size: 1.1rem;
    }
    .lp-ppdb-list {
        margin: 0;
        padding-left: 1.25rem;
        color: #334155;
    }
    .lp-ppdb-list li { margin-bottom: 0.5rem; line-height: 1.6; }

    /* ====== Alur steps ====== */
    .lp-ppdb-step {
        background: #ffffff;
        border: 1px solid rgba(15, 23, 42, 0.06);
        border-radius: var(--lp-radius-lg);
        box-shadow: var(--lp-shadow);
        padding: 1.25rem 1.25rem 1.25rem 1.5rem;
        display: flex;
        gap: 1rem;
        align-items: flex-start;
    }
    .lp-ppdb-step-num {
        flex-shrink: 0;
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--lp-primary), var(--lp-accent-2));
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1rem;
        box-shadow: 0 6px 16px rgba(var(--lp-primary-rgb), 0.3);
    }
    .lp-ppdb-step h6 {
        font-weight: 700;
        margin: 0 0 0.25rem;
        font-size: 1rem;
    }
    .lp-ppdb-step p {
        margin: 0;
        color: var(--lp-muted);
        font-size: 0.92rem;
        line-height: 1.55;
    }

    /* ====== Tabel jadwal ====== */
    .lp-ppdb-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        background: #fff;
        border: 1px solid rgba(15, 23, 42, 0.06);
        border-radius: var(--lp-radius-lg);
        overflow: hidden;
    }
    .lp-ppdb-table th, .lp-ppdb-table td {
        padding: 0.85rem 1rem;
        text-align: left;
        font-size: 0.92rem;
        border-bottom: 1px solid rgba(15, 23, 42, 0.05);
    }
    .lp-ppdb-table th {
        background: rgba(var(--lp-primary-rgb), 0.05);
        font-weight: 700;
        color: var(--lp-text);
    }
    .lp-ppdb-table tbody tr:last-child td { border-bottom: 0; }
    .lp-ppdb-table tbody tr:hover { background: rgba(var(--lp-primary-rgb), 0.03); }

    /* ====== FAQ ====== */
    .lp-faq-item {
        background: #fff;
        border: 1px solid rgba(15, 23, 42, 0.06);
        border-radius: var(--lp-radius-md);
        margin-bottom: 0.6rem;
        overflow: hidden;
        transition: box-shadow 0.2s ease, border-color 0.2s ease;
    }
    .lp-faq-item.is-open {
        border-color: rgba(var(--lp-primary-rgb), 0.25);
        box-shadow: 0 6px 18px rgba(var(--lp-primary-rgb), 0.08);
    }
    .lp-faq-q {
        width: 100%;
        background: transparent;
        border: 0;
        text-align: left;
        padding: 0.95rem 1.1rem;
        font-weight: 600;
        font-size: 0.95rem;
        color: var(--lp-text);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.75rem;
        cursor: pointer;
    }
    .lp-faq-q .bi { transition: transform 0.2s ease; color: var(--lp-primary); }
    .lp-faq-item.is-open .lp-faq-q .bi { transform: rotate(180deg); }
    .lp-faq-a {
        padding: 0 1.1rem 1rem;
        color: var(--lp-muted);
        font-size: 0.93rem;
        line-height: 1.65;
        display: none;
    }
    .lp-faq-item.is-open .lp-faq-a { display: block; }

    /* CTA bawah di halaman PPDB: versi kompak dari strip CTA home,
       dengan padding lebih kecil agar proporsional di dalam card FAQ. */
    .lp-ppdb-cta {
        padding: 2rem 2rem !important;
        border-radius: 22px !important;
    }
    .lp-ppdb-cta h3 {
        font-size: clamp(1.2rem, 2vw, 1.5rem) !important;
        line-height: 1.25;
    }
    .lp-ppdb-cta p {
        font-size: .95rem;
        opacity: .92;
        max-width: 520px;
    }
    .lp-ppdb-cta-actions {
        max-width: 280px;
    }
    .lp-ppdb-cta-actions .lp-cta-btn {
        padding: .75rem 1.5rem;
        font-size: .92rem;
    }
    .lp-ppdb-cta-actions .lp-cta-btn-outline {
        padding: .7rem 1.4rem;
        font-size: .88rem;
    }
    .lp-ppdb-cta-actions .lp-cta-meta {
        font-size: .72rem;
    }
    @media (max-width: 991.98px) {
        .lp-ppdb-cta-actions { margin: 0 auto; }
    }
    @media (max-width: 767.98px) {
        .lp-ppdb-cta { padding: 1.75rem 1.5rem !important; }
        .lp-ppdb-cta-actions { max-width: 100%; }
    }
</style>
@endsection

@section('content')

{{-- ====== Hero PPDB ====== --}}
<section class="lp-ppdb-hero">
    <div class="container text-center">
        <h1 class="lp-reveal" data-from="zoom">{{ $ppdb->title ?? 'Penerimaan Peserta Didik Baru' }}</h1>
        <p class="lp-reveal" data-delay="1">
            {{ $ppdb->subtitle ?? 'Mari bergabung bersama kami wujudkan pendidikan berkualitas.' }}
        </p>
        <div class="lp-ppdb-cta lp-reveal" data-delay="2">
            <a href="{{ $ppdb->cta_url ?: route('landing.kontak') }}" class="lp-btn-light">
                <i class="bi bi-pencil-square"></i>
                {{ $ppdb->cta_text ?? 'Formulir Pendaftaran Online' }}
            </a>
            <a href="{{ $ppdb->secondary_url ?: route('landing.kontak') }}" class="lp-btn-outline-light">
                <i class="bi bi-telephone"></i>
                {{ $ppdb->secondary_text ?? 'Kontak Kami' }}
            </a>
        </div>
    </div>
</section>

{{-- ====== Konten 2 kolom ====== --}}
<section class="lp-section">
    <div class="container">
        <div class="lp-ppdb-wrap">

            {{-- Sidebar --}}
            <aside class="lp-ppdb-side lp-reveal" data-from="left">
                <a href="#pendaftaran" class="{{ $active === 'pendaftaran' ? 'active' : '' }}" data-section="pendaftaran">
                    <i class="bi bi-megaphone-fill"></i> Pendaftaran
                </a>
                <a href="#persyaratan" class="{{ $active === 'persyaratan' ? 'active' : '' }}" data-section="persyaratan">
                    <i class="bi bi-card-checklist"></i> Persyaratan
                </a>
                <a href="#alur" class="{{ $active === 'alur' ? 'active' : '' }}" data-section="alur">
                    <i class="bi bi-diagram-3-fill"></i> Alur Pendaftaran
                </a>
                <a href="#jadwal" class="{{ $active === 'jadwal' ? 'active' : '' }}" data-section="jadwal">
                    <i class="bi bi-calendar2-week-fill"></i> Jadwal &amp; Biaya
                </a>
                <a href="#faq" class="{{ $active === 'faq' ? 'active' : '' }}" data-section="faq">
                    <i class="bi bi-question-circle-fill"></i> Pertanyaan
                </a>
            </aside>

            {{-- Konten --}}
            <div class="lp-ppdb-content">

                {{-- Pendaftaran --}}
                <div id="pendaftaran" class="lp-ppdb-card lp-reveal" data-from="zoom">
                    <h5><i class="bi bi-megaphone-fill"></i> Pendaftaran</h5>
                    <p class="text-muted mb-2">
                        Pendaftaran Peserta Didik Baru {{ $ppdb->school_name ?: ($setting->school_name ?? '') }}
                        Tahun Ajaran {{ date('Y') }}/{{ date('Y') + 1 }} telah dibuka.
                        Silakan pilih gelombang pendaftaran yang tersedia dan lengkapi dokumen sesuai persyaratan.
                    </p>
                    <p class="text-muted mb-0">
                        Klik tombol "Formulir Pendaftaran Online" di atas untuk memulai pendaftaran,
                        atau hubungi panitia PPDB untuk konsultasi terlebih dahulu.
                    </p>
                </div>

                {{-- Persyaratan --}}
                <div id="persyaratan" class="lp-ppdb-card lp-reveal" data-from="zoom">
                    <h5><i class="bi bi-card-checklist"></i> Persyaratan Pendaftaran</h5>
                    @forelse ($requirements as $req)
                        <h6 class="fw-bold mt-3 mb-2" style="font-size:0.95rem; color: var(--lp-text);">
                            {{ $req->title }}
                        </h6>
                        <ul class="lp-ppdb-list">
                            @foreach ($req->items_list as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    @empty
                        <p class="text-muted mb-0">Belum ada data persyaratan.</p>
                    @endforelse
                </div>

                {{-- Alur --}}
                <div id="alur" class="lp-ppdb-card lp-reveal" data-from="zoom">
                    <h5><i class="bi bi-diagram-3-fill"></i> Alur Pendaftaran</h5>
                    <div class="d-flex flex-column gap-3 mt-2">
                        @forelse ($stages as $i => $stage)
                            <div class="lp-ppdb-step">
                                <div class="lp-ppdb-step-num">{{ $i + 1 }}</div>
                                <div>
                                    <h6>{{ $stage->title }}</h6>
                                    <p>{{ $stage->description }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">Belum ada data alur pendaftaran.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Jadwal --}}
                <div id="jadwal" class="lp-ppdb-card lp-reveal" data-from="zoom">
                    <h5><i class="bi bi-calendar2-week-fill"></i> Jadwal &amp; Biaya Pendidikan</h5>
                    @if ($schedules->isNotEmpty())
                        <div class="table-responsive mt-2">
                            <table class="lp-ppdb-table">
                                <thead>
                                    <tr>
                                        <th>Gelombang</th>
                                        <th>Periode</th>
                                        <th>Biaya Pendaftaran</th>
                                        <th>SPP / Bulan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($schedules as $s)
                                        <tr>
                                            <td><strong>{{ $s->gelombang }}</strong></td>
                                            <td>
                                                {{ $s->start_date?->translatedFormat('d M Y') }}
                                                &ndash;
                                                {{ $s->end_date?->translatedFormat('d M Y') }}
                                            </td>
                                            <td>{{ $s->biaya_daftar ?? '-' }}</td>
                                            <td>{{ $s->spp_bulanan ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">Belum ada jadwal gelombang.</p>
                    @endif
                </div>

                {{-- FAQ --}}
                <div id="faq" class="lp-ppdb-card lp-reveal" data-from="zoom">
                    <h5><i class="bi bi-question-circle-fill"></i> Pertanyaan Sering Diajukan</h5>
                    @forelse ($faqs as $i => $faq)
                        <div class="lp-faq-item {{ $i === 0 ? 'is-open' : '' }}">
                            <button type="button" class="lp-faq-q" data-faq-toggle>
                                <span>{{ $faq->question }}</span>
                                <i class="bi bi-chevron-down"></i>
                            </button>
                            <div class="lp-faq-a">
                                {!! $faq->answer !!}
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Belum ada FAQ.</p>
                    @endforelse
                </div>

                {{-- CTA bawah --}}
                <div class="lp-cta-strip mt-4 lp-reveal lp-ppdb-cta" data-from="zoom">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-7">
                            <span class="lp-cta-eyebrow">
                                <i class="bi bi-megaphone-fill"></i> PPDB {{ date('Y') }}/{{ date('Y') + 1 }}
                            </span>
                            <h3 class="mb-2">Siap mendaftarkan putra/putri Anda?</h3>
                            <p class="mb-0">
                                Tim PPDB siap membantu Anda. Hubungi kami atau mulai pendaftaran online sekarang.
                            </p>
                        </div>
                        <div class="col-lg-5">
                            <div class="lp-cta-actions lp-ppdb-cta-actions">
                                <a href="{{ $ppdb->cta_url ?: route('landing.kontak') }}" class="lp-cta-btn">
                                    <span>Mulai Pendaftaran Online</span>
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                                <a href="{{ route('landing.kontak') }}" class="lp-cta-btn-outline">
                                    <i class="bi bi-telephone"></i> Hubungi Tim PPDB
                                </a>
                                <div class="lp-cta-meta">
                                    <i class="bi bi-shield-check"></i> Konsultasi gratis sebelum mendaftar
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

@endsection

@section('script')
<script>
(function () {
    // FAQ accordion
    document.querySelectorAll('[data-faq-toggle]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var item = btn.closest('.lp-faq-item');
            if (item) item.classList.toggle('is-open');
        });
    });

    // Sidebar: klik → smooth scroll + set hash + active state
    var sideLinks = document.querySelectorAll('.lp-ppdb-side a[data-section]');
    var sections  = ['pendaftaran', 'persyaratan', 'alur', 'jadwal', 'faq']
        .map(function (id) { return document.getElementById(id); })
        .filter(Boolean);

    function setActive(id) {
        sideLinks.forEach(function (a) {
            a.classList.toggle('active', a.getAttribute('data-section') === id);
        });
    }

    sideLinks.forEach(function (a) {
        a.addEventListener('click', function (e) {
            var id = a.getAttribute('data-section');
            var el = document.getElementById(id);
            if (!el) return;
            e.preventDefault();
            var top = el.getBoundingClientRect().top + window.scrollY - 110;
            window.scrollTo({ top: top, behavior: 'smooth' });
            history.replaceState(null, '', '#' + id);
            setActive(id);
        });
    });

    // Highlight section aktif saat scroll
    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    setActive(entry.target.id);
                }
            });
        }, { rootMargin: '-30% 0px -55% 0px' });

        sections.forEach(function (el) { io.observe(el); });
    }
})();
</script>
@endsection
