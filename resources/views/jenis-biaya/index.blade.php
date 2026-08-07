@extends('layouts.tenant.base')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-2 mx-md-3 z-index-2">
                    <div class="bg-gradient-secondary shadow-secondary border-radius-lg pt-3 pb-0">
                        <div class="row align-items-center g-2 px-2 px-md-3">
                            <div class="col-12 col-lg-5">
                                <h6 class="text-white text-capitalize mb-0" id="headerTitle">
                                </h6>
                            </div>
                            <div class="col-12 col-lg-7">
                                <div class="d-flex
                                            flex-column flex-lg-row
                                            align-items-stretch align-items-lg-center
                                            justify-content-lg-end
                                            gap-2">
                                    <button type="button" id="btnTambah"
                                        class="btn btn-primary text-white w-100 w-lg-auto">
                                        Tambah Jenis Bayar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body px-2 px-md-3">
                    <div id="tableWrapper">
                        <table id="keuangan" class="table align-items-center table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Angkatan</th>
                                    <th>Kode Rekening</th>
                                    <th>Jenis Pembayaran</th>
                                    <th>Nominal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div class="toast fade hide p-2 mt-2 bg-white" role="alert" aria-live="assertive" id="warningToast"
            aria-atomic="true">
            <div class="toast-header border-0">
                <i class="material-symbols-rounded text-warning me-2">travel_explore</i>
                <span class="me-auto font-weight-bold">{{ $appName }}</span>
                <small class="text-body">Now</small>
                <i class="fas fa-times text-md ms-3 cursor-pointer" data-bs-dismiss="toast" aria-label="Close"></i>
            </div>
            <div class="toast-body text-dark">
                "Maaf! Tidak ada data untuk ditampilkan".
            </div>
        </div>
    </div>


    <form action="" method="post" id="FormHapusBiaya">
        @method('DELETE')
        @csrf
    </form>
@endsection

@section('modal')
    <div class="modal fade modal-fullscreen-md-down" id="formModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formModalTitle">Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="formModalBody"></div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        //index
        $(document).ready(function() {
            const table = $('#keuangan').DataTable({
                dom: '<"row mb-3"<"col-md-6"l><"col-md-6">>rt<"row mt-3"<"col-md-5"i><"col-md-7"p>>',
                processing: true,
                serverSide: true,
                responsive: true,   // ⬅️ INI KUNCI
                scrollX: true,
                autoWidth: true,
                language: {
                    emptyTable: "Maaf! Data kosong"
                },
                ajax: {
                    url: '/app/jenis-biaya',
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        width: '5%'
                    },
                    {
                        data: 'angkatan',
                        name: 'angkatan',
                        className: 'text-start ps-4'
                    },
                    {
                        data: 'kode_akun',
                        name: 'kode_akun',
                        className: 'text-start'
                    },
                    {
                        data: 'nama_jenis',
                        name: 'nama_jenis',
                        className: 'text-start'
                    },
                    {
                        data: 'total_beban',
                        name: 'total_beban',
                        className: 'text-end pe-4'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%',
                        className: 'text-center'
                    },
                ]
            });

            $('#Filter_angkatan').on('click', function() {
                table.ajax.reload();
            });

            $('#keuangan').on('xhr.dt', function(e, settings, json, xhr) {
                if (!json.data || json.data.length === 0) {
                    const toastEl = document.getElementById('warningToast');
                    const toast = new bootstrap.Toast(toastEl);
                    toast.show();
                }
            });

        });
        //hapus
        $(document).on('click', '.btnDelete', function(e) {
            e.preventDefault();

            var hapus_id = $(this).attr('data-id');
            var actionUrl = '/app/jenis-biaya/' + hapus_id;

            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Data akan dihapus secara permanen dari aplikasi dan tidak bisa dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Hapus",
                cancelButtonText: "Batal",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    var form = $('#FormHapusBiaya');
                    $.ajax({
                        type: form.attr('method'),
                        url: actionUrl,
                        data: form.serialize(),
                        success: function(result) {
                            if (result.success) {
                                Swal.fire({
                                    title: "Berhasil!",
                                    text: result.msg,
                                    icon: "success",
                                    confirmButtonText: "Oke"
                                }).then((res) => {
                                    if (res.isConfirmed) {
                                        window.location.reload();
                                    }
                                });
                            } else {
                                Swal.fire({
                                    title: "Gagal",
                                    text: result.msg,
                                    icon: "info",
                                    confirmButtonText: "Oke"
                                });
                            }
                        },
                        error: function(response) {
                            Swal.fire({
                                title: "Kesalahan",
                                text: "Terjadi kesalahan pada server. Silakan coba lagi.",
                                icon: "error",
                                confirmButtonText: "Oke"
                            });
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Dibatalkan",
                        text: "Data tidak jadi dihapus.",
                        icon: "info",
                        confirmButtonText: "Oke"
                    });
                }
            });
        });

        // modal tambah / edit
        const formModal = $('#formModal');

        function initFormPlugins() {
            $('#formModal .select2').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#formModal'),
                placeholder: "-- Pilih Jenis Pembayaran --",
                allowClear: true
            });
            $('#formModal .nominal').maskMoney({ allowNegative: true });
            $('#formModal #id_jp').on('change', function() {
                var opt = $(this).find(':selected');
                $('#formModal #kode_akun').val(opt.data('kode') || '');
                var jumlah = opt.data('jumlah');
                if (jumlah) {
                    $('#formModal input[name="total_beban"]').maskMoney('mask', jumlah);
                }
            });
        }

        function openFormModal(title, html) {
            $('#formModalTitle').text(title);
            $('#formModalBody').html(html);
            formModal.modal('show');
            initFormPlugins();
        }

        $('#btnTambah').on('click', function() {
            $.get('/app/jenis-biaya-create-form', function(res) {
                openFormModal('Tambah Nominal Keuangan', res.html);
            });
        });

        $(document).on('click', '.btnEdit', function() {
            var id = $(this).data('id');
            $.get('/app/jenis-biaya-edit-form/' + id, function(res) {
                openFormModal('Edit Nominal Keuangan', res.html);
            });
        });

        $(document).on('submit', '#FormJenisBiaya', function(e) {
            e.preventDefault();
            var form = $(this);
            var nominalInput = form.find('input[name="total_beban"]');
            if (nominalInput.length && typeof nominalInput.maskMoney === 'function') {
                nominalInput.val(nominalInput.maskMoney('unmasked')[0]);
            }
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize(),
                success: function(result) {
                    formModal.modal('hide');
                    Swal.fire('Berhasil', result.msg, 'success').then(() => {
                        window.location.reload();
                    });
                },
                error: function(xhr) {
                    var msg = 'Cek kembali input yang anda masukkan';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) msg = xhr.responseJSON.message;
                        else if (xhr.responseJSON.errors) {
                            var first = Object.values(xhr.responseJSON.errors)[0];
                            if (first && first[0]) msg = first[0];
                        }
                    }
                    Swal.fire('Error', msg, 'error');
                }
            });
        });
    </script>
@endsection
