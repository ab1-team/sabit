@extends('layouts.tenant.base')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-secondary shadow-secondary border-radius-lg py-2 px-3 d-flex align-items-center">
                        <div class="row align-items-center w-100 gx-2">
                            <div class="col-12 col-md-3">
                                <select id="tahun_akademik" class="form-control select2 text-white w-100">
                                    <option value="">Pilih Tahun Akademik</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-3">
                                <select id="kelas" class="form-control select2 text-white w-100">
                                    <option value="__all__">Pilih Kelas</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-3">
                                <select id="cetak_batch" class="form-control select2 text-white w-100"
                                    title="Cetak kartu seluruh siswa di kelas yang sedang dipilih">
                                    <option value="">Cetak Kartu…</option>
                                    <option value="kartu_spp">Cetak Kartu SPP</option>
                                    <option value="uts1">Cetak Kartu UTS I</option>
                                    <option value="pas1">Cetak Kartu PAS I</option>
                                    <option value="uts2">Cetak Kartu UTS II</option>
                                    <option value="pas2">Cetak Kartu PAS II</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-auto pt-2 ms-md-auto">
                                <span class="text-white small">
                                    <i class="material-icons align-middle me-1" style="font-size:16px">event</i>
                                    Status: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body px-3 py-2">
                    <div id="notifikasi" class="d-none"></div>
                    <div class="table-responsive mt-3" id="tableWrapper">
                        <table id="daftarkelas" class="table table-striped align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center" width="4%">No</th>
                                    <th width="10%">NISN</th>
                                    <th width="23%">Nama Siswa</th>
                                    <th class="text-end" width="11%">SPP / Bulan</th>
                                    <th class="text-end" width="13%">Kom. Tagihan Bulan Ini</th>
                                    <th class="text-center" width="14%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="loadingOverlay"
        style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
        background:rgba(255,255,255,0.8); z-index:9999; text-align:center;">

        <div style="position:absolute; top:45%; left:50%; transform:translate(-50%,-50%);">
            <div class="spinner-border text-primary" style="width:3rem; height:3rem;"></div>
            <p class="mt-3 fw-bold">Memproses data...</p>
        </div>
    </div>
