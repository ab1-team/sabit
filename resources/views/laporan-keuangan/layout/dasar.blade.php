@php $excelMode = (request('action') === 'excel'); @endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dokumen')</title>
    @if ($excelMode)
        <style>
            * { font-family: Arial, sans-serif; font-size: 11px; }
            table { border-collapse: collapse; }
            .border-table th, .border-table td { border: 1px solid #000; padding: 4px 6px; }
            .border-table th { background: #d9d9d9; font-weight: bold; text-align: center; }
            img { display: none; }
        </style>
    @endif
    <style>
        * {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.5;
        }

        .judul {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
        }

        .subjudul {
            text-align: center;
            margin-bottom: 30px;
        }

        .rata-kanan {
            text-align: right;
        }

        .rata-kiri {
            text-align: left;
        }

        .rata-tengah {
            text-align: center;
        }

        .ratakanankiri {
            text-align: justify;
            text-justify: inter-word;
        }

        .paragraf {
            text-align: justify;
            text-justify: inter-word;
            text-indent: 2em;
        }

        ol.nested-numbering {
            counter-reset: section;
            list-style: none;
            padding-left: 5px;
        }

        ol.nested-numbering>li {
            counter-increment: section;
            position: relative;
        }

        ol.nested-numbering>li::before {
            content: "";
        }

        ol.nested-numbering ol {
            counter-reset: subsection;
            list-style: none;
            padding-left: 20px;
        }

        ol.nested-numbering>li>ol>li {
            counter-increment: subsection;
            position: relative;
        }

        ol.nested-numbering>li>ol>li::before {
            content: counter(section) "." counter(subsection) " ";
            position: absolute;
            left: -20px;
        }

        ol.nested-numbering>li>ol>li>ol {
            counter-reset: subsub;
            list-style: none;
            padding-left: 20px;
        }

        ol.nested-numbering>li>ol>li>ol>li {
            counter-increment: subsub;
            position: relative;
        }

        ol.nested-numbering>li>ol>li>ol>li::before {
            content: counter(subsub) ". ";
            position: absolute;
            left: -20px;
        }

        .break {
            page-break-after: always;
        }

        ol.romawi,
        ol.angka,
        ol.huruf-kecil,
        ol.huruf-besar {
            margin: 0;
            padding-left: 20px;
        }

        ol.romawi {
            list-style-type: upper-roman;
            padding-top: 5px;
        }

        ol.angka {
            list-style-type: decimal;
            padding-top: 5px;
        }

        ol.huruf-kecil {
            list-style-type: lower-alpha;
            padding-top: 5px;
        }

        ol.huruf-besar {
            list-style-type: upper-alpha;
            padding-top: 5px;
        }

        .border-table {
            border-collapse: collapse;
            width: 100%;
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .border-table th,
        .border-table td {
            border: 1px solid black;
            padding: 2px 5px;
            text-align: left;
        }

        .border-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        .page-break {
            page-break-before: always;
            break-before: page;
        }
        .ttd-wrapper table,
        .ttd-wrapper tr,
        .ttd-wrapper td {
            border: none !important;
            outline: none !important;
        }
        .ttd-wrapper table {
            border-collapse: collapse !important;
        }
        .ttd-wrapper table {
            width: 100% !important;
            max-width: 100% !important;
            height: auto !important;
            table-layout: fixed;
        }

        @media print {
            .ttd-wrapper,
            .ttd-wrapper table,
            .ttd-wrapper td {
                border: none !important;
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>

    @include('laporan-keuangan.layout.kop')

    @yield('content')

</body>


</html>
