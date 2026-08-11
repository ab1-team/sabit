@extends('layouts.tenant.base')
@section('content')
<style>
    .sop-shell {
        --sop-accent: #37d17c;
        --sop-accent-dark: #0f9b58;
        --sop-ink: #0f172a;
        --sop-muted: #64748b;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    .sop-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f766e 100%);
        border-radius: 18px;
        padding: 22px 26px;
        color: #fff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 12px 32px rgba(15, 23, 42, .18);
    }
    .sop-hero::after {
        content: "";
        position: absolute;
        right: -60px; top: -60px;
        width: 220px; height: 220px;
        background: radial-gradient(circle, rgba(55, 209, 124, .35), transparent 70%);
        pointer-events: none;
    }
    .sop-hero .crumb {
        font-size: 12px;
        color: #fff;
        text-transform: uppercase;
        letter-spacing: .12em;
        opacity: 1;
    }
    .sop-hero h4 { font-weight: 700; margin: 4px 0 6px; color: #fff; }
    .sop-hero h4 i { color: #fff; }

    .sop-menu {
        gap: 4px !important;
    }
    .sop-menu a.btn {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 6px 12px;
        color: var(--sop-ink);
        font-weight: 500;
        font-size: 13px;
        transition: all .2s ease;
        position: relative;
    }
    .sop-menu a.btn .mi {
        width: 28px; height: 28px;
        border-radius: 8px;
        display: inline-flex; align-items: center; justify-content: center;
        background: #f1f5f9;
        color: #334155;
        font-size: 14px;
        transition: all .2s ease;
    }
    .sop-menu a.btn:hover {
        border-color: var(--sop-accent);
        transform: translateY(-1px);
        box-shadow: 0 6px 14px rgba(15, 23, 42, .06);
    }
    .sop-menu a.btn:hover .mi {
        background: rgba(55, 209, 124, .15);
        color: var(--sop-accent-dark);
    }

    /* active state */
    .sop-setting:not(:has(:target)) .sop-menu a[href="#lembaga"],
    .sop-wrapper:has(#lembaga:target) .sop-menu a[href="#lembaga"],
    .sop-wrapper:has(#logo:target) .sop-menu a[href="#logo"],
    .sop-wrapper:has(#jatuhTempo:target) .sop-menu a[href="#jatuhTempo"],
    .sop-wrapper:has(#sopPembayaran:target) .sop-menu a[href="#sopPembayaran"] {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #fff;
        border-color: transparent;
        box-shadow: 0 10px 22px rgba(15, 23, 42, .25);
    }
    .sop-setting:not(:has(:target)) .sop-menu a[href="#lembaga"] .mi,
    .sop-wrapper:has(#lembaga:target) .sop-menu a[href="#lembaga"] .mi,
    .sop-wrapper:has(#logo:target) .sop-menu a[href="#logo"] .mi,
    .sop-wrapper:has(#jatuhTempo:target) .sop-menu a[href="#jatuhTempo"] .mi,
    .sop-wrapper:has(#sopPembayaran:target) .sop-menu a[href="#sopPembayaran"] .mi {
        background: rgba(255, 255, 255, .14);
        color: #fff;
    }
    .sop-setting:not(:has(:target)) .sop-menu a[href="#lembaga"]::after,
    .sop-wrapper:has(#lembaga:target) .sop-menu a[href="#lembaga"]::after,
    .sop-wrapper:has(#logo:target) .sop-menu a[href="#logo"]::after,
    .sop-wrapper:has(#jatuhTempo:target) .sop-menu a[href="#jatuhTempo"]::after,
    .sop-wrapper:has(#sopPembayaran:target) .sop-menu a[href="#sopPembayaran"]::after {
        content: "";
        position: absolute;
        right: 14px; top: 50%;
        width: 8px; height: 8px;
        border-radius: 50%;
        background: var(--sop-accent);
        transform: translateY(-50%);
        box-shadow: 0 0 0 4px rgba(55, 209, 124, .25);
    }

    .sop-content { display: none; }
    .sop-content:target { display: block; animation: sopFade .3s ease; }
    .sop-setting:not(:has(:target)) #lembaga { display: block; animation: sopFade .3s ease; }

    @keyframes sopFade {
        from { opacity: 0; transform: translateY(6px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .sop-card,
    .sop-shell .card.sop-card {
        border-radius: 0.75rem !important;
        border: none !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.10), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
        overflow: hidden;
    }
    .sop-card .card-header {
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        padding: 14px 18px;
        display: flex; align-items: center; gap: 12px;
        border-top-left-radius: 0.75rem !important;
        border-top-right-radius: 0.75rem !important;
    }
    .sop-card > .card-body.sop-menu { flex: 0 0 auto; }
    .sop-card .card-header .header-icon {
        width: 32px; height: 32px;
        border-radius: 9px;
        background: linear-gradient(135deg, rgba(55, 209, 124, .15), rgba(15, 118, 110, .15));
        color: var(--sop-accent-dark);
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 15px;
    }
    .sop-card .card-header h5 { margin: 0; font-weight: 700; color: var(--sop-ink); }
    .sop-card .card-header .sub {
        font-size: 12px; color: var(--sop-muted); margin-top: 2px;
    }

    /* ---------- Responsive ---------- */
    @media (max-width: 767.98px) {
        .sop-hero {
            padding: 16px 18px;
            border-radius: 14px;
        }
        .sop-hero .crumb { font-size: 10px; letter-spacing: .1em; }
        .sop-hero h4 { font-size: 18px; margin: 2px 0; }
        .sop-hero::after { width: 140px; height: 140px; right: -40px; top: -40px; }

        .sop-menu a.btn { padding: 10px 12px; font-size: 13px; }
        .sop-menu a.btn .mi { width: 30px; height: 30px; font-size: 14px; }

        .sop-card .card-header { padding: 12px 14px; gap: 10px; }
        .sop-card .card-header h5 { font-size: 15px; }
        .sop-card .card-header .sub { font-size: 11px; }
        .sop-card .card-header .header-icon { width: 30px; height: 30px; font-size: 14px; }

        /* hide active dot on small screens */
        .sop-wrapper .sop-menu a.btn::after { display: none !important; }
    }

    @media (max-width: 575.98px) {
        .sop-hero { padding: 14px 16px; }
        .sop-hero h4 { font-size: 17px; }
        .sop-card .card-body { padding: 14px; }
    }
</style>

<div class="container-fluid py-4 sop-setting sop-shell">
    <div class="row sop-wrapper g-3 align-items-stretch">
        <div class="col-lg-3 col-md-4 d-flex">
            <div class="card sop-card shadow-sm w-100">
                <div class="card-header">
                    <div>
                        <h5>Menu SOP</h5>
                        <div class="sub">Pilih modul pengaturan</div>
                    </div>
                </div>
                <div class="card-body d-grid gap-1 sop-menu">
                    <a href="#lembaga" class="btn text-start">
                        <span class="mi"><i class="fas fa-school"></i></span>
                        <span>Lembaga</span>
                    </a>
                    <a href="#logo" class="btn text-start">
                        <span class="mi"><i class="fas fa-image"></i></span>
                        <span>Logo</span>
                    </a>
                    <a href="#jatuhTempo" class="btn text-start">
                        <span class="mi"><i class="fas fa-clock"></i></span>
                        <span>Jatuh Tempo</span>
                    </a>
                    <a href="#sopPembayaran" class="btn text-start">
                        <span class="mi"><i class="fas fa-receipt"></i></span>
                        <span>SOP Pembayaran</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-9 col-md-8 d-flex flex-column">
            <div class="sop-content card sop-card shadow-sm" id="lembaga">
                <div class="card-header">
                    <div class="header-icon"><i class="fas fa-school"></i></div>
                    <div>
                        <h5>Pengaturan Lembaga</h5>
                        <div class="sub">Nama, alamat, kontak, dan penanggung jawab</div>
                    </div>
                </div>
                <div class="card-body">@include('pengaturan.view.lembaga')</div>
            </div>

            <div class="sop-content card sop-card shadow-sm" id="logo">
                <div class="card-header">
                    <div class="header-icon"><i class="fas fa-image"></i></div>
                    <div>
                        <h5>Pengaturan Logo</h5>
                        <div class="sub">Logo yang tampil di kwitansi & laporan</div>
                    </div>
                </div>
                <div class="card-body">@include('pengaturan.view.logo')</div>
            </div>

            <div class="sop-content card sop-card shadow-sm" id="jatuhTempo">
                <div class="card-header">
                    <div class="header-icon"><i class="fas fa-clock"></i></div>
                    <div>
                        <h5>Pengaturan Jatuh Tempo</h5>
                        <div class="sub">Tanggal toleransi pengingat tunggakan bulanan</div>
                    </div>
                </div>
                <div class="card-body">@include('pengaturan.view.jatuh_tempo')</div>
            </div>

            <div class="sop-content card sop-card shadow-sm" id="sopPembayaran">
                <div class="card-header">
                    <div class="header-icon"><i class="fas fa-receipt"></i></div>
                    <div>
                        <h5>SOP Pembayaran SPP</h5>
                        <div class="sub">Syarat minimal pembayaran untuk cetak kartu PTS &amp; PAS</div>
                    </div>
                </div>
                <div class="card-body">@include('pengaturan.view.sop_pembayaran')</div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5'
        });
    });

    function sopSubmit(opts) {
        $(document).on('click', opts.btn, function(e) {
            e.preventDefault();
            const $form = $(opts.form);
            $form.find('small').html('');
            $.ajax({
                type: 'POST',
                url: $form.attr('action'),
                data: $form.serialize(),
                success: function(result) {
                    if (result.success) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: result.msg,
                            showConfirmButton: false,
                            timer: 2500,
                            timerProgressBar: true
                        }).then(() => window.location.reload());
                    }
                },
                error: function(xhr) {
                    Swal.fire('Galat', 'Cek kembali input yang anda masukkan', 'error');
                    if (xhr.status === 422) {
                        $.each(xhr.responseJSON.errors, function(key, value) {
                            $('#' + key).closest('.input-group-outline').addClass('is-invalid');
                            $('#msg_' + key).html(value[0]);
                        });
                    }
                }
            });
        });
    }
    sopSubmit({ btn: '#SimpanLembaga',     form: '#FormLembaga' });
    sopSubmit({ btn: '#SimpanJatuhTempo',  form: '#FormJatuhTempo' });
    sopSubmit({ btn: '#SimpanSoppembayaran', form: '#FormSoppembayaran' });

    $(document).on('click', '#SimpanLogo', function (e) {
        e.preventDefault();
        const form = document.getElementById('FormLogo');
        const formData = new FormData(form);

        if (!formData.get('logo') || !formData.get('logo').name) {
            Swal.fire('Info', 'Pilih file logo terlebih dahulu', 'info');
            return;
        }

        $.ajax({
            url: form.action,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (result) {
                if (result.logo) {
                    const img = document.getElementById('previewLogo');
                    const zone = document.getElementById('uploadZone');
                    if (img) img.src = result.logo + '?v=' + Date.now();
                    if (zone) zone.classList.add('has-image');
                }
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: result.msg,
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            },
            error: function (xhr) {
                let msg = 'Logo belum dipilih atau format salah';
                if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.logo) {
                    msg = xhr.responseJSON.errors.logo[0];
                }
                Swal.fire('Galat', msg, 'error');
            }
        });
    });
</script>
@endsection