@endsection
@section('modal')
    <div class="modal fade" id="detailTagihanSpp" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex flex-column">
                        <h5 class="modal-title mb-1">
                            <i class="bi bi-receipt-cutoff me-1"></i> Detail Tagihan SPP
                        </h5>
                        <small class="text-muted modal-title-nama lh-sm">—</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailTagihanSppContent">
                    <div class="text-center text-muted py-5">
                        <div class="spinner-border text-primary"></div>
                        <p class="mt-2">Klik salah satu baris siswa untuk melihat detail tagihan.</p>
                    </div>
                </div>
                <div class="modal-footer justify-content-end gap-2">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {

            let urlParams = new URLSearchParams(window.location.search);
            let qs_tahun = urlParams.get('tahun_akademik') || @json($tahunBerjalan);
            let qs_kelas = urlParams.get('kelas');
            let tahunLoaded = false;
            let kelasLoaded = false;

            let table = $('#daftarkelas').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                paging: true,
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "Semua"]
                ],
                info: true,
                autoWidth: false,
                scrollX: true,
                rowId: 'id',
                ajax: {
                    url: '/app/daftar-kelas/data',
                    data: function(d) {
                        d.tahun_akademik = $('#tahun_akademik').val();
                        d.kelas = $('#kelas').val();
                    }
                },
                columns: [{
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        width: '4%',
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        name: 'nisn',
                        data: 'nisn',
                        width: '10%',
                        defaultContent: '-',
                        render: function(d) {
                            return (d === '0' || !d) ? '-' : d;
                        }
                    },
                    {
                        name: 'nama',
                        data: 'nama',
                        width: '23%'
                    },
                    {
                        data: 'spp_per_bulan',
                        searchable: false,
                        width: '11%',
                        className: 'text-end',
                        render: function(d) {
                            return formatRupiah(d);
                        }
                    },
                    {
                        data: 'tagihan_bulan_ini',
                        searchable: false,
                        width: '13%',
                        className: 'text-end',
                        render: function(d, type, row) {
                            let status = row.status_tagihan ?? 'menunggak';
                            let cls = 'text-danger fw-bold';
                            if (status === 'lebih') cls = 'text-success fw-bold';
                            else if (status === 'pas') cls = 'text-warning fw-bold';
                            return `<span class="${cls}">${formatRupiah(d)}</span>`;
                        }
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center td-action',
                        width: '14%'
                    }
                ],
                columnDefs: [{
                    targets: '_all',
                    className: 'align-middle'
                }],
                drawCallback: function() {
                    $('#daftarkelas tbody tr').css('cursor', 'pointer');
                }
            });

            $(document).on('click', '#daftarkelas tbody tr', function(e) {
                if ($(e.target).closest('a, button').length) return;

                let row = table.row(this).data();
                if (!row || !row.id) return;

                let modal = '#detailTagihanSpp';
                let content = '#detailTagihanSppContent';
                let kelasTxt = row.kode_kelas ? ', Kelas ' + row.kode_kelas : '';
                $(modal).find('.modal-title-nama').text((row.nama || '—') + kelasTxt);
                $(content).html(`
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary"></div>
                        <p class="mt-2 small mb-0">Memuat...</p>
                    </div>
                `);
                $(modal).modal('show');
                $.get('/app/transaksi/pembayaranSPPDetailTagihan/' + row.id)
                    .done(function(res) {
                        $(content).html(res);
                    })
                    .fail(function() {
                        $(content).html(`
                            <div class="alert alert-danger text-start mb-0">
                                <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle me-1"></i>Detail tagihan gagal dimuat</div>
                                <div class="small"><strong>Penyebab:</strong> siswa sudah dihapus / tidak ditemukan, atau terjadi gangguan jaringan.</div>
                                <div class="small mt-1"><strong>Solusi:</strong> tutup modal lalu klik baris siswa kembali.</div>
                            </div>
                        `);
                    });
            });

            function formatRupiah(num) {
                if (num === null || num === undefined) return '0';
                return new Intl.NumberFormat('id-ID').format(num);
            }

            function tryReloadTable() {
                if (!tahunLoaded || !kelasLoaded) return;

                let tahunVal = $('#tahun_akademik').val();

                if (!tahunVal) {
                    table.clear().draw();
                    return;
                }

                table.ajax.reload();
            }

            $.getJSON('/app/daftar-kelas/listTahun', function(data) {
                let tahun = $('#tahun_akademik');
                tahun.empty().append('<option value="">Pilih Tahun Akademik</option>');
                data.forEach(t => tahun.append(`<option value="${t.nama_tahun}">${t.nama_tahun}</option>`));

                if (qs_tahun && data.some(t => t.nama_tahun === qs_tahun)) {
                    tahun.val(qs_tahun);
                } else if (data.length > 0) {
                    tahun.val(data[0].nama_tahun);
                }

                tahun.select2({
                    theme: 'bootstrap-5'
                });
                tahunLoaded = true;
                tryReloadTable();
            });

            $.getJSON('/app/daftar-kelas/listKelas', function(data) {
                let kelas = $('#kelas');
                kelas.empty().append('<option value="__all__">Pilih Kelas</option>');
                data.forEach(k => kelas.append(
                    `<option value="${k.kode_kelas}">${k.kode_kelas} - ${k.nama_kelas}</option>`));

                if (qs_kelas && (qs_kelas === '__all__' || data.some(k => k.kode_kelas === qs_kelas))) {
                    kelas.val(qs_kelas);
                } else {
                    kelas.val('I.A');
                }

                kelas.select2({
                    theme: 'bootstrap-5'
                });
                kelasLoaded = true;
                tryReloadTable();
            });

            function applyFilter() {
                let tahun = $('#tahun_akademik').val();
                let kelas = $('#kelas').val();

                let params = new URLSearchParams(window.location.search);
                if (tahun) params.set('tahun_akademik', tahun);
                else params.delete('tahun_akademik');
                if (kelas && kelas !== '__all__') params.set('kelas', kelas);
                else params.delete('kelas');
                window.history.replaceState({}, '', `${location.pathname}?${params.toString()}`);

                if (!tahun) {
                    table.clear().draw();
                    return;
                }

                table.ajax.reload(function() {
                    table.columns.adjust().draw();
                });
            }

            $('#tahun_akademik, #kelas').on('change', applyFilter);

            $('#cetak_batch').on('change', function() {
                let jenis = $(this).val();
                if (!jenis) return;

                let tahun = $('#tahun_akademik').val();
                let kelas = $('#kelas').val();

                if (!tahun) {
                    Swal.fire('Info', 'Pilih Tahun Akademik terlebih dahulu.', 'info');
                    $(this).val('').trigger('change.select2');
                    return;
                }
                if (!kelas || kelas === '__all__') {
                    Swal.fire('Info', 'Pilih Kelas terlebih dahulu.', 'info');
                    $(this).val('').trigger('change.select2');
                    return;
                }

                let url = '/app/daftar-kelas/cetak-kartu-batch?' + $.param({
                    jenis: jenis,
                    tahun_akademik: tahun,
                    kelas: kelas,
                });

                window.open(url, '_blank');
                $(this).val('').trigger('change.select2');
            });

            $('#cetak_batch').select2({
                theme: 'bootstrap-5',
                minimumResultsForSearch: Infinity,
            });
        });
    </script>
@endsection
