@php
    use App\Utils\Tanggal;
    use App\Utils\Angka;
@endphp

<style>
    #detailJurnalContent .table-responsive {
        overflow-x: auto !important;
        overflow-y: visible !important;
    }

    #detailJurnalContent .dataTables_wrapper,
    #detailJurnalContent .dataTables_scroll,
    #detailJurnalContent .dataTables_scrollBody,
    #detailJurnalContent .dataTables_scrollHead {
        overflow: visible !important;
    }

    #detailJurnalContent .dataTables_scrollBody {
        overflow-x: auto !important;
    }

    #detailJurnalContent .dropdown-menu {
        z-index: 13050 !important;
    }

    #detailJurnalContent #tableJurnalUmum {
        width: 100% !important;
        table-layout: fixed;
        border-collapse: separate;
        border-spacing: 0;
    }

    #detailJurnalContent #tableJurnalUmum thead th,
    #detailJurnalContent #tableJurnalUmum tbody td {
        padding: 0.65rem 0.9rem;
        vertical-align: middle;
    }

    #detailJurnalContent #tableJurnalUmum thead th {
        white-space: nowrap;
        font-weight: 600;
        background-color: #f8f9fa;
    }

    #detailJurnalContent #tableJurnalUmum tbody td {
        white-space: normal;
        word-break: break-word;
        border-bottom: 1px solid #f1f3f5;
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
                                <th>Keterangan</th>
                                <th>Nominal</th>
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
            { data: 'ket', name: 'keterangan' },
            { data: 'nominal_fmt', name: 'jumlah', className: 'text-end' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-center text-nowrap' }
        ],
        columnDefs: [
            { targets: 0, width: '70px', className: 'text-center' },
            { targets: 1, width: '120px' },
            { targets: 2, width: '140px' },
            { targets: 3, width: 'auto' },
            { targets: 4, width: '160px', className: 'text-end' },
            { targets: 5, width: '220px', className: 'text-center text-nowrap' }
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

