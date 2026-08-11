@if ($siswa->exists)
@endif

<style>
  .sop-cetakkartu.sop-disabled,
  .sop-cetakkartu[disabled] {
    opacity: .45;
    cursor: not-allowed;
    pointer-events: auto;
  }
  .sop-cetakkartu.sop-disabled:hover {
    filter: none;
  }
  .action-section {
    position: relative;
  }
  .action-section .section-label {
    position: absolute;
    top: -.55rem;
    left: .75rem;
    padding: 0 .35rem;
    background: #fff;
    font-size: .65rem;
    font-weight: 600;
    letter-spacing: .03em;
    text-transform: uppercase;
    color: #6c757d;
    line-height: 1;
    z-index: 1;
    max-width: calc(100% - 1.5rem);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .action-section .section-label i {
    font-size: .8rem;
    margin-right: .15rem;
    vertical-align: -1px;
  }
  .action-card {
    height: 38px;
    font-size: .8rem;
    font-weight: 500;
    border-radius: .375rem;
    transition: all .15s ease;
    white-space: nowrap;
  }
  .action-card i {
    font-size: 1rem;
  }
.action-card:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
  }
  .spp-row {
    display: grid;
    grid-template-columns: repeat(6, minmax(0, 1fr));
    gap: .5rem;
    flex-wrap: nowrap;
  }
  .spp-row .spp-item {
    min-width: 0;
  }
  .spp-check {
    font-weight: 700;
    font-size: .95rem;
    line-height: 1;
  }
  .spp-row .spp-label {
    width: 100%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    padding: .35rem .5rem;
    font-size: .8rem;
  }
  .spp-row .spp-label::before {
    content: "\2713" !important;
    font-size: .95rem;
    line-height: 1;
    margin-right: .3rem;
    font-weight: 700;
    display: none !important;
    vertical-align: -1px;
  }
  .spp-row .btn-check:checked + .spp-label::before,
  .spp-row .spp-bln:checked + .spp-label::before {
    display: inline-block !important;
  }
  @media (max-width: 575.98px) {
    .spp-row {
      grid-template-columns: repeat(4, minmax(0, 1fr));
    }
  }
  @media (max-width: 575.98px) {
    .action-card {
      height: 36px;
      font-size: .75rem;
    }
    .action-card i {
      font-size: .9rem;
    }
  }
</style>

