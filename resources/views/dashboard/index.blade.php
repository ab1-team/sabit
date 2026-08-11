@php
    use Carbon\Carbon;
@endphp
@extends('layouts.tenant.base')

@section('content')
<style>
    .stat-card {
        border-radius: 0.75rem;
        border: none;
        background: #fff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.10), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        padding: 18px 20px;
        height: 100%;
        cursor: pointer;
        transition: transform .15s ease, box-shadow .15s ease;
        min-width: 0;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(15, 23, 42, .10);
    }
    .stat-card .label { font-size: 11px; text-transform: uppercase; letter-spacing: .5px; color: #64748b; font-weight: 600; }
    .stat-card .value { font-size: 30px; font-weight: 700; color: #0f172a; margin: 4px 0 0; line-height: 1.1; min-width: 0; word-break: break-word; }
    .stat-card .top { display:flex; justify-content:space-between; align-items:flex-start; gap: 10px; }
    .stat-card .top > div:first-child { min-width: 0; flex: 1 1 auto; }
    .stat-card .icon {
        width: 46px; height: 46px; border-radius: 12px;
        display:flex; align-items:center; justify-content:center;
        color: #fff;
    }
    .bg-grad-primary { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .bg-grad-success { background: linear-gradient(135deg, #22c55e, #16a34a); }
    .bg-grad-danger  { background: linear-gradient(135deg, #ef4444, #dc2626); }
    .bg-grad-warning { background: linear-gradient(135deg, #f59e0b, #d97706); }

    .panel-card {
        border-radius: 0.75rem;
        border: none;
        background: #fff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.10), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        padding: 18px 20px;
        display: flex;
        flex-direction: column;
    }
    .panel-card .chart-box {
        flex: 1 1 auto;
        min-height: 0;
        position: relative;
        height: 100%;
    }
    .chart-box canvas {
        position: absolute !important;
        top: 0;
        left: 0;
        width: 100% !important;
        height: 100% !important;
    }
    .panel-card h6.title {
        font-size: 14px;
        font-weight: 700;
        margin: 0 0 12px;
        color: #0f172a;
        display:flex; align-items:center; gap:6px;
    }
    .panel-card h6.title .material-symbols-rounded { color: #198754; }

    .recent-accordion .accordion-item {
        border: 1px solid #e2e8f0;
        border-radius: 10px !important;
        margin-bottom: 8px;
        overflow: hidden;
    }
    .recent-accordion .accordion-button {
        background: #e2e8f0;
        font-size: 13px;
        padding: 6px 16px;
        box-shadow: none !important;
        white-space: normal !important;
    }
    .recent-accordion .accordion-button:not(.collapsed) {
        background: #94a3b8;
        color: #0f172a;
    }
    .recent-accordion .accordion-body {
        background: #ffffff;
    }
    .recent-accordion .accordion-button .acc-chevron {
        transition: transform .2s ease;
        color: #475569;
    }
    .recent-accordion .accordion-button:not(.collapsed) .acc-chevron {
        transform: rotate(180deg);
        color: #0f172a;
    }
    .recent-accordion .acc-date {
        font-size: 11px;
        text-transform: uppercase;
        color: #64748b;
        font-weight: 600;
        letter-spacing: .4px;
        min-width: 90px;
    }
    .recent-accordion .acc-name { font-weight: 600; color: #0f172a; }
    .recent-accordion .acc-nisn { font-size: 11px; color: #94a3b8; }
    .recent-accordion .acc-amt {
        font-weight: 700;
        color: #15803d;
        background: rgba(34,197,94,.12);
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        white-space: nowrap;
    }
    .recent-accordion .accordion-body {
        font-size: 13px;
        color: #475569;
        padding: 12px 16px;
        background: #fafbfc;
    }
    .recent-accordion .table-responsive {
        max-height: 360px;
        overflow-y: auto;
    }
    .badge-soft {
        padding: 4px 10px; border-radius: 999px;
        font-size: 11px; font-weight: 600;
    }
    .bg-soft-success { background: rgba(34,197,94,.12); color: #15803d; }

    .pie-card {
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        border-radius: 12px;
        padding: 10px;
        display: flex; flex-direction: column; align-items: center;
        height: 100%;
        min-width: 0;
    }
    .pie-card .pie-value { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%; }
    .pie-card-danger {
        background: linear-gradient(135deg, #fef2f2, #fecaca);
    }
    .pie-wrap {
        position: relative;
        width: 70px; height: 70px;
    }
    .pie-wrap canvas { width: 100% !important; height: 100% !important; }
    .pie-info {
        text-align: center;
        margin-top: 8px;
    }
    .pie-label {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: .4px;
        color: #64748b;
        font-weight: 600;
    }
    .pie-value {
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
        margin-top: 2px;
    }
    .pie-card-danger .pie-value { color: #b91c1c; }

    .crypto-card {
        border-radius: 16px;
        padding: 20px 22px 8px;
        color: #e5e7eb;
        background: radial-gradient(120% 140% at 0% 0%, #1e293b 0%, #0f172a 55%, #020617 100%);
        border: 1px solid rgba(148,163,184,.12);
        box-shadow: 0 10px 30px rgba(2,6,23,.45);
        position: relative;
        overflow: hidden;
    }
    .crypto-card::after {
        content: "";
        position: absolute;
        top: -40%; right: -10%;
        width: 220px; height: 220px;
        background: radial-gradient(circle, rgba(16,185,129,.22), transparent 70%);
        pointer-events: none;
    }
    .crypto-head { display:flex; justify-content:space-between; align-items:center; }
    .crypto-title {
        display:flex; align-items:center; gap:6px;
        font-size: 13px; font-weight: 600; color:#94a3b8;
        text-transform: uppercase; letter-spacing:.4px;
    }
    .crypto-title .material-symbols-rounded { color:#34d399; font-size:20px; }
    .crypto-trend {
        font-size: 12px; font-weight: 700;
        padding: 4px 10px; border-radius: 999px;
        display:flex; align-items:center; gap:2px;
    }
    .crypto-trend.up   { background: rgba(16,185,129,.15); color:#34d399; }
    .crypto-trend.down { background: rgba(239,68,68,.15);  color:#f87171; }
    .crypto-total { font-size: 32px; font-weight: 800; color:#f8fafc; margin-top: 10px; line-height:1.1; letter-spacing:-.5px; }
    .crypto-sub { font-size: 12px; color:#64748b; margin-top: 2px; }
    .crypto-chart { position: relative; height: 260px; margin-top: 6px; }
    .crypto-chart canvas { position:absolute; inset:0; width:100% !important; height:100% !important; }

    @media (max-width: 575.98px) {
        .stat-card { padding: 12px 14px; }
        .stat-card .value { font-size: 22px; }
        .stat-card .icon { width: 38px; height: 38px; }
        .pie-wrap { width: 56px; height: 56px; }
        .crypto-total { font-size: 24px; }
        .crypto-chart { height: 170px; }
        .crypto-title { font-size: 12px; }
        .crypto-head { flex-wrap: wrap; gap: 6px; }
        .panel-card { padding: 14px 14px; }
        .recent-accordion .acc-date { min-width: 0; font-size: 10px; }
        .recent-accordion .acc-name { font-size: 13px; }
        .recent-accordion .acc-amt { font-size: 11px; padding: 3px 8px; }
        .recent-accordion .accordion-button { padding: 10px 12px; }
        .pie-value { font-size: 11px; }
    }

    @media (max-width: 767.98px) {
        .stat-card .value { font-size: 26px; }
        .pie-card { padding: 8px; }
        .pie-value { font-size: 12px; }
    }
</style>

<div class="row g-3 mb-3">
    <div class="col-12 col-lg-6">
        <div class="row g-3">
            <div class="col-6">
                <div class="stat-card" data-bs-toggle="modal" data-bs-target="#modalSiswaAktif">
                    <div class="top">
                        <div>
                            <div class="label">Siswa Aktif</div>
                            <div class="value text-success">{{ \App\Utils\Angka::format($siswaAktif, 0) }}</div>
                        </div>
                        <div class="icon bg-grad-success"><span class="material-symbols-rounded">verified_user</span></div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="stat-card" data-bs-toggle="modal" data-bs-target="#modalSiswaMenunggak">
                    <div class="top">
                        <div>
                            <div class="label">Tunggakan SPP</div>
                            <div class="value text-danger">{{ $jumlahSiswaMenunggak }}</div>
                        </div>
                        <div class="icon bg-grad-danger"><span class="material-symbols-rounded">warning</span></div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="crypto-card">
                    <div class="crypto-head">
                        <div class="crypto-title">
                            <span class="material-symbols-rounded">payments</span>
                            Pendapatan SPP
                        </div>
                        <span id="cryptoTrend" class="crypto-trend"></span>
                    </div>
                    <div class="crypto-chart">
                        <canvas id="chartPendapatan"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="panel-card h-100">
            <h6 class="title">
                <span class="material-symbols-rounded">receipt_long</span>
                Ringkasan Keuangan
            </h6>

            <div class="row g-2">
                <div class="col-12 col-sm-4">
                    <div class="pie-card">
                        <div class="pie-wrap"><canvas id="pieHariIni"></canvas></div>
                        <div class="pie-info">
                            <div class="pie-label">Hari Ini</div>
                            <div class="pie-value">{{ \App\Utils\Angka::format($pemasukanHariIni, 2) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-4">
                    <div class="pie-card">
                        <div class="pie-wrap"><canvas id="pieBulanIni"></canvas></div>
                        <div class="pie-info">
                            <div class="pie-label">Bulan Ini</div>
                            <div class="pie-value">{{ \App\Utils\Angka::format($pemasukanBulanIni, 2) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-4">
                    <div class="pie-card pie-card-danger">
                        <div class="pie-wrap"><canvas id="pieTunggakan"></canvas></div>
                        <div class="pie-info">
                            <div class="pie-label">Tunggakan SPP</div>
                            <div class="pie-value">{{ \App\Utils\Angka::format($totalTunggakanSpp, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-3">
            <h6 class="title">
                <span class="material-symbols-rounded">school</span>
                Nominal SPP / Angkatan
            </h6>
            <ul class="list-group list-group-flush" style="max-height:150px; overflow-y:auto;">
                @forelse($jenis_biaya as $b)
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                        <span>Angkatan {{ $b->angkatan }}</span>
                        <span class="badge bg-soft-success">{{ \App\Utils\Angka::format($b->total_beban, 2) }}</span>
                    </li>
                @empty
                    <li class="list-group-item px-0 text-muted">Belum ada data</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-12">
        <div class="accordion accordion-flush" id="accRiwayatCard">
            <div class="accordion-item">
                <div class="panel-card p-2">
                    <h2 class="accordion-header" id="accHeadRiwayat">
                        <button class="accordion-button {{ request('search') ? '' : 'collapsed' }} d-flex align-items-center gap-2" type="button"
                                data-bs-toggle="collapse" data-bs-target="#accBodyRiwayat"
                                aria-expanded="{{ request('search') ? 'true' : 'false' }}" aria-controls="accBodyRiwayat">
                            <span class="material-symbols-rounded">history</span>
                            <span class="fw-semibold flex-grow-1">Riwayat Transaksi Terbaru</span>
                            <i class="material-icons acc-chevron ms-auto">expand_more</i>
                        </button>
                    </h2>
                    <div id="accBodyRiwayat" class="accordion-collapse collapse {{ request('search') ? 'show' : '' }}"
                         aria-labelledby="accHeadRiwayat" data-bs-parent="#accRiwayatCard">
                        <div class="accordion-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama / NISN</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Keterangan</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentTransaksi as $t)
                                            <tr>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0">{{ Carbon::parse($t->tanggal_transaksi)->translatedFormat('d M Y') }}</p>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0 text-truncate" style="max-width:220px;">{{ $t->siswa?->nama ?? 'Umum' }}</p>
                                                    <p class="text-xs text-secondary mb-0">NISN: {{ $t->siswa?->nisn ?? '-' }}</p>
                                                </td>
                                                <td class="text-wrap" style="max-width:280px;">
                                                    <p class="text-xs mb-0">{{ $t->keterangan ?: '—' }}</p>
                                                </td>
                                                <td class="text-end">
                                                    <p class="text-xs font-weight-bold mb-0">{{ \App\Utils\Angka::format($t->getRawOriginal('jumlah'), 2) }}</p>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada transaksi</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('modal')
<div class="modal fade" id="modalSiswaAktif" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-fullscreen-sm-down modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Daftar Siswa Aktif</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="bodySiswaAktif">
                <div class="text-center text-muted py-5">Memuat...</div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalSiswaMenunggak" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-fullscreen-md-down modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Daftar Siswa Menunggak SPP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="bodySiswaMenunggak">
                <div class="text-center text-muted py-5">Memuat...</div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
const labelsBulanan     = @json($labelsBulanan);
const pendapatanBulanan = @json($pendapatanBulanan);

const ctx = document.getElementById('chartPendapatan').getContext('2d');

(function () {
    const last = +pendapatanBulanan[pendapatanBulanan.length - 1] || 0;
    const prev = +pendapatanBulanan[pendapatanBulanan.length - 2] || 0;
    const el = document.getElementById('cryptoTrend');
    if (prev > 0) {
        const pct = ((last - prev) / prev) * 100;
        const up = pct >= 0;
        el.className = 'crypto-trend ' + (up ? 'up' : 'down');
        el.innerHTML = '<span class="material-symbols-rounded" style="font-size:16px">'
            + (up ? 'trending_up' : 'trending_down') + '</span>'
            + (up ? '+' : '') + pct.toFixed(1) + '%';
    } else {
        el.className = 'crypto-trend up';
        el.textContent = '—';
    }
})();

new Chart(ctx, {
    type: 'line',
    data: {
        labels: labelsBulanan,
        datasets: [{
            label: 'Pendapatan',
            data: pendapatanBulanan,
            borderColor: '#34d399',
            backgroundColor: 'rgba(52,211,153,.15)',
            tension: 0.4,
            fill: true,
            borderWidth: 2.5,
            pointRadius: 0,
            pointHoverRadius: 5,
            pointBackgroundColor: '#34d399',
            pointBorderColor: '#0f172a',
            pointBorderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: (c) => new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(Math.round(c.parsed.y))
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { color: '#64748b', callback: (v) => new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(v) },
                grid: { color: 'rgba(148,163,184,.10)' }
            },
            x: { ticks: { color: '#64748b' }, grid: { display: false } }
        }
    }
});

const makePie = (id, value, max, color1, color2) => {
    const el = document.getElementById(id);
    if (!el) return;
    const pct = max > 0 ? Math.min(100, (value / max) * 100) : 0;
    new Chart(el.getContext('2d'), {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [pct, 100 - pct],
                backgroundColor: [color1, '#e2e8f0'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: { legend: { display: false }, tooltip: { enabled: false } }
        }
    });
};

makePie('pieHariIni',   {{ (float) $pemasukanHariIni }}, Math.max({{ (float) $pemasukanBulanIni }}, 1), '#10b981', '#d1fae5');
makePie('pieBulanIni',  {{ (float) $pemasukanBulanIni }}, Math.max({{ (float) $pemasukanBulanIni }}, 1), '#0ea5e9', '#bae6fd');
makePie('pieTunggakan', {{ (float) $totalTunggakanSpp }},   Math.max({{ (float) $totalTunggakanSpp }}, 1),   '#ef4444', '#fecaca');

$(document).ready(function () {
    const loadPartial = function (modalId, bodyId, url) {
        const $modal = $(modalId);
        $modal.on('shown.bs.modal', function () {
            const $body = $(bodyId);
            if ($body.data('loaded')) return;
            $body.html('<div class="text-center text-muted py-5">Memuat...</div>');
            $.get(url, function (html) {
                $body.html(html).data('loaded', true);
            }).fail(function () {
                $body.html('<div class="text-center text-danger py-5">Gagal memuat data.</div>');
            });
        });
    };

    loadPartial('#modalSiswaAktif',    '#bodySiswaAktif',    '/app/dashboard/siswa-aktif');
    loadPartial('#modalSiswaMenunggak', '#bodySiswaMenunggak', '/app/dashboard/siswa-menunggak');
});
</script>

<div id="piutangOverlay" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,.55); z-index:99999; backdrop-filter:blur(2px);">
    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); background:#fff; padding:28px 32px; border-radius:14px; box-shadow:0 10px 30px rgba(0,0,0,.2); min-width:320px; text-align:center;">
        <div class="spinner-border text-success mb-3" role="status" style="width:3rem; height:3rem;"></div>
        <div style="font-weight:600; color:#0f172a;">Generate Piutang SPP</div>
        <div style="color:#64748b; font-size:13px; margin-top:6px;" id="piutangOverlayMsg">Sedang memproses, jangan tutup halaman...</div>
    </div>
</div>

@if(request('gen_piutang') == '1' && request('job'))
<script>
(function () {
    const overlay = document.getElementById('piutangOverlay');
    const msg = document.getElementById('piutangOverlayMsg');
    const job = @json(request('job'));
    overlay.style.display = 'block';
    document.body.style.overflow = 'hidden';

    fetch('/app/system/generate-tunggakan?job=' + encodeURIComponent(job), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.ok) {
            msg.textContent = 'Pembuatan selesai. Memuat hasil...';
            let tries = 0;
            const poll = setInterval(() => {
                tries++;
                fetch('/app/system/piutang-status?job=' + encodeURIComponent(job))
                    .then(r => r.json())
                    .then(s => {
                        if (s.done) {
                            clearInterval(poll);
                            const inserted = s.data?.inserted ?? 0;
                            const skipped  = s.data?.skipped ?? 0;
                            const bulan    = s.data?.bulan ?? '';
                            overlay.style.display = 'none';
                            document.body.style.overflow = '';
                            const url = window.location.pathname;
                            Swal.fire({
                                icon: 'success',
                                title: 'Pembuatan Piutang Selesai',
                                html: `Bulan: <strong>${bulan}</strong>`,
                                confirmButtonText: 'OK',
                                allowOutsideClick: false,
                            }).then(() => { window.location.href = url; });
                        } else if (tries > 60) {
                            clearInterval(poll);
                            overlay.style.display = 'none';
                            document.body.style.overflow = '';
                            Swal.fire({ icon: 'warning', title: 'Batas waktu habis', text: 'Cek log server.' });
                        }
                    });
            }, 1000);
        } else {
            throw new Error('Gagal');
        }
    })
    .catch(() => {
        overlay.style.display = 'none';
        document.body.style.overflow = '';
            Swal.fire({ icon: 'error', title: 'Pembuatan gagal', text: 'Terjadi kesalahan server.' });
    });
})();
</script>
@endif
@endsection
