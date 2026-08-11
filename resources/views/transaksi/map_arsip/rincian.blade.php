@php
    use App\Utils\Tanggal;
@endphp

<div class="row">
    <div class="col-12">
        <div class="card m-0" style="border-radius:0">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="keuangan" class="table align-items-center table-striped">
                        <thead>
                            <tr align="center">
                                <th width="6%">ID</th>
                                <th width="12%">Tanggal Trx</th>
                                <th width="14%">Kode Akun</th>
                                <th width="36%">Keterangan</th>
                                <th width="12%">Nominal</th>
                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($siswa->getTransaksi as $item)
                                <tr>
                                    <td align="center">{{ $item->id }}</td>
                                    <td align="center">

                                        {{ Tanggal::tglIndo($item->tanggal_transaksi) }}
                                    </td>
                                    <td align="center">
                                        <div class="small">
                                            <div>D: {{ $item->rekeningDebit->kode_akun ?? '-' }}</div>
                                            <div>K: {{ $item->rekeningKredit->kode_akun ?? '-' }}</div>
                                        </div>
                                    </td>
                                    <td>{{ $item->keterangan }}</td>
                                    <td align="right" style="padding-right: 10px;">
                                        {{ \App\Utils\Angka::format($item->getRawOriginal('jumlah'), 0) }}
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <div class="d-inline-flex gap-1">
                                            <a href="/app/transaksi/kwitansi-spp?ids={{ $item->id }}"
                                                target="_blank" class="btn btn-secondary btn-compact"
                                                title="Cetak Kwitansi">
                                                <i class="material-symbols-rounded">print</i>
                                            </a>
                                            <a href="/app/transaksi/cetakPadaKartu?ids={{ $item->id }}"
                                                target="_blank" class="btn btn-secondary btn-compact"
                                                title="Cetak Pada Kartu">
                                                <i class="material-symbols-rounded">credit_card</i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-compact btnDelete"
                                                data-id="{{ $item->id }}">
                                                <i class="material-symbols-rounded">delete</i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">
                                        Tidak ada transaksi SPP
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <td colspan="4" class="text-end">Jumlah</td>
                                <td class="text-end">
                                    {{ \App\Utils\Angka::format($siswa->getTransaksi->sum(fn($t) => $t->getRawOriginal('jumlah')), 0) }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