<div class="row d-flex align-items-stretch">
    <div class="col-md-8 d-flex">
        <div class="card mt-1 mb-4 flex-fill">
            <div class="card-body">
                <form method="POST" action="/app/transaksi/ProsesPembayaran" id="FormPembayaranSPP">
                    @csrf
                    <input type="hidden" name="siswa_id" value="{{ $siswa->id }}">
                    <input type="hidden" name="siswa_nama" id="siswa_nama" value="{{ $siswa->nama }}">
                    <input type="hidden" id="nominalMap" value='{{ json_encode($nominalMap->mapWithKeys(fn($g, $k) => [$k => $g->pluck("total_beban")])) }}'>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div
                                class="input-group input-group-outline mb-3 {{ old('tanggal', date('Y-m-d')) ? 'is-filled' : '' }}">
                                <label class="form-label">Tanggal</label>
                                <input type="text" name="tanggal" class="form-control datepicker"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div
                                class="input-group input-group-outline mb-3 {{ old('kelas', optional($anggota_kelas)->kode_kelas ?? $siswa->kode_kelas) ? 'is-filled' : '' }}">
                                <label class="form-label">Kelas</label>
                                <input type="text" name="kelas" id="kelas" class="form-control"
                                    value="{{ optional($anggota_kelas)->kode_kelas ?? $siswa->kode_kelas }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6 mb-2 d-none">
                            <select name="sumber_dana" id="sumber_dana" class="form-control select2">
                                <option value="0">Sumber Pembayaran</option>
                                @foreach ($sumber_dana as $sd)
                                    <option value="{{ $sd->kode_akun }}">{{ $sd->nama_akun }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-2">
                            @if (!$siswa->exists)
                                <select name="jenis_biaya" id="jenis_biaya" class="form-control select2" disabled>
                                    <option value="0">Pilih Jenis Pembayaran</option>
                                </select>
                            @elseif ($jenis_biaya->isEmpty())
                                <div class="alert alert-warning mb-0">
                                    Tidak ada jenis pembayaran untuk tahun angkatan {{ $tahun_angkatan }}.
                                </div>
                            @else
                                <select name="jenis_biaya" id="jenis_biaya" class="form-control select2">
                                    <option value="0">Pilih Jenis Pembayaran</option>
                                    @foreach ($jenis_biaya as $jb)
                                        @php
                                            $arr = $nominalMap[$jb->id.'|'.$tahun_angkatan] ?? null;
                                            $nm = ($arr && isset($arr[0])) ? $arr[0]->total_beban : '';
                                        @endphp
                                        <option value="{{ $jb->kode_akun }}" data-idjp="{{ $jb->id }}"
                                            data-nominal="{{ $nm }}"
                                            data-is-spp="{{ $jb->isSpp() ? 1 : 0 }}">{{ $jb->nama }}</option>
                                    @endforeach
                                </select>
                            @endif
                            <input type="hidden" id="tahun_angkatan" value="{{ $tahun_angkatan }}">
                        </div>
                    </div>
                    <div class="row mt-2" id="bulanWrapper" style="display: none;">
                        <div class="col-12 mt-2">
                            <label>Bulan Tagihan</label>
                        </div>
                        @php
                            $sppItems = collect($spp)->sortBy(function ($item) {
                                $m = (int) \Carbon\Carbon::parse($item->tanggal)->month;
                                return $m >= 7 ? $m : $m + 12;
                            })->values();
                            $sppGanjil = $sppItems->filter(fn ($i) => (int) \Carbon\Carbon::parse($i->tanggal)->month >= 7)->values();
                            $sppGenap  = $sppItems->filter(fn ($i) => (int) \Carbon\Carbon::parse($i->tanggal)->month <  7)->values();
                        @endphp
                        <div class="col-md-12 mt-2">
                            <div id="sppBulanList">
                                <div class="spp-row spp-row-ganjil mt-1">
                                    @foreach ($sppGanjil as $idx => $item)
                                        @php
                                            $bulan = (int) \Carbon\Carbon::parse($item->tanggal)->month;
                                            $isPaid = $item->status == 'L';
                                            $globalIdx = $sppItems->search(fn ($x) => $x->kode === $item->kode);
                                            $prevKode = $globalIdx > 0 ? $sppItems[$globalIdx - 1]->kode : null;
                                            $lockReason = $globalIdx > 0
                                                ? 'Bulan ' . \App\Utils\Tanggal::namaBulan($sppItems[$globalIdx - 1]->tanggal) . ' belum dibayar'
                                                : '';
                                            $btnClass = $isPaid ? 'btn-info' : 'btn-outline-info';
                                        @endphp
                                        <span class="spp-item">
                                            <input type="checkbox" name="bulan_dibayar[]" class="btn-check spp-checkbox spp-bln"
                                                data-kode="{{ $item->kode }}" data-nominal="{{ $item->nominal }}"
                                                id="tgl_{{ $item->id }}" value="{{ $item->tanggal }}"
                                                data-spp_ke="{{ $item->spp_ke }}"
                                                data-bulan="{{ $bulan }}"
                                                data-prev-kode="{{ $prevKode }}"
                                                data-lock-reason="{{ $lockReason }}"
                                                {{ $isPaid ? 'checked disabled' : '' }}>
                                            <label
                                                class="btn btn-sm rounded-pill text-center spp-label {{ $btnClass }}"
                                                for="tgl_{{ $item->id }}">
                                                @if ($isPaid)<span class="spp-check me-1">&#10003;</span>@endif{{ \App\Utils\Tanggal::namaBulan($item->tanggal) }}
                                            </label>
                                        </span>
                                    @endforeach
                                </div>
                                <div class="spp-row spp-row-genap mt-2">
                                    @foreach ($sppGenap as $idx => $item)
                                        @php
                                            $bulan = (int) \Carbon\Carbon::parse($item->tanggal)->month;
                                            $isPaid = $item->status == 'L';
                                            $globalIdx = $sppItems->search(fn ($x) => $x->kode === $item->kode);
                                            $prevKode = $globalIdx > 0 ? $sppItems[$globalIdx - 1]->kode : null;
                                            $lockReason = $globalIdx > 0
                                                ? 'Bulan ' . \App\Utils\Tanggal::namaBulan($sppItems[$globalIdx - 1]->tanggal) . ' belum dibayar'
                                                : '';
                                            $btnClass = $isPaid ? 'btn-danger' : 'btn-outline-danger';
                                        @endphp
                                        <span class="spp-item">
                                            <input type="checkbox" name="bulan_dibayar[]" class="btn-check spp-checkbox spp-bln"
                                                data-kode="{{ $item->kode }}" data-nominal="{{ $item->nominal }}"
                                                id="tgl_{{ $item->id }}" value="{{ $item->tanggal }}"
                                                data-spp_ke="{{ $item->spp_ke }}"
                                                data-bulan="{{ $bulan }}"
                                                data-prev-kode="{{ $prevKode }}"
                                                data-lock-reason="{{ $lockReason }}"
                                                {{ $isPaid ? 'checked disabled' : '' }}>
                                            <label
                                                class="btn btn-sm rounded-pill text-center spp-label {{ $btnClass }}"
                                                for="tgl_{{ $item->id }}">
                                                @if ($isPaid)<span class="spp-check me-1">&#10003;</span>@endif{{ \App\Utils\Tanggal::namaBulan($item->tanggal) }}
                                            </label>
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div id="sppKEContainer"></div>
                        <div id="sppIDContainer"></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <div
                                class="input-group input-group-outline mb-3 {{ old('keterangan') ? 'is-filled' : '' }}">
                                <label class="form-label">Keterangan</label>
                                <textarea name="keterangan" id="keterangan" rows="1" class="form-control">{{ old('keterangan') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <div
                                class="input-group input-group-outline mb-3 {{ old('nominal', optional($anggota_kelas)->spp_nominal) ? 'is-filled' : '' }}">
                                <label class="form-label">Nominal</label>
                                <input type="text" name="nominal" id="nominal" class="form-control nominal"
                                    readonly>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex
                            flex-column flex-md-row
                            align-items-stretch align-items-md-center
                            justify-content-md-end
                            gap-2
                            p-2 pb-1">
                    <button type="button" id="kuitansi"
                        class="btn btn-outline-secondary btn-sm d-none w-100 w-md-auto">
                        Kwitansi
                    </button>
                    <button type="button" id="CetakPadaKartu"
                        class="btn btn-outline-info btn-sm d-none w-100 w-md-auto">
                        Cetak Pada Kartu
                    </button>
                    <button type="submit" id="Tunai"
                        data-sumber="1.1.01.01"
                        class="btn btn-warning w-100 mb-0 w-md-auto SPPsimpan"
                        @disabled(!$siswa->exists)>
                        Tunai
                    </button>
                    <button type="submit" id="TransferBank"
                        data-sumber="1.1.01.03"
                        class="btn btn-info w-100 w-md-auto mb-0 SPPsimpan"
                        @disabled(!$siswa->exists)>
                        Transfer Bank
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4 d-flex">
        <div class="card mt-1 mb-4 flex-fill">
            <div class="card-body pt-3">
                @if(!$siswa->exists)
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-person fs-1"></i>
                        <p class="mt-2 mb-0 small">Belum ada siswa dipilih</p>
                    </div>
                @else
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2"
                            style="width:48px;height:48px;font-weight:600;">
                            {{ strtoupper(mb_substr($siswa->nama ?? '?', 0, 1)) }}
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold text-dark lh-sm">{{ $siswa->nama }}</div>
                            <small class="text-muted">{{ optional($anggota_kelas)->kode_kelas ?? $siswa->kode_kelas }} · {{ $siswa->ruang }}</small>
                        </div>
                    </div>
                    <hr class="horizontal dark my-3">
                    <hr class="horizontal dark my-3">

                    <div class="action-section mb-3">
                        <button type="button" id="btnDetailSiswa"
                            class="btn btn-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-2"
                            @disabled(!$siswa->exists)>
                            <i class="bi bi-receipt-cutoff" style="font-size:1.1rem;"></i>
                            <span>Detail Pembayaran</span>
                        </button>
                        <a href="{{ url('/app/daftar-kelas') . '?' . http_build_query(array_filter([
                            'tahun_akademik' => request('tahun_akademik'),
                            'kelas'          => request('kelas') !== '__all__' ? request('kelas') : null,
                        ])) }}" class="btn btn-secondary btn-sm w-100 mt-2 d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-list-ul" style="font-size:1.1rem;"></i>
                            <span>Daftar Siswa Per-Kelas</span>
                        </a>
                    </div>

                    <div class="action-section mb-3 pt-3 px-2 pb-1 border border-light rounded">
                        <div class="section-label">
                            <i class="bi bi-credit-card-2-front"></i>
                            <span>Cetak Kartu SPP</span>
                        </div>
                        <a href="/app/transaksi/cetak-kartu-spp/{{ $siswa->id }}{{ request('tahun_akademik') || request('kelas') ? '?' . http_build_query(array_filter(['tahun_akademik' => request('tahun_akademik'), 'kelas' => request('kelas') !== '__all__' ? request('kelas') : null])) : '' }}" target="_blank"
                            class="btn btn-outline-primary btn-sm w-100 d-flex align-items-center justify-content-center gap-2"
                            @disabled(!$siswa->exists)>
                            <i class="material-symbols-rounded" style="font-size:1.2rem;">print</i>
                            <span>Kartu SPP</span>
                        </a>
                    </div>

                    <div class="action-section pt-3 px-2 pb-1 border border-light rounded">
                        <div class="section-label">
                            <i class="bi bi-pencil-square"></i>
                            <span>Cetak Kartu Ujian</span>
                        </div>
                        @php
                            $bulanLunas   = (int) ($bulan_lunas ?? 0);
                            $sopPts       = (int) ($sop_pts ?? 3);
                            $sopPas       = (int) ($sop_pas ?? 3);
                            $bolehPts     = $bulanLunas >= $sopPts;
                            $bolehPas     = $bulanLunas >= $sopPas;
                            $infoPtsShort = "Syarat minimal bayar {$sopPts} bulan SPP. Baru dibayar: {$bulanLunas} bulan.";
                            $infoPasShort = "Syarat minimal bayar {$sopPas} bulan SPP. Baru dibayar: {$bulanLunas} bulan.";
                            $infoPtsFull  = "Cetak kartu UTS I & PAS I butuh minimal {$sopPts} bulan SPP lunas. Saat ini baru {$bulanLunas} bulan.";
                            $infoPasFull  = "Cetak kartu UTS II & PAS II butuh minimal {$sopPas} bulan SPP lunas. Saat ini baru {$bulanLunas} bulan.";
                            $cetakQs = http_build_query(array_filter([
                                'tahun_akademik' => request('tahun_akademik'),
                                'kelas'          => request('kelas') !== '__all__' ? request('kelas') : null,
                            ]));
                            $cetakQs = $cetakQs ? '?'.$cetakQs : '';
                        @endphp
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="d-flex flex-column gap-2">
                                    <a href="/app/transaksi/cetak-kartu-ujian/{{ $siswa->id }}/uts1{{ $cetakQs }}" target="_blank"
                                        class="action-card btn btn-outline-success d-flex align-items-center justify-content-center gap-2 sop-cetakkartu {{ $bolehPts ? '' : 'sop-disabled' }}"
                                        @disabled(!$siswa->exists || !$bolehPts)
                                        title="{{ $bolehPts ? 'Cetak Kartu UTS I' : $infoPtsShort }}"
                                        data-sop-msg="{{ $infoPtsFull }}">
                                        <i class="material-symbols-rounded">print</i>
                                        <span>UTS I</span>
                                    </a>
                                    <a href="/app/transaksi/cetak-kartu-ujian/{{ $siswa->id }}/uts2{{ $cetakQs }}" target="_blank"
                                        class="action-card btn btn-outline-info d-flex align-items-center justify-content-center gap-2 sop-cetakkartu {{ $bolehPas ? '' : 'sop-disabled' }}"
                                        @disabled(!$siswa->exists || !$bolehPas)
                                        title="{{ $bolehPas ? 'Cetak Kartu UTS II' : $infoPasShort }}"
                                        data-sop-msg="{{ $infoPasFull }}">
                                        <i class="material-symbols-rounded">print</i>
                                        <span>UTS II</span>
                                    </a>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex flex-column gap-2">
                                    <a href="/app/transaksi/cetak-kartu-ujian/{{ $siswa->id }}/pas1{{ $cetakQs }}" target="_blank"
                                        class="action-card btn btn-outline-warning d-flex align-items-center justify-content-center gap-2 sop-cetakkartu {{ $bolehPts ? '' : 'sop-disabled' }}"
                                        @disabled(!$siswa->exists || !$bolehPts)
                                        title="{{ $bolehPts ? 'Cetak Kartu PAS I' : $infoPtsShort }}"
                                        data-sop-msg="{{ $infoPtsFull }}">
                                        <i class="material-symbols-rounded">print</i>
                                        <span>PAS I</span>
                                    </a>
                                    <a href="/app/transaksi/cetak-kartu-ujian/{{ $siswa->id }}/pas2{{ $cetakQs }}" target="_blank"
                                        class="action-card btn btn-outline-danger d-flex align-items-center justify-content-center gap-2 sop-cetakkartu {{ $bolehPas ? '' : 'sop-disabled' }}"
                                        @disabled(!$siswa->exists || !$bolehPas)
                                        title="{{ $bolehPas ? 'Cetak Kartu PAS II' : $infoPasShort }}"
                                        data-sop-msg="{{ $infoPasFull }}">
                                        <i class="material-symbols-rounded">print</i>
                                        <span>PAS II</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
