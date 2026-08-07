@extends('layouts.tenant.base')
@section('content')
<div class="row">
    <div class="col-md-9 mt-3">
        <div class="card h-100">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div
                    class="bg-gradient-secondary shadow-secondary border-radius-lg pt-3 pb-1 d-flex justify-content-between align-items-center">
                    <h6 class="text-white text-capitalize ps-3" id="headerTitle">
                        Pencatatan Transaksi Keuangan (Jurnal Umum)
                    </h6>
                </div>
            </div>
            <div class="card-body p-3 pb-0">
                <form action="/app/Transaksi" method="post" id="FormTransaksi">
                    @csrf
                    <input type="hidden" name="transaksi" id="transaksi" value="jurnal_umum">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="tanggal">Tanggal Transaksi</label>
                            <div class="input-group input-group-outline mb-3">
                                <input type="text" class="form-control" id="tanggal" name="tanggal"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="jenis_transaksi">Jenis Transaksi</label>
                            <div class="input-group input-group-outline mb-3">
                                <select id="jenis_transaksi" name="jenis_transaksi" class="form-control select2">
                                    <option value="">-- Pilih Jenis Transaksi --</option>
                                    @foreach ($jenisTransaksi as $jt)
                                    <option value="{{ $jt->id }}">{{ $jt->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="sumber_dana">Sumber Dana</label>
                            <div class="input-group input-group-outline mb-3">
                                <select id="sumber_dana" name="sumber_dana" class="form-control select2">
                                    <option value="">-- Pilih Sumber Dana --</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="disimpan_ke">Disimpan Ke</label>
                            <div class="input-group input-group-outline mb-3">
                                <select id="disimpan_ke" name="disimpan_ke" class="form-control select2">
                                    <option value="">-- Pilih Tujuan --</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="form-jurnal-umum">
                        <div class="col-md-12">
                            <label for="keterangan_transaksi">Keterangan</label>
                            <div class="input-group input-group-outline mb-3">
                                <textarea class="form-control" id="keterangan_transaksi"
                                    name="jurnal_umum[keterangan]" rows="1"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label for="nominal">Nominal</label>
                            <div class="input-group input-group-outline mb-3">
                                <input type="text" class="form-control nominal" id="nominal" name="jurnal_umum[nominal]"
                                    value="0.00">
                            </div>
                        </div>
                    </div>
                    <div class="row" id="form-beli-inventaris" style="display:none">
                        <input type="hidden" name="beli_inventaris[jenis_inventaris]" id="jenis_inventaris">
                        <input type="hidden" name="beli_inventaris[kategori_inventaris]" id="kategori_inventaris">
                        <div class="col-md-12">
                            <label>Nama Barang</label>
                            <div class="input-group input-group-outline mb-3">
                                <input type="text" class="form-control" name="beli_inventaris[nama_barang]">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>Harga Satuan</label>
                            <div class="input-group input-group-outline mb-3">
                                <input type="text" class="form-control nominal" name="beli_inventaris[harga_satuan]"
                                    value="0.00">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>Umur Ekonomis (bulan)</label>
                            <div class="input-group input-group-outline mb-3">
                                <input type="number" class="form-control" name="beli_inventaris[umur_ekonomis]"
                                    value="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>Jumlah Unit</label>
                            <div class="input-group input-group-outline mb-3">
                                <input type="number" class="form-control" name="beli_inventaris[jumlah_unit]" value="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>Harga Perolehan</label>
                            <div class="input-group input-group-outline mb-3">
                                <input type="text" class="form-control nominal" name="beli_inventaris[harga_perolehan]"
                                    value="0.00">
                            </div>
                        </div>
                    </div>
                    <div class="row" id="form-hapus-inventaris" style="display:none">
                        <div class="col-md-12">
                            <label>Daftar Barang</label>
                            <div class="input-group input-group-outline mb-3">
                                <select id="daftar_barang" name="hapus_inventaris[daftar_barang]"
                                    class="form-control select2">
                                    <option value="">-- Pilih Barang --</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>Alasan</label>
                            <div class="input-group input-group-outline mb-3">
                                <select id="alasan" name="hapus_inventaris[alasan]" class="form-control select2">
                                    <option value="">-- Pilih Alasan --</option>
                                    <option value="jual">Jual</option>
                                    <option value="hapus">Hapus</option>
                                    <option value="hilang">Hilang</option>
                                    <option value="revaluasi">Revaluasi</option>
                                    <option value="rusak">Rusak</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>Jumlah Unit</label>
                            <div class="input-group input-group-outline mb-3">
                                <input type="number" class="form-control"
                                    name="hapus_inventaris[jumlah_unit_inventaris]" value="0" min="0">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label>Nilai Buku</label>
                            <div class="input-group input-group-outline mb-3">
                                <input type="text" class="form-control nominal" name="hapus_inventaris[nilai_buku]"
                                    value="0.00" readonly>
                            </div>
                        </div>
                        <div class="col-md-6" id="input-harga-jual" style="display:none">
                            <label>Harga Jual</label>
                            <div class="input-group input-group-outline mb-3">
                                <input type="text" class="form-control nominal" name="hapus_inventaris[harga_jual]"
                                    value="0.00">
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-info mb-0">Simpan Transaksi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-3 mt-3">
        <div class="card h-100">
            <div class="card-body p-3 pb-0">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-0">Saldo</h6>
                    <span class="fw-bolder" id="saldo-wrapper">
                        Rp. <span id="saldo">0</span>
                    </span>
                </div>
                <input type="hidden" name="saldo_trx" id="saldo_trx" value="0">
                <hr class="horizontal dark my-4">
                <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-2 text-center">
                    Cetak Buku Bantu
                </h6>
                <hr class="horizontal dark my-3">
                <div class="row">
                    <div class="col-md-12">
                        <label class="text-muted small mb-2">Tahunan</label>
                        <div class="input-group input-group-outline input-group-sm mb-2">
                            <select class="form-control form-control-sm select2" id="filter-tahunan">
                                <option value="">Pilih Tahun</option>
                                <option value="2025">2025</option>
                                <option value="2026">2026</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label class="text-muted small mb-2">Bulanan</label>
                        <div class="input-group input-group-outline input-group-sm mb-2">
                            <select class="form-control form-control-sm select2" id="filter-bulanan">
                                <option value="">Pilih Bulan</option>
                                <option value="1">Januari</option>
                                <option value="2">Februari</option>
                                <option value="3">Maret</option>
                                <option value="4">April</option>
                                <option value="5">Mei</option>
                                <option value="6">Juni</option>
                                <option value="7">Juli</option>
                                <option value="8">Agustus</option>
                                <option value="9">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label class="text-muted small mb-2">Harian</label>
                        <div class="input-group input-group-outline input-group-sm mb-3">
                            <select class="form-control form-control-sm select2" id="filter-harian">
                                <option value="">Pilih Tanggal</option>
                                <option value="1">01</option>
                                <option value="2">02</option>
                                <option value="3">03</option>
                                <option value="4">04</option>
                                <option value="5">05</option>
                                <option value="6">06</option>
                                <option value="7">07</option>
                                <option value="8">08</option>
                                <option value="9">09</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                                <option value="13">13</option>
                                <option value="14">14</option>
                                <option value="15">15</option>
                                <option value="16">16</option>
                                <option value="17">17</option>
                                <option value="18">18</option>
                                <option value="19">19</option>
                                <option value="20">20</option>
                                <option value="21">21</option>
                                <option value="22">22</option>
                                <option value="23">23</option>
                                <option value="24">24</option>
                                <option value="25">25</option>
                                <option value="26">26</option>
                                <option value="27">27</option>
                                <option value="28">28</option>
                                <option value="29">29</option>
                                <option value="30">30</option>
                                <option value="31">31</option>
                            </select>
                        </div>
                    </div>
                </div>
                    <div class="d-flex justify-content-end mt-3 mb-0">
                        <button type="button" class="btn btn-danger text-white btn-sm mb-0" id="btnDetailJurnalUmum">
                            <i class="bi bi-receipt-cutoff me-1"></i> Detail Transaksi
                        </button>
                    </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('modal')
    <div class="modal fade modal-fullscreen" id="detailJurnal" tabindex="-1">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content" style="border-radius:0">
                <div class="modal-header" style="border-radius:0">
                    <h5 class="modal-title">
                        <i class="bi bi-journal-text me-1"></i> Detail Jurnal Umum
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" id="detailJurnalContent">
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-info-circle fs-2"></i>
                        <p class="mt-2">Memuat detail jurnal umum...</p>
                    </div>
                </div>

                <div class="modal-footer
                            flex-column flex-sm-row
                            justify-content-end gap-2
                            position-sticky bottom-0 bg-white border-top"
                     style="border-radius:0">
                    <button type="button" class="btn btn-secondary w-100 w-sm-auto" id="btnCetakJurnal">
                        <i class="bi bi-printer me-1"></i> Cetak Bukti Transaksi
                    </button>
                    <button type="button" class="btn btn-danger btn-close-modal w-100 w-sm-auto" id="btnTutupDetailJurnal">
                        <i class="bi bi-x-circle me-1"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-fullscreen" id="cetakJurnalModal" tabindex="-1">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content" style="border-radius:0">
                <div class="modal-header" style="border-radius:0">
                    <h5 class="modal-title">
                        <i class="bi bi-list-check me-1"></i> Pilih Transaksi Jurnal Umum
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="cetakJurnalContent">
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-info-circle fs-2"></i>
                        <p class="mt-2">Memuat bukti transaksi...</p>
                    </div>
                </div>
                <div class="modal-footer
                            flex-column flex-sm-row
                            justify-content-end gap-2
                            position-sticky bottom-0 bg-white border-top"
                     style="border-radius:0">
                    <button type="button" class="btn btn-success w-100 w-sm-auto" id="btnCetakJurnalPilih">
                        <i class="bi bi-printer-fill me-1"></i> Cetak
                    </button>
                    <button type="button" class="btn btn-danger btn-close-modal w-100 w-sm-auto" id="btnTutupCakboxJurnal">
                        <i class="bi bi-x-circle me-1"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script>
const REKENING = @json($rekening);
const INVENTARIS = { id: 0, jumlah: 0, nilai_buku: 0 };

function numberUnformat(val) {
    if (!val) return 0;
    return parseFloat(val.toString().replace(/[^0-9-]/g, '')) || 0;
}

function numberFormat(val) {
    val = Number(val) || 0;
    return val.toLocaleString('id-ID');
}

function setSaldo(sumber_dana, tgl_iso) {
    if (!sumber_dana || !tgl_iso) return;
    var parts = tgl_iso.split('-');
    var tahun = parts[0];
    var bulan = parts[1];
    var hari  = parts[2];
    $.get('/app/transaksi/saldo/' + encodeURIComponent(sumber_dana),
        { tahun: tahun, bulan: bulan, hari: hari },
        function (r) {
            var s = parseFloat(r.saldo) || 0;
            $('#saldo').text(numberFormat(s));
            $('#saldo_trx').val(s);
            $('#saldo-wrapper').toggleClass('text-success', s >= 0).toggleClass('text-danger', s < 0);
        }
    );
}

$(document).ready(function () {
    $('.select2').select2({ theme: 'bootstrap-5', allowClear: false });
    $('#tanggal').flatpickr();
    $('.nominal').maskMoney({ allowNegative: true });

    var now = new Date();
    var y = now.getFullYear();
    var m = now.getMonth() + 1;
    var d = now.getDate();

    var $tahun = $('#filter-tahunan');
    var $bulan = $('#filter-bulanan');
    var $hari = $('#filter-harian');

    $tahun.empty().append(new Option('Pilih Tahun', '', false, false));
    for (var i = y - 2; i <= y + 1; i++) {
        $tahun.append(new Option(i, i, i === y, i === y));
    }
    $tahun.val(y).trigger('change.select2');

    function daysInMonth(year, month1to12) {
        return new Date(year, month1to12, 0).getDate();
    }

    function setHariOptions() {
        var tahun = $tahun.val();
        var bulan = parseInt($bulan.val(), 10);
        var prev = $hari.val();
        $hari.empty().append(new Option('-- Semua Tanggal (Full Bulan) --', '', false, false));
        var max = (tahun && bulan) ? daysInMonth(tahun, bulan) : 31;
        for (var i = 1; i <= max; i++) {
            var s = String(i).padStart(2, '0');
            $hari.append(new Option(s, i, false, false));
        }
        if (prev && parseInt(prev, 10) <= max) $hari.val(prev);
        else if (tahun && bulan && parseInt(tahun, 10) === y && bulan === m) $hari.val(d);
        else $hari.val('');
        $hari.prop('disabled', !(tahun && bulan));
        $hari.trigger('change.select2');
    }

    $bulan.prop('disabled', !$tahun.val());
    setHariOptions();

    $(document).on('change', '#filter-tahunan', function () {
        $bulan.prop('disabled', !$tahun.val());
        $bulan.val('').trigger('change.select2');
        $hari.val('').trigger('change.select2');
        setHariOptions();
    });

    $(document).on('change', '#filter-bulanan', function () {
        setHariOptions();
    });
});

$(document).on('change', '#tanggal', function () {
    ambilDaftarInventaris($(this).val());
    var sd = $('#sumber_dana').val();
    if (sd) setSaldo(sd, $(this).val());
});

$(document).on('change', '#jenis_transaksi', function () {
    var jenis_transaksi = $(this).val();
    var sumber_dana = [];
    var disimpan_ke = [];
    var label_sumber_dana = 'Sumber Dana';
    var label_disimpan_ke = 'Disimpan Ke';

    if (jenis_transaksi == '1') {
        sumber_dana = REKENING.filter(item =>
            (item.lev1 == '2' || item.lev1 == '3' || item.lev1 == '4') &&
            !['2.1.04.01','2.1.04.02','2.1.04.03','2.1.02.01','2.1.03.01'].includes(item.kode_akun) &&
            !item.kode_akun.startsWith('4.1.01')
        ).map(item => ({ id: item.kode_akun, text: item.kode_akun + '. ' + item.nama_akun }));

        disimpan_ke = REKENING.filter(item => item.lev1 == '1')
            .map(item => ({ id: item.kode_akun, text: item.kode_akun + '. ' + item.nama_akun }));
    }

    if (jenis_transaksi == '2') {
        sumber_dana = REKENING.filter(item =>
            (item.lev1 == '1' || item.lev1 == '2') &&
            !item.kode_akun.startsWith('2.1.04')
        ).map(item => ({ id: item.kode_akun, text: item.kode_akun + '. ' + item.nama_akun }));

        disimpan_ke = REKENING.filter(item =>
            item.lev1 == '2' || item.lev1 == '3' || item.lev1 == '5'
        ).map(item => ({ id: item.kode_akun, text: item.kode_akun + '. ' + item.nama_akun }));

        label_disimpan_ke = 'Keperluan';
    }

    if (jenis_transaksi == '3') {
        sumber_dana = REKENING.map(item => ({ id: item.kode_akun, text: item.kode_akun + '. ' + item.nama_akun }));
        disimpan_ke = REKENING.map(item => ({ id: item.kode_akun, text: item.kode_akun + '. ' + item.nama_akun }));
    }

    setFormSelect2('#sumber_dana', sumber_dana);
    setFormSelect2('#disimpan_ke', disimpan_ke);

    $('label[for="sumber_dana"]').text(label_sumber_dana);
    $('label[for="disimpan_ke"]').text(label_disimpan_ke);
});

$(document).on('change', '#sumber_dana, #disimpan_ke', function () {
    var jenis_transaksi = $('#jenis_transaksi').val();
    var sumber_dana = $('#sumber_dana').val();
    var disimpan_ke = $('#disimpan_ke').val();

    var sd = REKENING.find(i => i.kode_akun == sumber_dana);
    var dk = REKENING.find(i => i.kode_akun == disimpan_ke);

    if ($(this).attr('id') == 'sumber_dana' && sd) {
        setSaldo(sumber_dana, $('#tanggal').val());
    }

    var keterangan = '';

    if (sd) {
        if (jenis_transaksi == '1') {
            keterangan = 'Dari ' + sd.nama_akun;
            if (dk) keterangan += ' ke ' + dk.nama_akun;
        }
        if (jenis_transaksi == '2') {
            if (sd.kode_akun.startsWith('1.1.01')) keterangan = 'Bayar ';
            if (sd.kode_akun.startsWith('1.1.02')) keterangan = 'Transfer ';
            if (dk) keterangan += dk.nama_akun;
        }
        if (jenis_transaksi == '3') {
            keterangan = 'Pemindahan Saldo ' + sd.nama_akun;
            if (dk) keterangan += ' ke ' + dk.nama_akun;
        }
    }

    $('#keterangan_transaksi').val(keterangan);

    if (sd && dk) handleFormTransaksi(sd, dk, jenis_transaksi);
});

$(document).on('submit', '#FormTransaksi', function (e) {
    e.preventDefault();

    var jenis_transaksi = $('#jenis_transaksi').val();
    var sumber_dana = $('#sumber_dana').val();
    var nominal = numberUnformat($('#nominal').val());

    if (jenis_transaksi == '2' && sumber_dana && nominal > 0) {
        var sd = REKENING.find(i => i.kode_akun == sumber_dana);
        var lev1 = sd ? sd.lev1 : null;
        var skipCek = ['2', '3', '4', '5'].indexOf(String(lev1)) !== -1
            || (sumber_dana.startsWith('1.2.02.')
                || sumber_dana.startsWith('1.2.04.')
                || sumber_dana.startsWith('1.1.04.'));

        if (!skipCek) {
            var saldo_rek = parseFloat($('#saldo_trx').val()) || 0;
            if (saldo_rek < nominal) {
                Swal.fire('Error', 'Nominal transaksi melebihi saldo', 'error');
                return false;
            }
        }
    }

    $.ajax({
        type: 'POST',
        url: $(this).attr('action'),
        data: $(this).serialize(),
        success: function (r) {
            if (!r.success) return;

            Swal.fire('Berhasil!', r.message, 'success');

            $('#FormTransaksi')[0].reset();
            $('.select2').val(null).trigger('change');
            $('#saldo').text('0');
            $('#saldo_trx').val(0);
        },
        error: function (xhr) {
            Swal.fire(
                'Gagal!',
                xhr.responseJSON?.error || 'Terjadi kesalahan',
                'error'
            );
        }
    });
});


function handleFormTransaksi(sd, dk, jt) {
    if (sd.kode_akun.startsWith('1.2.01') && dk.kode_akun.startsWith('5.3.02.01') && jt == '2') {
        $('#form-jurnal-umum').hide();
        $('#form-beli-inventaris').hide();
        $('#form-hapus-inventaris').show();
        $('#jenis_inventaris').val(sd.kode_akun.startsWith('1.2.03') ? 'atb' : 'ati');
        $('#kategori_inventaris').val(sd.kode_akun.split('.').pop());
        $('#transaksi').val('hapus_inventaris');
        ambilDaftarInventaris($('#tanggal').val());
        return;
    }

    if (dk.kode_akun.startsWith('1.2.01') || dk.kode_akun.startsWith('1.2.03')) {
        $('#form-jurnal-umum').hide();
        $('#form-beli-inventaris').show();
        $('#form-hapus-inventaris').hide();
        $('#jenis_inventaris').val(dk.kode_akun.startsWith('1.2.03') ? 'atb' : 'ati');
        $('#kategori_inventaris').val(dk.kode_akun.split('.').pop());
        $('#transaksi').val('beli_inventaris');
        return;
    }

    $('#form-jurnal-umum').show();
    $('#form-beli-inventaris').hide();
    $('#form-hapus-inventaris').hide();
    $('#transaksi').val('jurnal_umum');
}

function ambilDaftarInventaris(tanggal) {
    if (!$('#jenis_inventaris').val() || !$('#kategori_inventaris').val()) return;

    $.get('/app/transaksi/daftar-inventaris', {
        tanggal: tanggal,
        jenis: $('#jenis_inventaris').val(),
        kategori: $('#kategori_inventaris').val()
    }).done(function (res) {
        setFormSelect2('#daftar_barang', res.map(item => ({
            id: item.id + '#' + item.jumlah + '#' + item.nilai_buku,
            text: item.id + '. ' + item.nama + ' (' + item.jumlah + ' unit x ' +
                numberFormat(item.harga_satuan) + ') | NB. ' + numberFormat(item.nilai_buku)
        })));
    });
}

function setFormSelect2(target, data) {
    var el = $(target);
    el.empty();
    el.append(new Option('Select Value', '', true, true));
    data.forEach(opt => el.append(new Option(opt.text, opt.id, false, false)));
    el.trigger('change');
}

function loadJurnalUmumDetail() {
    let modal = '#detailJurnal';
    let content = '#detailJurnalContent';
    $(content).html(`
        <div class="text-center py-5">
            <div class="spinner-border text-danger"></div>
            <p class="mt-2">Memuat detail jurnal umum...</p>
        </div>
    `);
    $(modal).modal('show');

    $.get('/app/Transaksi/jurnal-umum/detail', {
        tahun: $('#filter-tahunan').val() || '',
        bulan: $('#filter-bulanan').val() || '',
        tanggal: $('#filter-harian').val() || ''
    })
        .done(function (res) {
            $(content).html(res);
        })
        .fail(function () {
            $(content).html(`
                <div class="alert alert-danger">
                    Detail jurnal umum gagal dimuat. Coba lagi beberapa saat.
                </div>
            `);
        });
}

$(document).on('show.bs.dropdown', '#tableJurnalUmum .dropdown', function () {
    const dropdown = this;
    const $dropdown = $(dropdown);
    const $toggle = $dropdown.find('[data-bs-toggle="dropdown"]');
    const $menu = $dropdown.find('.dropdown-menu');
    const rect = $toggle[0].getBoundingClientRect();

    $menu.data('dropdown-parent', dropdown).appendTo(document.body).addClass('show').css({
        position: 'fixed',
        top: rect.bottom + 'px',
        left: Math.max(8, rect.right - $menu.outerWidth()) + 'px',
        zIndex: 13050
    });
});

$(document).on('hidden.bs.dropdown', '#tableJurnalUmum .dropdown', function () {
    const dropdown = this;
    $('body > .dropdown-menu').filter(function () {
        return $(this).data('dropdown-parent') === dropdown;
    }).removeClass('show').removeAttr('style').removeData('dropdown-parent').appendTo(dropdown);
});

$(document).on('click', '#btnDetailJurnalUmum', function () {
    loadJurnalUmumDetail();
});

$(document).on('click', '#btnCetakJurnal', function () {
    const params = new URLSearchParams({
        tahun: $('#filter-tahunan').val() || '',
        bulan: $('#filter-bulanan').val() || '',
        tanggal: $('#filter-harian').val() || ''
    });
    $('#cetakJurnalContent').html(`
        <div class="text-center text-muted py-5">
            <div class="spinner-border text-danger"></div>
            <p class="mt-2">Memuat bukti transaksi...</p>
        </div>
    `);
    $('#cetakJurnalModal').modal('show');
    $.get('/app/Transaksi/jurnal-umum/cetak', params.toString())
        .done(function (res) {
            $('#cetakJurnalContent').html(res);
        })
        .fail(function () {
            $('#cetakJurnalContent').html(`
                <div class="alert alert-danger">Bukti transaksi gagal dimuat.</div>
            `);
        });
});

$(document).on('change', '#cetakJurnalContent #checkAllJurnal', function () {
    $('#cetakJurnalContent .checkItemJurnal').prop('checked', $(this).is(':checked'));
});

$(document).on('change', '#cetakJurnalContent .checkItemJurnal', function () {
    const $items = $('#cetakJurnalContent .checkItemJurnal');
    $('#cetakJurnalContent #checkAllJurnal').prop(
        'checked',
        $items.filter(':checked').length === $items.length && $items.length > 0
    );
});

$(document).on('click', '#btnCetakJurnalPilih', function () {
    const $checked = $('#cetakJurnalContent .checkItemJurnal:checked');
    if ($checked.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Pilih minimal 1 transaksi untuk dicetak'
        });
        return;
    }
    const ids = $checked.map(function () { return $(this).val(); }).get();
    window.open('/app/Transaksi/jurnal-umum/printDokumen/cetak?ids=' + ids.join(','), '_blank');
});

$(document).on('click', '#detailJurnal .btn-close-modal, #cetakJurnalModal .btn-close-modal, #btnTutupDetailJurnal, #btnTutupCakboxJurnal', function () {
    $('.modal.show').modal('hide');
});
</script>
@endsection
