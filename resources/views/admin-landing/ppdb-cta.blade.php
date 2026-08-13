@extends('layouts.tenant.base')

@section('style')
    @include('admin-landing._gaya')
    <style>
        .lp-cta-card { position: relative; }

        .lp-cta-card .input-group input,
        .lp-cta-card .input-group textarea {
            /* Pastikan kursor & klik input bekerja normal (Material outline +
               card nested kadang bikin pointer-events salah). */
            pointer-events: auto;
            position: relative;
            z-index: 1;
        }

        .lp-cta-foot {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: .5rem;
            margin-top: .75rem;
            padding-top: .5rem;
            border-top: 1px dashed #e2e8f0;
        }
        /* Card Konten PPDB - kecilkan ruang bawah tombol saja */
        .lp-cta-card-konten > .card-body {
            padding-bottom: .4rem;
        }
        .lp-cta-card-konten .lp-cta-foot {
            margin-top: .35rem;
            margin-bottom: -.25rem;
            padding-top: .3rem;
            padding-bottom: 0;
        }
    </style>
@endsection

@section('content')
<div class="px-2 py-2">
    @php
        $titleSlot = '<p class="text-muted small mb-0">Atur judul & paragraf Hero CTA, teks Pendaftaran, dan Konten PPDB 2026/2027 (eyebrow, judul, paragraf, tombol) yang tampil di section PPDB beranda & halaman PPDB publik. Setiap kartu punya tombol simpan sendiri.</p>';
    @endphp
    @include('admin-landing._header-halaman', [
        'subtitle' => 'Landing Page',
        'titleSlot' => $titleSlot,
    ])

    @php
        $registrationDefault = "Pendaftaran Peserta Didik Baru demo Tahun Ajaran 2026/2027 telah dibuka. Silakan pilih gelombang pendaftaran yang tersedia dan lengkapi dokumen sesuai persyaratan.\n\nKlik tombol \"Formulir Pendaftaran Online\" di atas untuk memulai pendaftaran, atau hubungi panitia PPDB untuk konsultasi terlebih dahulu.";
        $registrationValue = old('registration', $cta['registration'] ?? $registrationDefault);
        $titleValue = old('title', $cta['title'] ?? 'Penerimaan Peserta Didik Baru');
        $paragraphValue = old('paragraph', $cta['paragraph'] ?? 'Mari bergabung bersama kami wujudkan pendidikan berkualitas.');

        // Default Konten PPDB 2026/2027
        $bottomEyebrow = old('bottom_eyebrow', $ppdb->bottom_eyebrow ?? 'PPDB 2026/2027');
        $bottomTitle = old('bottom_title', $ppdb->bottom_title ?? 'Siap mendaftarkan putra/putri Anda?');
        $bottomParagraph = old('bottom_paragraph', $ppdb->bottom_paragraph ?? 'Tim PPDB siap membantu Anda. Hubungi kami atau mulai pendaftaran online sekarang.');
        $bottomPrimaryText = old('bottom_primary_text', $ppdb->bottom_primary_text ?? 'Mulai Pendaftaran Online');
        $bottomPrimaryUrl = old('bottom_primary_url', $ppdb->bottom_primary_url ?? '');
        $bottomSecondaryText = old('bottom_secondary_text', $ppdb->bottom_secondary_text ?? '');
        $bottomSecondaryUrl = old('bottom_secondary_url', $ppdb->bottom_secondary_url ?? '');
        $bottomMeta = old('bottom_meta', $ppdb->bottom_meta ?? 'Konsultasi gratis sebelum mendaftar');
    @endphp

    {{-- ============ CARD 1: HERO CTA & PENDAFTARAN ============ --}}
    <form action="{{ $action }}" method="POST" class="lp-ajax">
        @csrf
        <input type="hidden" name="section" value="hero">
        <div class="card my-3 shadow-sm lp-cta-card">
            <div class="card-body p-3">
                <div class="lp-section-title">
                    <span class="material-symbols-rounded">title</span>
                    Hero CTA & Pendaftaran
                </div>
                <p class="text-muted small mb-3">Judul, paragraf singkat, dan teks panjang pendaftaran yang tampil di section CTA beranda publik.</p>

                <div class="row">
                    <div class="col-md-12">
                        <div class="input-group input-group-outline mb-3 @if ($titleValue !== '') is-filled @endif">
                            <label class="form-label">Judul <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required maxlength="255" value="{{ $titleValue }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="input-group input-group-outline mb-3 @if ($paragraphValue !== '') is-filled @endif">
                            <label class="form-label">Paragraf</label>
                            <textarea name="paragraph" rows="3" class="form-control">{{ $paragraphValue }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="input-group input-group-outline mb-3 @if ($registrationValue !== '') is-filled @endif">
                            <label class="form-label">Konten Pendaftaran</label>
                            <textarea name="registration" rows="6" class="form-control" placeholder="Tulis teks panjang pendaftaran di sini. Baris baru akan tampil sebagai paragraf di halaman publik.">{{ $registrationValue }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="lp-cta-foot" style="margin-bottom: -.25rem; padding-bottom: 0;">
                    <button type="submit" class="btn btn-info">
                        <span class="material-symbols-rounded">save</span>
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- ============ CARD 2: KONTEN PPDB 2026/2027 ============ --}}
    <form action="{{ $action }}" method="POST" class="lp-ajax">
        @csrf
        <input type="hidden" name="section" value="konten">
        <div class="card my-3 shadow-sm lp-cta-card lp-cta-card-konten">
            <div class="card-body p-3">
                <div class="lp-section-title">
                    <span class="material-symbols-rounded">smart_button</span>
                    Konten PPDB 2026/2027
                </div>
                <p class="text-muted small mb-3">Strip ajakan di bagian bawah halaman PPDB publik: "Siap mendaftarkan putra/putri Anda?".</p>

                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group input-group-outline mb-3 @if ($bottomEyebrow !== '') is-filled @endif">
                            <label class="form-label">Eyebrow</label>
                            <input type="text" name="bottom_eyebrow" class="form-control" maxlength="100" value="{{ $bottomEyebrow }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group input-group-outline mb-3 @if ($bottomTitle !== '') is-filled @endif">
                            <label class="form-label">Judul <span class="text-danger">*</span></label>
                            <input type="text" name="bottom_title" class="form-control" required maxlength="200" value="{{ $bottomTitle }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="input-group input-group-outline mb-3 @if ($bottomParagraph !== '') is-filled @endif">
                            <label class="form-label">Paragraf</label>
                            <textarea name="bottom_paragraph" rows="3" class="form-control" placeholder="Tim PPDB siap membantu Anda. Hubungi kami atau mulai pendaftaran online sekarang.">{{ $bottomParagraph }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group input-group-outline mb-3 @if ($bottomPrimaryText !== '') is-filled @endif">
                            <label class="form-label">Teks Tombol Utama <span class="text-danger">*</span></label>
                            <input type="text" name="bottom_primary_text" class="form-control" required maxlength="100" value="{{ $bottomPrimaryText }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group input-group-outline mb-3 @if ($bottomPrimaryUrl !== '') is-filled @endif">
                            <label class="form-label">URL Tombol Utama</label>
                            <input type="text" name="bottom_primary_url" class="form-control" maxlength="255" value="{{ $bottomPrimaryUrl }}" placeholder="/ppdb atau https://...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group input-group-outline mb-3 @if ($bottomSecondaryText !== '') is-filled @endif">
                            <label class="form-label">Teks Tombol Sekunder (opsional)</label>
                            <input type="text" name="bottom_secondary_text" class="form-control" maxlength="100" value="{{ $bottomSecondaryText }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group input-group-outline mb-3 @if ($bottomSecondaryUrl !== '') is-filled @endif">
                            <label class="form-label">URL Tombol Sekunder</label>
                            <input type="text" name="bottom_secondary_url" class="form-control" maxlength="255" value="{{ $bottomSecondaryUrl }}" placeholder="/kontak atau https://...">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="input-group input-group-outline mb-3 @if ($bottomMeta !== '') is-filled @endif">
                            <label class="form-label">Teks Meta (di bawah tombol)</label>
                            <input type="text" name="bottom_meta" class="form-control" maxlength="150" value="{{ $bottomMeta }}">
                        </div>
                    </div>
                </div>

                <div class="lp-cta-foot" style="margin-bottom: -.25rem; padding-bottom: 0;">
                    <button type="submit" class="btn btn-info">
                        <span class="material-symbols-rounded">save</span>
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
    @include('admin-landing._skrip')
@endsection
