@php
    use App\Utils\Tanggal;
@endphp
<style>
    #tblSiswaMenunggak thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .4px;
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 2;
    }
    #tblSiswaMenunggak tbody td {
        vertical-align: middle;
        font-size: 13px;
    }
    #tblSiswaMenunggak .col-nisn {
        width: 110px;
        white-space: nowrap;
        font-variant-numeric: tabular-nums;
    }
    #tblSiswaMenunggak .col-nama {
        min-width: 200px;
    }
    #tblSiswaMenunggak .col-kelas {
        width: 80px;
        text-align: center;
    }
    #tblSiswaMenunggak .col-nominal {
        width: 140px;
        text-align: right;
        white-space: nowrap;
        font-variant-numeric: tabular-nums;
    }
    #tblSiswaMenunggak .col-total {
        width: 150px;
        text-align: right;
        white-space: nowrap;
        font-variant-numeric: tabular-nums;
    }
    #tblSiswaMenunggak .col-bulan {
        width: 230px;
    }
    #tblSiswaMenunggak .bulan-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        align-items: center;
        max-width: 220px;
    }
    #tblSiswaMenunggak .badge.bg-warning {
        background-color: #f59e0b !important;
        color: #fff;
        font-weight: 600;
        font-size: 10.5px;
        padding: 4px 8px;
        white-space: nowrap;
    }
    #tblSiswaMenunggak .badge.bg-secondary {
        background-color: #64748b !important;
        color: #fff;
        font-weight: 600;
        font-size: 10.5px;
        padding: 4px 8px;
        white-space: nowrap;
    }
</style>

<div class="table-responsive" style="max-height: 60vh;">
    <table id="tblSiswaMenunggak" class="table table-bordered table-striped table-sm align-middle mb-0">
        <thead>
            <tr class="text-center">
                <th class="col-nisn">NISN</th>
                <th class="col-nama">Nama</th>
                <th class="col-kelas">Kelas</th>
                <th class="col-nominal">Nominal / Bulan</th>
                <th class="col-total">Total Tunggakan</th>
                <th class="col-bulan">Bulan Tunggakan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $r)
                <tr>
                    <td class="text-center col-nisn">{{ $r->siswa->nisn ?: '-' }}</td>
                    <td class="col-nama">{{ $r->siswa->nama }}</td>
                    <td class="text-center col-kelas">{{ $r->kode_kelas }}</td>
                    <td class="text-end col-nominal">{{ \App\Utils\Angka::format($r->nominal_per_bulan, 2) }}</td>
                    <td class="text-end fw-semibold text-danger col-total">
                        {{ \App\Utils\Angka::format($r->total_tunggakan, 2) }}
                    </td>
                    <td class="col-bulan">
                        <div class="bulan-wrap">
                            @foreach ($r->bulan_tunggakan->take(3) as $d)
                                <span class="badge bg-warning rounded-pill">
                                    {{ $d->translatedFormat('M Y') }}
                                </span>
                            @endforeach
                            @if ($r->bulan_tunggakan->count() > 3)
                                <span class="badge bg-secondary rounded-pill">+{{ $r->bulan_tunggakan->count() - 3 }}</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada siswa menunggak SPP.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>