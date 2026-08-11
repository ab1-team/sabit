@extends('layouts.tenant.base')
@section('content')
    <style>
        .lk-page { max-width: 1100px; margin: 0 auto; }
        .lk-hero {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #db2777 100%);
            color: #fff;
            border-radius: 14px;
            padding: 22px 26px;
            box-shadow: 0 10px 30px rgba(79, 70, 229, .25);
            margin-bottom: 18px;
        }
        .lk-hero h4 { margin: 0; font-weight: 700; letter-spacing: .3px; }
        .lk-hero p { margin: 4px 0 0; opacity: .9; font-size: 13px; }

        .lk-card {
            border: 0;
            border-radius: 14px;
            box-shadow: 0 6px 18px rgba(20, 20, 43, .06);
            overflow: hidden;
        }
        .lk-card .form-label { font-weight: 600; color: #475569; font-size: 13px; }
        .lk-card .form-select, .lk-card .form-control { border-radius: 10px; }
        .lk-actions {
            background: #f8fafc;
            border-top: 1px solid #eef2f7;
            padding: 14px 18px;
            margin: 18px -18px -14px;
        }
        .lk-actions .btn {
            border-radius: 10px;
            padding: 8px 18px;
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(149, 31, 31, 0.06);
        }

        .lk-info-card {
            border: 0;
            border-radius: 14px;
            box-shadow: 0 6px 18px rgba(20, 20, 43, .06);
            transition: transform .15s ease, box-shadow .15s ease;
        }
        .lk-info-card.bg-danger-soft { background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); }
        .lk-info-card.bg-success-soft { background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); }
        .lk-info-card.bg-info-soft { background: linear-gradient(135deg, #ecfeff 0%, #cffafe 100%); }
        .lk-info-card:hover { transform: translateY(-2px); box-shadow: 0 12px 24px rgba(20, 20, 43, .1); }
        .lk-info-card .lk-icon {
            width: 44px; height: 44px; min-width: 44px; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            color: #fff; font-size: 18px; margin-right: 12px;
        }
        .lk-info-card h6 { margin: 0; font-weight: 700; font-size: 14px; }
        .lk-info-card p { margin: 4px 0 0; color: #64748b; font-size: 12px; line-height: 1.4; }
        .lk-info-card .card-body { padding: 12px 14px; }
        .lk-info-card .lk-icon { width: 38px; height: 38px; min-width: 38px; font-size: 16px; margin-right: 10px; }
        .row.g-3 > [class*="col-"] > .lk-info-card { height: 100%; }
        .bg-grad-danger { background: linear-gradient(135deg, #ef4444, #b91c1c); }
        .bg-grad-success { background: linear-gradient(135deg, #10b981, #047857); }
        .bg-grad-info { background: linear-gradient(135deg, #06b6d4, #0e7490); }
    </style>

    <div class="lk-page">
        <div class="card lk-card mb-3">
            <div class="card-body p-4">
                <form id="formPelaporan" action="/app/pelaporan/preview" method="GET" target="_blank">
                    <div id="laporanRow" class="row g-3">
                        <div class="col-md-4" id="wrapTahun">
                            <label class="form-label">Pilih Tahun</label>
                            <select name="tahun" class="form-select select2">
                                @for ($i = 2020; $i <= date('Y'); $i++)
                                    <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-md-4" id="wrapBulan">
                            <label class="form-label">Pilih Bulan</label>
                            <select name="bulan" class="form-select select2">
                                @foreach ([
            '01' => 'JANUARI', '02' => 'FEBRUARI', '03' => 'MARET', '04' => 'APRIL',
            '05' => 'MEI', '06' => 'JUNI', '07' => 'JULI', '08' => 'AGUSTUS',
            '09' => 'SEPTEMBER', '10' => 'OKTOBER', '11' => 'NOVEMBER', '12' => 'DESEMBER',
        ] as $num => $name)
                                    <option value="{{ $num }}" {{ $num == date('m') ? 'selected' : '' }}>{{ $num }}. {{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4" id="wrapHari">
                            <label class="form-label">Pilih Hari</label>
                            <select name="hari" class="form-select select2">
                                <option value="">---</option>
                                @for ($i = 1; $i <= 31; $i++)
                                    <option value="{{ $i }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-md-6 d-none" id="wrapPeriode">
                            <label class="form-label">Periode Tanggal</label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="text" name="tgl_awal" class="form-control datepicker flex-fill"
                                    value="{{ \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}">
                                <span class="text-muted small">s.d</span>
                                <input type="text" name="tgl_akhir" class="form-control datepicker flex-fill"
                                    value="{{ \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}">
                            </div>
                        </div>

                        <div class="col-md-6 d-none" id="wrapTahunAkademik">
                            <label class="form-label">Tahun Akademik</label>
                            <select name="tahun_akademik_id" class="form-select select2">
                                <option value="">-- Semua --</option>
                                @foreach (\App\Models\Tahun_Akademik::orderBy('nama_tahun', 'desc')->get() as $ta)
                                    <option value="{{ $ta->id }}">{{ $ta->nama_tahun }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="colLaporan" class="col-md-6">
                            <label class="form-label">Pilih Nama Laporan</label>
                            <select id="laporan" name="laporan" class="form-select select2">
                                <option value="">---</option>
                                @foreach ($laporan as $item)
                                    <option value="{{ $item->file }}" {{ request('laporan') == $item->file ? 'selected' : '' }}>
                                        {{ $item->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="subLaporan" class="col-md-6">
                            <label class="form-label">Pilih Nama Sub Laporan</label>
                            <select name="sub_laporan" id="sub_laporan" class="form-select select2">
                                <option value="">---</option>
                            </select>
                        </div>
                    </div>

                    <div class="lk-actions d-flex justify-content-end gap-2 p-3 pt-2 pb-0">
                        <button type="button" id="btnSimpanSaldo" class="btn btn-danger">Simpan Saldo</button>
                        <button type="button" id="btnExcel" class="btn btn-success">Excel</button>
                        <button type="button" id="btnPreview" class="btn btn-info">Preview</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-md-4">
                <div class="card lk-info-card bg-danger-soft">
                    <div class="card-body d-flex align-items-start">
                        <span class="lk-icon bg-grad-danger"><i class="fa-solid fa-floppy-disk"></i></span>
                        <div>
                            <h6 class="text-danger">Simpan Saldo</h6>
                            <p>Menyimpan ringkasan saldo periode laporan ke database untuk arsip/riwayat.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card lk-info-card bg-success-soft">
                    <div class="card-body d-flex align-items-start">
                        <span class="lk-icon bg-grad-success"><i class="fa-solid fa-file-excel"></i></span>
                        <div>
                            <h6 class="text-success">Excel</h6>
                            <p>Mengunduh laporan dalam format .xlsx sesuai filter laporan & sub laporan.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card lk-info-card bg-info-soft">
                    <div class="card-body d-flex align-items-start">
                        <span class="lk-icon bg-grad-info"><i class="fa-solid fa-eye"></i></span>
                        <div>
                            <h6 class="text-info">Preview</h6>
                            <p>Menampilkan laporan di tab baru sebagai pratinjau sebelum dicetak/diunduh.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5'
            });

            if (typeof flatpickr !== 'undefined') {
                flatpickr('.datepicker', {
                    dateFormat: "Y-m-d"
                });
            }

            const initFile = $('#laporan').val();
            adjustLayout(initFile);

            if (initFile === 'calk' && $('#editor').length) {
                initQuill();
            }
        });

        const SPP_FILES = ['pembayaran_spp', 'daftar_ulang', 'pembangunan', 'ujian_semester', 'bantuan_yayasan'];

        function adjustLayout(file) {
            const laporanCol = $('#colLaporan');
            const subCol = $('#subLaporan');

            if (file === 'calk') {
                laporanCol.removeClass('col-md-6').addClass('col-12 mb-2');
                subCol.removeClass('col-md-6').addClass('col-12');
            } else {
                laporanCol.removeClass('col-12 mb-2').addClass('col-md-6');
                subCol.removeClass('col-12').addClass('col-md-6');
            }

            const isSpp = SPP_FILES.includes(file);
            $('#wrapTahun, #wrapBulan, #wrapHari').toggleClass('d-none', isSpp);
            $('#wrapPeriode, #wrapTahunAkademik').toggleClass('d-none', !isSpp);
        }

        function initQuill() {
            const editor = document.getElementById('editor');
            if (!editor || typeof Quill === 'undefined') return;

            if (editor.__quill) {
                editor.__quill = null;
            }

            const quill = new Quill('#editor', {
                theme: 'snow'
            });

            editor.__quill = quill;

            quill.on('text-change', function() {
                document.getElementById('sub_laporan').value = quill.root.innerHTML;
            });
        }

        $('#laporan').on('change', function() {
            const file = $(this).val();

            adjustLayout(file);

            if (!file) {
                $('#subLaporan').html(`
                <label class="form-label">Nama Sub Laporan</label>
                <select name="sub_laporan" id="sub_laporan" class="form-select select2">
                    <option value="">---</option>
                </select>
            `);

                $('#sub_laporan').select2({
                    theme: 'bootstrap-5'
                });
                return;
            }

            $.ajax({
                url: '/app/pelaporan/sub_laporan/' + file,
                type: 'GET',
                data: {
                    tahun: $('select[name="tahun"]').val(),
                    bulan: $('select[name="bulan"]').val()
                },
                success: function(res) {
                    $('#subLaporan').html(res);

                    if (file === 'calk') {
                        initQuill();
                    } else {
                        $('#sub_laporan').select2({
                            theme: 'bootstrap-5'
                        });
                    }
                }
            });
        });

        const NAMA_BULAN = {
            '01':'Januari','02':'Februari','03':'Maret','04':'April','05':'Mei','06':'Juni',
            '07':'Juli','08':'Agustus','09':'September','10':'Oktober','11':'November','12':'Desember'
        };

        $('#btnPreview').on('click', function() {
            $('#formPelaporan').attr('target', '_blank');
            $('#formPelaporan').find('input[name=action]').remove();
            $('#formPelaporan').submit();
        });

        $('#btnExcel').on('click', function(e) {
            e.preventDefault();
            const $form = $('#formPelaporan');
            const url = $form.attr('action') + '?' + $form.serialize() + '&action=excel';

            const loading = Swal.fire({
                title: 'Menyiapkan Excel...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch(url, { credentials: 'same-origin' })
                .then(r => {
                    const dispo = r.headers.get('Content-Disposition') || '';
                    const m = dispo.match(/filename="?([^"]+)"?/);
                    const filename = m ? m[1] : 'laporan.xls';
                    return r.blob().then(b => ({ blob: b, filename: filename }));
                })
                .then(({ blob, filename }) => {
                    const a = document.createElement('a');
                    a.href = URL.createObjectURL(blob);
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(a.href);
                })
                .catch(err => {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: err.message || String(err) });
                })
                .finally(() => Swal.close());
        });

        $('#btnSimpanSaldo').on('click', function() {
            const tahun = $('select[name="tahun"]').val();
            const bulan = $('select[name="bulan"]').val();
            if (!tahun || !bulan) return;

            const loading = Swal.fire({
                title: 'Mohon Menunggu..',
                html: 'Menyimpan saldo periode ' + (NAMA_BULAN[bulan] || '') + ' ' + tahun,
                timerProgressBar: true,
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            window.open(
                '/app/laporan-keuangan/simpan-saldo?tahun=' + tahun + '&bulan=' + bulan,
                '_blank'
            );
        });

        window.addEventListener('message', function(e) {
            if (e.data === 'closed') {
                Swal.close();
                window.location.reload();
            }
        });
    </script>
@endsection
