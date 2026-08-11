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
                                    <option value="">Semua Kelas</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-auto pt-2 ms-md-auto">
                                <button id="btnPrint" class="btn btn-success text-white w-100 w-md-auto">
                                    <i class="material-icons align-middle me-1">print</i>
                                    Cetak Siswa
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="card-body px-3 py-2">
                    <div id="notifikasi">
                        <div class="alert alert-light alert-dismissible text-secondary" role="alert">
                            Silakan pilih Tahun Akademik untuk menampilkan data siswa.
                            <button type="button" class="btn-close text-lg opacity-10" data-bs-dismiss="alert"></button>
                        </div>
                    </div>
                    <div class="table-responsive mt-3 d-none" id="tableWrapper">
                        <table id="siswa" class="table table-striped align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center">
                                        <div class="form-check d-flex justify-content-center">
                                            <input class="form-check-input" type="checkbox" id="checkAll">
                                        </div>
                                    </th>
                                    <th>NISN</th>
                                    <th>Nama</th>
                                    <th>Tahun Akademik</th>
                                    <th>Kode Kelas</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
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
@section('script')
    <script>
        $(document).ready(function() {

            let urlParams = new URLSearchParams(window.location.search);
            let qs_tahun = urlParams.get('tahun_akademik');
            let qs_kelas = urlParams.get('kelas');
            let tahunLoaded = false;
            let kelasLoaded = false;

            let table = $('#siswa').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                paging: true,
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                info: true,
                autoWidth: true,
                scrollX: true,
                rowId: 'id',
                ajax: {
                    url: '/app/siswa',
                    data: function(d) {
                        d.tahun_akademik = $('#tahun_akademik').val();
                        d.kelas = $('#kelas').val();
                    }
                },
                columns: [{
                        width: "9%",
                        data: 'checkbox',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        width: "15%",
                        name: 'nisn',
                        data: 'nisn'
                    },
                    {
                        width: "25%",
                        name: 'nama',
                        data: 'nama'
                    },
                    {
                        width: "15%",
                        name: 'tahun_akademik',
                        data: 'tahun_akademik'
                    },
                    {
                        width: "15%",
                        name: 'kode_kelas',
                        data: 'kode_kelas'
                    },
                    {
                        width: "10%",
                        data: 'status_siswa',
                        name: 'status_siswa',
                        render: function (data, type, row) {
                            if (type === 'display') {
                                if (data === 'aktif') {
                                    return '<span class="badge bg-success">Aktif</span>';
                                } 
                                else if (data === 'nonaktif') {
                                    return '<span class="badge bg-warning text-dark">Nonaktif</span>';
                                } 
                                else if (data === 'blokir') {
                                    return '<span class="badge bg-danger">Blokir</span>';
                                }
                                return '<span class="badge bg-secondary">Tidak Diketahui</span>';
                            }
                            return data;
                        }
                    },
                    {
                        width: "10%",
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center td-action'
                    }
                ],
                drawCallback: function() {
                    $('#siswa').css('width', '100%');
                }
            });

            function tryReloadTable() {
                if (!tahunLoaded || !kelasLoaded) return;

                let tahunVal = $('#tahun_akademik').val();

                if (!tahunVal) {
                    $('#notifikasi').removeClass('d-none');
                    $('#tableWrapper').addClass('d-none');
                    table.clear().draw();
                    return;
                }

                $('#notifikasi').addClass('d-none');
                $('#tableWrapper').removeClass('d-none');

                table.ajax.reload();
            }

            $.getJSON('/app/siswa/listTahun', function(data) {
                let tahun = $('#tahun_akademik');
                tahun.empty().append('<option value="">Pilih Tahun Akademik</option>');
                data.forEach(t => tahun.append(`<option value="${t.nama_tahun}">${t.nama_tahun}</option>`));
                tahun.select2({ theme: 'bootstrap-5' });

                if (qs_tahun) {
                    tahun.val(qs_tahun).trigger('change');
                }

                tahunLoaded = true;
                tryReloadTable();
            });

            $.getJSON('/app/siswa/listKelas', function(data) {
                let kelas = $('#kelas');
                kelas.empty().append('<option value="">Semua Kelas</option>');
                data.forEach(k => kelas.append(
                    `<option value="${k.kode_kelas}">${k.kode_kelas} - ${k.nama_kelas}</option>`));
                kelas.select2({ theme: 'bootstrap-5' });

                if (qs_kelas) {
                    kelas.val(qs_kelas).trigger('change');
                }

                kelasLoaded = true;
                tryReloadTable();
            });

            $('#siswa tbody').on('click', 'tr', function (e) {
                if (
                    $(e.target).closest('input[type="checkbox"]').length ||
                    $(e.target).closest('button').length ||
                    $(e.target).closest('a').length
                ) {
                    return;
                }

                let data = table.row(this).data();
                if (!data) return;

                let tahun = $('#tahun_akademik').val();
                let kelas = $('#kelas').val();

                window.location.href =
                    `/app/siswa/${data.id}?tahun_akademik=${tahun}&kelas=${kelas}`;
            });

            function applyFilter() {
                let tahun = $('#tahun_akademik').val();
                let kelas = $('#kelas').val();

                let params = new URLSearchParams(window.location.search);
                params.set('tahun_akademik', tahun);
                params.set('kelas', kelas);
                window.history.replaceState({}, '', `${location.pathname}?${params.toString()}`);

                if (!tahun) {
                    $('#notifikasi').removeClass('d-none');
                    $('#tableWrapper').addClass('d-none');
                    table.clear().draw();
                    return;
                }

                $('#notifikasi').addClass('d-none');
                $('#tableWrapper').removeClass('d-none');
                table.ajax.reload(function () {
                    table.columns.adjust().draw();
                });
            }

            $('#tahun_akademik, #kelas').on('change', applyFilter);

            $('#checkAll').on('click', function () {
                $('.checkItem').prop('checked', this.checked);

                if (this.checked) {
                    Swal.fire({
                        title: 'Aksi Massal',
                        html: `
                        <div class="row">
                            <div class="col-md-12">
                                <label class="form-label">Nama Kelas</label>
                                <div class="input-group input-group-outline mb-3">
                                    <select name="mutasi_kelas" id="mutasi_kelas" class="form-control kelas"></select>
                                </div>
                            </div>
                        </div>
                        `,
                        showCancelButton: true,
                        showDenyButton: true,
                        confirmButtonText: "Pindahkan Siswa",
                        denyButtonText: "Cetak Data Siswa",
                        cancelButtonText: "Batal",
                        width: 600,

                        didOpen: () => {
                            $.getJSON('/app/siswa/listKelas', function(data) {
                                let kelas = $('#mutasi_kelas');
                                kelas.empty().append('<option value="">Pilih Kelas</option>');

                                data.forEach(k => {
                                    kelas.append(`<option value="${k.kode_kelas}|${k.tingkat}">${k.kode_kelas} - ${k.nama_kelas}</option>`);
                                });
                                $('#mutasi_kelas').select2({
                                    dropdownParent: $('.swal2-popup'),
                                    theme: 'bootstrap-5',
                                    placeholder: "Pilih Kelas",
                                    allowClear: true
                                });
                            });
                        }
                    }).then((result) => {

                        if (result.isConfirmed) {
                            pindahkanViaButton(
                                $('#mutasi_kelas').val(),
                            );
                        }

                        else if (result.isDenied) {
                            $('#btnPrint')[0].click();
                        }

                        else {
                            $('#checkAll').prop('checked', false);
                            $('.checkItem').prop('checked', false);
                        }
                    });
                }
            });

            $(document).on('click', '.btnMutasi', function () {
                let ids = $('.checkItem:checked').map(function () {
                    return $(this).val();
                }).get();

                if (ids.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Belum ada siswa dipilih',
                        text: 'Silakan centang siswa yang ingin dipindahkan.',
                    });
                    return;
                }

                Swal.fire({
                    title: 'Mutasi Siswa',
                    html: `
                        <div class="row">
                            <div class="col-md-12">
                                <label class="form-label">Nama Kelas</label>
                                <div class="input-group input-group-outline mb-3">
                                    <select name="mutasi_kelas" id="mutasi_kelas" class="form-control kelas"></select>
                                </div>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: "Pindahkan",
                    didOpen: () => {
                        $.getJSON('/app/siswa/listKelas', function(data) {
                            let kelas = $('#mutasi_kelas');

                            kelas.empty().append('<option value="">Pilih Kelas</option>');

                            data.forEach(k => {
                                kelas.append(`<option value="${k.kode_kelas}|${k.tingkat}">${k.kode_kelas} - ${k.nama_kelas}</option>`);
                            });
                            $('#mutasi_kelas').select2({
                                dropdownParent: $('.swal2-popup'), 
                                theme: 'bootstrap-5',
                                placeholder: "Pilih Kelas",
                                allowClear: true
                            });
                        });
                    }
                }).then((r) => {
                    if (!r.isConfirmed) return;

                    pindahkanViaButton(
                        $('#mutasi_kelas').val(),
                        ids.join(',')
                    );
                });
            });
            
            $('#btnPrint').click(function() {
            let ids = [];

                if ($('#checkAll').is(':checked')) {
                    let allData = table.rows({ search: 'applied' }).data().toArray();
                    allData.forEach(row => {
                        ids.push(row.id);
                    });
                } else {
                    $('.checkItem:checked').each(function() {
                        ids.push($(this).val());
                    });
                }
                if (ids.length < 1) {
                    Swal.fire({
                        title: "Peringatan",
                        text: "Silakan pilih minimal satu siswa terlebih dahulu.",
                        icon: "warning",
                        confirmButtonText: "Oke"
                    });
                    return;
                }
                window.open('/app/siswa/printSiswa?ids=' + ids.join(','), '_blank');
            });
        });

        //mutasi
        function pindahkanViaButton(kelas, singleId = null) {

            let ids = [];

            if (singleId) {
                ids = singleId.split(',');
            } else {
                $('.checkItem:checked').each(function () {
                    ids.push($(this).val());
                });
            }

            if (ids.length < 1) {
                Swal.fire("Peringatan", "Tidak ada siswa yang dipilih", "info");
                return;
            }

            $("#loadingOverlay").show();
            $("button").prop("disabled", true);

            $.ajax({
                type: "POST",
                url: "/app/siswa/mutasi",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    ids: ids,
                    kelas: kelas
                },
                success: function (result) {

                    // Sembunyikan loading kembali
                    $("#loadingOverlay").hide();
                    $("button").prop("disabled", false);

                    if (result.success) {
                        Swal.fire({
                            title: "Berhasil!",
                            text: result.msg,
                            icon: "success",
                            confirmButtonText: "Oke"
                        }).then((res) => {
                            if (res.isConfirmed) {
                                window.location.href =
                                    '/app/siswa?tahun_akademik=' + $('#tahun_akademik').val() +
                                    '&kelas=' + $('#kelas').val();
                            }
                        });
                    } else {
                        Swal.fire("Gagal", result.msg, "error");
                    }
                },
                error: function () {

                    // Sembunyikan loading kembali
                    $("#loadingOverlay").hide();
                    $("button").prop("disabled", false);

                    Swal.fire("Galat", "Terjadi kesalahan pada server", "error");
                }
            });
        }
    </script>
@endsection
