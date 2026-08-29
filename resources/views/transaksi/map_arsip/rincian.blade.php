@php
    use App\Utils\Tanggal;
@endphp

<div class="row" data-siswa-id="{{ $siswa->id }}">
    <div class="col-12">
        <div class="card m-0" style="border-radius:0">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-2">
                    <h6 class="mb-0">
                        <i class="bi bi-person-lines-fill me-1"></i>
                        An. {{ $siswa->nama }}
                        <small class="text-muted">({{ $siswa->nisn }})</small>
                    </h6>
                    <div class="d-flex align-items-center gap-2">
                        <label for="filterTahunAkademik" class="form-label mb-0 small text-muted">Tahun Akademik:</label>
                        <select id="filterTahunAkademik" class="form-select form-select-sm select2" style="min-width: 160px;">
                            @forelse ($tahunList ?? [] as $ta)
                                <option value="{{ $ta }}" @selected($ta === ($tahunDipilih ?? null))>
                                    {{ $ta }}
                                </option>
                            @empty
                                <option value="">(Tidak ada)</option>
                            @endforelse
                        </select>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="keuangan" class="table align-items-center table-striped">
                        <thead>
                            <tr align="center">
                                <th width="6%">ID</th>
                                <th width="14%">Tanggal Trx</th>
                                <th width="46%">Keterangan</th>
                                <th width="14%">Nominal</th>
                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($siswa->transaksi as $item)
                                <tr>
                                    <td align="center">{{ $item->id }}</td>
                                    <td align="center">

                                        {{ Tanggal::tglIndo($item->tanggal_transaksi) }}
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
                                    <td colspan="5" class="text-center text-muted">
                                        @if (!empty($tahunDipilih))
                                            Tidak ada transaksi untuk tahun akademik
                                            <strong>{{ $tahunDipilih }}</strong>
                                        @else
                                            Tidak ada transaksi SPP
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <td colspan="3" class="text-end">Jumlah</td>
                                <td class="text-end">
                                    {{ \App\Utils\Angka::format($siswa->transaksi->sum(fn($t) => $t->getRawOriginal('jumlah')), 0) }}
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
