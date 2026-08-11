@extends('layouts.tenant.base')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-body px-3 py-3">
                    <div class="table-responsive">
                        <table id="invoices" class="table table-striped table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 60px;">#</th>
                                    <th>Jenis Pembayaran</th>
                                    <th style="width: 130px;">Tgl Invoice</th>
                                    <th style="width: 130px;">Tgl Lunas</th>
                                    <th style="width: 110px;">Status</th>
                                    <th class="text-end" style="width: 160px;">Jumlah</th>
                                    <th class="text-center" style="width: 90px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $('#invoices').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('app.pengaturan.invoice.data') }}',
                paging: true,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
                searching: true,
                info: true,
                autoWidth: true,
                scrollX: true,
                order: [[2, 'desc']],
                language: {
                    lengthMenu: 'Tampilkan _MENU_ data',
                    search: 'Cari:',
                    info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                    infoEmpty: 'Tidak ada data',
                    zeroRecords: 'Data tidak ditemukan',
                    paginate: { previous: 'Sebelumnya', next: 'Berikutnya' }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center text-muted' },
                    { data: 'jenis_pembayaran', name: 'jenis_pembayaran', className: 'fw-semibold' },
                    { data: 'tgl_invoice_fmt', name: 'tgl_invoice', className: 'text-nowrap' },
                    { data: 'tgl_lunas_fmt', name: 'tgl_lunas', className: 'text-nowrap' },
                    { data: 'status_badge', name: 'status', orderable: false, searchable: false },
                    { data: 'jumlah_fmt', name: 'jumlah', className: 'text-end fw-semibold text-nowrap' },
                    { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'print_url', name: 'print_url', visible: false },
                ],
                rowCallback: function (row, data) {
                    $(row).addClass('row-invoice').attr('data-href', data.print_url).css('cursor', 'pointer');
                }
            });

            $('#invoices').on('click', 'tr.row-invoice', function (e) {
                if ($(e.target).closest('a, .btn').length) return;
                const href = $(this).data('href');
                if (href) window.open(href, '_blank');
            });
        });
    </script>
@endsection
