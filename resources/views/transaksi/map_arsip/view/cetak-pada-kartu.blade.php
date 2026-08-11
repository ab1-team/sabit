@php
    use App\Utils\Tanggal;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Kartu</title>

    <style>
        @media print {
            body {
                margin: 0;
            }
        }

        body {
            font-family: "Courier New", monospace;
            font-size: 12px;
        }

        /* area kartu */
        .kartu {
            width: 100%;
            margin-top: 140px; /* atur sesuai posisi kartu fisik */
        }

        /* satu baris data */
        .row-kartu {
            display: grid;
            grid-template-columns: 
                40px     /* No */
                120px    /* Tanggal */
                200px    /* alokasi */
                200px    /* Keterangan */
                120px    /* Nominal */
                80px;    /* ID */
            align-items: center;
        }
        .right {
            text-align: right;
        }
    </style>
</head>
<body onload="window.print()">

<div class="kartu">
    @foreach ($transaksis as $i => $trx)
        <div class="row-kartu">
            <div>{{ $i + 1 }}</div>
            <div>{{ Tanggal::tglIndo($trx->tanggal) }}</div>
            <div>
                @if ( $trx->spp)
                    {{ Tanggal::namabulan($trx->spp->tanggal) }}
                @else
                    Daftar Ulang
                @endif
            </div>
            <div>{{ strtoupper($trx->keterangan) }}</div>
            <div class="right">{{ \App\Utils\Angka::format($trx->getRawOriginal('jumlah'), 0) }}</div>
            <div class="right">{{ $trx->id }}</div>
        </div>
    @endforeach
</div>

</body>
</html>
