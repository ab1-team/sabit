@php
    use App\Utils\Tanggal;
    use App\Utils\Angka;
@endphp

<style>
    #detailJurnalContent .table-responsive,
    #detailJurnalContent .dataTables_wrapper,
    #detailJurnalContent .dataTables_scroll,
    #detailJurnalContent .dataTables_scrollBody,
    #detailJurnalContent .dataTables_scrollHead,
    #detailJurnalContent .card,
    #detailJurnalContent .card-body {
        overflow: visible !important;
    }

    #detailJurnalContent .dropdown-menu {
        z-index: 13050 !important;
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="card m-0" style="border-radius:0">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tableJurnalUmum" class="table align-items-center table-striped w-100">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tanggal</th>
                                <th>Kode Akun</th>
                                <th>Nama Akun</th>
                                <th>Keterangan</th>
                                <th>Nominal</th>
                                <th>Pengguna</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="text-end mt-2 fw-bold">
                    Jumlah: {{ Angka::format($total, 0) }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const $tbl = $('#tableJurnalUmum');
    if ($.fn.dataTable.isDataTable($tbl)) {
        $tbl.DataTable().destroy();
    }

    const params = new URLSearchParams({
        tahun: $('#filter-tahunan').val() || '',
        bulan: $('#filter-bulanan').val() || '',
        tanggal: $('#filter-harian').val() || ''
    });

    $tbl.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/app/transaksi/jurnal-umum/data?' + params.toString(),
            type: 'GET'
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'tgl_fmt', name: 'tanggal_transaksi' },
            { data: 'kode_akun', name: 'kode_akun', orderable: false, searchable: false },
            { data: 'rekening_debit_nama', name: 'nama_akun', orderable: false, searchable: false,
                render: function (data, type, row) {
                    const d = data || '-';
                    const k = row.rekening_kredit_nama || '-';
                    return '<div class="small"><div>' + d + '</div><div>' + k + '</div></div>';
                }
            },
            { data: 'ket', name: 'keterangan' },
            { data: 'nominal_fmt', name: 'jumlah', className: 'text-end' },
            { data: 'user', name: 'user', orderable: false, searchable: false },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-center text-nowrap' }
        ],
        columnDefs: [
            { targets: 7, width: '350px' }
        ],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        order: [[1, 'desc']],
        scrollX: false,
        autoWidth: false,
        drawCallback: function () {}
    });
})();
</script>

