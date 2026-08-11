@php
use App\Utils\Tanggal;
@endphp

<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="keuangan" class="table align-items-center table-striped">
                        <thead>
                            <tr>
                                <th width="10%" class="text-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="checkAll">
                                    </div>
                                </th>
                                <th width="15%">Tanggal Trx</th>
                                <th width="15%">Alokasi </th>
                                <th width="15%">Nominal</th>
                                <th width="45%">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($siswa->getTransaksi as $item)
                            <tr>
                                <td class="text-center">
                                    <div class="form-check">
                                        <input class="form-check-input checkItem" type="checkbox"
                                            value="{{ $item->id }}">
                                    </div>
                                </td>
                                <td>{{ Tanggal::tglIndo($item->tanggal_transaksi) }}</td>
                                <td>
                                    @if ($item->spp)
                                    {{ Tanggal::namabulan($item->spp->tanggal) }}
                                    @else
                                    Daftar Ulang
                                    @endif
                                </td>
                                <td>{{ \App\Utils\Angka::format($item->getRawOriginal('jumlah'), 0) }}</td>
                                <td>{{ $item->keterangan }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    Tidak ada transaksi SPP
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