@if($kode_tunggakan->count())
<div id="toast-wrapper"
     class="position-fixed bottom-0 end-0 p-3"
     style="z-index:99999">
    @foreach($kode_tunggakan as $t)
        <div class="toast mb-2" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto text-danger">Tunggakan SPP</strong>
                <button class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                {{ $t->keterangan }}
            </div>
        </div>
    @endforeach
</div>
@endif
<script>
document.querySelectorAll('#toast-wrapper .toast').forEach(el => {
    const toast = new bootstrap.Toast(el, {
        delay: 3000,
        autohide: true
    });

    toast.show();

    el.addEventListener('hidden.bs.toast', () => {
        el.remove();

        const wrapper = document.getElementById('toast-wrapper');
        if (wrapper && wrapper.children.length === 0) {
            wrapper.remove();
        }
    });
});
</script>

<script>
    $('#keterangan').val('-').trigger('focus').trigger('blur');

    function applyPrefillJenis() {
        if (typeof window.__prefillJenisApplied !== 'undefined' && window.__prefillJenisApplied) return;
        if ($('#jenis_biaya').length === 0) return;
        let urlParams = new URLSearchParams(window.location.search);
        let prefillJenis = urlParams.get('prefill_jenis');
        if (prefillJenis === 'spp') {
            let $sppOpt = $('#jenis_biaya option[data-is-spp="1"]').first();
            if ($sppOpt.length) {
                $('#jenis_biaya').val($sppOpt.val()).trigger('change').trigger('select2:select');
                window.__prefillJenisApplied = true;
            }
        }
    }

    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5'
        });
        flatpickr('.datepicker', {
            dateFormat: 'Y-m-d'
        });
        $('.nominal').maskMoney({
            thousands: ',',
            decimal: '.',
            precision: 2,
            allowZero: true
        });

        $('#jenis_biaya').on('change', function() {

            const $opt = $('#jenis_biaya option:selected');
            const isSpp = $opt.data('is-spp') == 1;
            const nama = $('#siswa_nama').val();
            const namaAkun = $opt.text();
            const idjp = $opt.data('idjp');
            const angkatan = $('#tahun_angkatan').val();

            $('.SPPsimpan')
                .prop('disabled', false)
                .removeAttr('aria-disabled')
                .each(function () {
                    $(this).html($(this).data('original-html'));
            });

            $('#bulanWrapper').toggle(isSpp);
            $('.spp-checkbox').prop('checked', false);
            $('.spp-bln:not(:disabled)').prop('disabled', false);
            $('#sppIDContainer').empty();

            const defaultNominal = lookupNominal(idjp, angkatan);

            const syncNominalLabel = () => {
                const v = $('#nominal').val().trim();
                $('#nominal').closest('.input-group').toggleClass('is-filled', v !== '');
            };

            if (isSpp) {
                $('#nominal').prop('readonly', true).maskMoney('mask', 0);
                syncNominalLabel();
                $('#keterangan').val(`${namaAkun} an. ${nama}`);
                $('#kuitansi, #CetakPadaKartu').addClass('d-none');
            } else if (defaultNominal > 0) {
                $('#nominal').prop('readonly', false).maskMoney('mask', defaultNominal);
                syncNominalLabel();
                $('#kuitansi, #CetakPadaKartu').addClass('d-none');
                $('#keterangan').val(`${namaAkun} an. ${nama}`);
            } else {
                $('#nominal').prop('readonly', false).val('');
                syncNominalLabel();
                $('#kuitansi, #CetakPadaKartu').addClass('d-none');
                $('#keterangan').val(`${namaAkun} an. ${nama}`);
            }
        });

        function lookupNominal(idjp, angkatan) {
            try {
                const map = JSON.parse($('#nominalMap').val() || '{}');
                const arr = map[`${idjp}|${angkatan}`];
                return arr && arr.length ? arr[0] : 0;
            } catch (e) { return 0; }
        }

        applyPrefillJenis();

        function sppIsBulanUnlocked($input) {
            const prevKode = $input.data('prev-kode');
            if (!prevKode) return true;
            const $prev = $('#tgl_' + $('[data-kode="' + prevKode + '"]').attr('id').replace('tgl_', ''));
            const $prevByKode = $('.spp-bln').filter(function() {
                return $(this).data('kode') === prevKode;
            }).first();
            if ($prevByKode.length === 0) return true;
            if ($prevByKode.prop('disabled')) return true;
            if ($prevByKode.is(':checked')) return true;
            return false;
        }

        function sppNotifyLocked($input) {
            const reason = $input.data('lock-reason') || 'Bulan sebelumnya belum dibayar';
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak Dapat Memilih Bulan Ini',
                    text: reason + '. Selesaikan pembayaran bulan sebelumnya terlebih dahulu.',
                    confirmButtonText: 'Oke'
                });
            } else {
                alert(reason + '. Selesaikan pembayaran bulan sebelumnya terlebih dahulu.');
            }
        }

        $(document).on('click', '.spp-label', function(e) {
            const $input = $(this).siblings('.spp-bln');
            if ($input.length && !$input.prop('disabled') && !sppIsBulanUnlocked($input)) {
                e.preventDefault();
                e.stopImmediatePropagation();
                $input.prop('checked', false);
                setTimeout(function() { $input.prop('checked', false); }, 0);
                sppNotifyLocked($input);
                return false;
            }
        });

        $(document).on('click', '.spp-bln', function(e) {
            if (!this.disabled && this.checked && !sppIsBulanUnlocked($(this))) {
                e.stopImmediatePropagation();
                this.checked = false;
                sppNotifyLocked($(this));
            }
        });

        $(document).on('change', '.spp-bln', function(e) {
            if (!this.disabled && this.checked && !sppIsBulanUnlocked($(this))) {
                e.stopImmediatePropagation();
                this.checked = false;
                sppNotifyLocked($(this));
            }
        });

        $('.spp-checkbox').on('change', function() {
            let total = 0;
            $('#sppIDContainer').empty();

            $('.spp-checkbox:checked:not(:disabled)').each(function() {
                const kode = $(this).data('kode');
                const nominal = parseInt($(this).data('nominal'));

                total += nominal;

                $('#sppIDContainer').append(`
            <input type="hidden" name="kode_spp[]" value="${kode}">
            <input type="hidden" name="nominal_spp[]" value="${nominal}">
        `);
            });

            $('#nominal').maskMoney('mask', total);
            $('#nominal').closest('.input-group').toggleClass('is-filled', total > 0);
        });

        $('#nominal').on('input', function() {
            $(this).closest('.input-group').toggleClass('is-filled', $(this).val().trim() !== '');
        });

        const $ta = $('textarea.form-control');

        function updateState(el) {
            const g = el.closest('.input-group');
            g.toggleClass('is-filled is-focused', el.val().trim() !== '');
        }
        $ta.each(function() {
            updateState($(this));
        });
        $ta.on('focus input', function() {
            $(this).closest('.input-group').addClass('is-filled is-focused');
        });
        $ta.on('blur', function() {
            updateState($(this));
        });
    });
</script>
<script>
    function setTextareaRows() {
        const textarea = document.getElementById('keterangan');
        if (window.innerWidth < 768) {
            textarea.rows = 4; // HP
        } else {
            textarea.rows = 1; // Desktop
        }
    }

    setTextareaRows();
    window.addEventListener('resize', setTextareaRows);
</script>
<script>
    document.querySelectorAll('a.sop-cetakkartu.sop-disabled').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (el.hasAttribute('disabled')) {
                e.preventDefault();
                return;
            }
            if (el.classList.contains('sop-disabled')) {
                e.preventDefault();
                const msg = el.getAttribute('data-sop-msg') || 'Syarat pembayaran SPP belum terpenuhi.';
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Belum Bisa Cetak Kartu',
                        text: msg,
                        confirmButtonText: 'OK'
                    });
                } else {
                    alert(msg);
                }
            }
        });
    });
</script>
