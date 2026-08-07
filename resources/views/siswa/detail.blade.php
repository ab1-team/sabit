@php
    use App\Utils\Tanggal;
@endphp
@extends('layouts.tenant.base')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4 shadow-sm">
                <div class="card-header p-1  position-relative mt-n4 mx-3 bg-white rounded-3">
                    <ul class="nav nav-pills nav-fill p-1 bg-secondary">
                        <li class="nav-item">
                            <a class="nav-link active d-flex align-items-center justify-content-center gap-1 py-2"
                                data-bs-toggle="tab" href="#tabSiswa">
                                <span class="material-symbols-rounded fs-5">school</span>
                                <span>Data Siswa</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center justify-content-center gap-1 py-2"
                                data-bs-toggle="tab" href="#tabWali">
                                <span class="material-symbols-rounded fs-5">people</span>
                                <span>Data Orang tua / Wali</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center justify-content-center gap-1 py-2"
                                data-bs-toggle="tab" href="#tabRiwayatPembayaran">
                                <span class="material-symbols-rounded fs-5">receipt</span>
                                <span>Riwayat Pembayaran</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content">
                    <div class="tab-pane fade show active p-3" id="tabSiswa">
                        <div class="row">
                            <div class="col-lg-4 col-md-6">
                                <div class="card h-100">
                                    <div class="card-header pb-0">
                                        <h6>Profil</h6>
                                    </div>
                                    <div class="card-body p-3 text-center">
                                        <div class="mx-auto mb-2"
                                            style="width:120px; height:120px; border-radius:50%; overflow:hidden; background-color:#f1f1f1;">
                                            <img src="{{ \App\Models\Profil::tenantStorageUrl('siswa/' . ($siswa->foto ?: 'default.png')) }}"
                                                style="width:100%; height:100%; object-fit:cover;">
                                        </div>
                                        <div class="d-flex justify-content-end mb-2">
                                            <span
                                                class="badge 
                                                {{ $siswa->status_siswa == 'aktif' ? 'bg-success' : 'bg-danger' }}">
                                                {{ $siswa->status_siswa }}
                                            </span>
                                        </div>
                                        <h6 class="mt-1 mb-0 fw-bold text-dark">{{ $siswa->nama }}</h6>
                                        <p class="text-secondary text-sm mb-0">NISN : {{ $siswa->nisn }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-8 col-md-6">
                                <div class="card h-100">
                                    <div class="card-header pb-0">
                                        <h6>Detail Data Siswa</h6>
                                    </div>
                                    <div class="card-body p-3">
                                        <h6 class="text-dark mb-3">
                                            <span class="material-symbols-rounded text-success me-1">person</span>
                                            Data Pribadi
                                        </h6>
                                        <div class="row mb-4">
                                            <div class="col-md-6 border-end">
                                                <ul class="list-group">
                                                    <li
                                                        class="list-group-item border-0 p-1 text-sm d-flex justify-content-between">
                                                        <strong>Tempat Lahir:</strong>
                                                        <span>{{ $siswa->tempat_lahir ?: '-' }}</span>
                                                    </li>
                                                    <li
                                                        class="list-group-item border-0 p-1 text-sm d-flex justify-content-between">
                                                        <strong>Tanggal Lahir:</strong>
                                                        <span>
                                                            {{ $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->format('d F Y') : '-' }}
                                                        </span>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <ul class="list-group">
                                                    <li
                                                        class="list-group-item border-0 p-1 text-sm d-flex justify-content-between">
                                                        <strong>Jenis Kelamin:</strong>
                                                        <span>{{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : ($siswa->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</span>
                                                    </li>
                                                    <li
                                                        class="list-group-item border-0 p-1 text-sm d-flex justify-content-between">
                                                        <strong>Agama:</strong>
                                                        <span>{{ $siswa->agama ?: '-' }}</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <h6 class="text-dark mb-3">
                                            <span class="material-symbols-rounded text-primary me-1">badge</span>
                                            Identitas Dasar
                                        </h6>
                                        <div class="row mb-4">
                                            <div class="col-md-6 border-end">
                                                <ul class="list-group">
                                                    <li
                                                        class="list-group-item border-0 p-1 text-sm d-flex justify-content-between">
                                                        <strong>Password:</strong>
                                                        <span>••••••••</span>
                                                    </li>
                                                    <li
                                                        class="list-group-item border-0 p-1 text-sm d-flex justify-content-between">
                                                        <strong>Kelas:</strong>
                                                        <span>{{ $siswa->kode_kelas ?: '-' }}</span>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <ul class="list-group">
                                                    <li
                                                        class="list-group-item border-0 p-1 text-sm d-flex justify-content-between">
                                                        <strong>Angkatan:</strong>
                                                        <span>{{ $siswa->tahun_akademik ?: '-' }}</span>
                                                    </li>
                                                    <li
                                                        class="list-group-item border-0 p-1 text-sm d-flex justify-content-between">
                                                        <strong>Status Siswa:</strong>
                                                        <span>{{ ucfirst($siswa->status_siswa) }}</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-lg-12">
                                <div class="card h-100 p-3">
                                    <h6 class="text-dark mb-3">
                                        <span class="material-symbols-rounded text-primary me-1">location_on</span>
                                        Alamat Lengkap
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6 border-end">
                                            <ul class="list-group mb-4">
                                                <li
                                                    class="list-group-item border-0 p-1 text-sm d-flex justify-content-between">
                                                    <strong>Alamat:</strong>
                                                    <span>{{ $siswa->alamat }}</span>
                                                </li>
                                                <li
                                                    class="list-group-item border-0 p-1 text-sm d-flex justify-content-between">
                                                    <strong>RT/RW:</strong>
                                                    <span>{{ $siswa->rt }}/{{ $siswa->rw }}</span>
                                                </li>
                                                <li
                                                    class="list-group-item border-0 p-1 text-sm d-flex justify-content-between">
                                                    <strong>Dusun:</strong>
                                                    <span>{{ $siswa->dusun }}</span>
                                                </li>
                                            </ul>
                                        </div>

                                        <div class="col-md-6">
                                            <ul class="list-group mb-4">
                                                <li
                                                    class="list-group-item border-0 p-1 text-sm d-flex justify-content-between">
                                                    <strong>Kelurahan:</strong>
                                                    <span>{{ $siswa->kelurahan }}</span>
                                                </li>
                                                <li
                                                    class="list-group-item border-0 p-1 text-sm d-flex justify-content-between">
                                                    <strong>Kecamatan:</strong>
                                                    <span>{{ $siswa->kecamatan }}</span>
                                                </li>
                                                <li
                                                    class="list-group-item border-0 p-1 text-sm d-flex justify-content-between">
                                                    <strong>Kode Pos:</strong>
                                                    <span>{{ $siswa->kode_pos }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <h6 class="text-dark mb-3 mt-4">
                                        <span class="material-symbols-rounded text-warning me-1">info</span>
                                        Lain-lain
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6 border-end">
                                            <ul class="list-group mb-4">
                                                <li
                                                    class="list-group-item border-0 p-1 text-sm d-flex justify-content-between">
                                                    <strong>Status Awal:</strong>
                                                    <span>{{ ucfirst($siswa->status_awal ?: '-') }}</span>
                                                </li>
                                                <li
                                                    class="list-group-item border-0 p-1 text-sm d-flex justify-content-between">
                                                    <strong>NIK:</strong>
                                                    <span>{{ $siswa->nik ?: '-' }}</span>
                                                </li>
                                                <li
                                                    class="list-group-item border-0 p-1 text-sm d-flex justify-content-between">
                                                    <strong>Kebutuhan Khusus:</strong>
                                                    <span>{{ $siswa->kebutuhan_khusus ?: '-' }}</span>
                                                </li>
                                                <li
                                                    class="list-group-item border-0 p-1 text-sm d-flex justify-content-between">
                                                    <strong>Jenis Tinggal:</strong>
                                                    <span>{{ ucfirst(str_replace('_', ' ', $siswa->jenis_tinggal ?: '-')) }}</span>
                                                </li>
                                            </ul>
                                        </div>

                                        <div class="col-md-6">
                                            <ul class="list-group mb-4">
                                                <li
                                                    class="list-group-item border-0 p-1 text-sm d-flex justify-content-between">
                                                    <strong>Transportasi:</strong>
                                                    <span>{{ $siswa->alat_transportasi ?: '-' }}</span>
                                                </li>
                                                <li
                                                    class="list-group-item border-0 p-1 text-sm d-flex justify-content-between">
                                                    <strong>No HP:</strong>
                                                    <span>{{ $siswa->hp ?: '-' }}</span>
                                                </li>
                                                <li
                                                    class="list-group-item border-0 p-1 text-sm d-flex justify-content-between">
                                                    <strong>Email:</strong>
                                                    <span>{{ $siswa->email ?: '-' }}</span>
                                                </li>
                                                <li
                                                    class="list-group-item border-0 p-1 text-sm d-flex justify-content-between">
                                                    <strong>Penerima KPS:</strong>
                                                    <span>{{ $siswa->penerima_kps ?: '-' }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade p-3" id="tabWali">
                        <h6 class="text-dark mb-3">
                            <span class="material-symbols-rounded text-info me-1">family_restroom</span>
                            Data Orang Tua / Wali
                        </h6>
                        <div class="row g-4">
                            <div class="col-lg-4">
                                <div class="card shadow-sm p-3" style="background:#f7faff;">
                                    <h6 class="mb-4 text-primary">
                                        <i class="material-symbols-rounded me-1 text-primary">man</i> Ayah
                                    </h6>
                                    <div class="row mb-2">
                                        <div class="col-5 fw-bold">Nama</div>
                                        <div class="col-7">: {{ $siswa->nama_ayah ?: '-' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-5 fw-bold">Thn Lahir</div>
                                        <div class="col-7">: {{ $siswa->tahun_lahir_ayah ?: '-' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-5 fw-bold">Pendidikan</div>
                                        <div class="col-7">: {{ $siswa->pendidikan_ayah ?: '-' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-5 fw-bold">Pekerjaan</div>
                                        <div class="col-7">: {{ $siswa->pekerjaan_ayah ?: '-' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-5 fw-bold">Penghasilan</div>
                                        <div class="col-7">: {{ $siswa->penghasilan_ayah ?: '-' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-5 fw-bold">No Telp</div>
                                        <div class="col-7">: {{ $siswa->no_telepon_ayah ?: '-' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="card shadow-sm p-3" style="background:#f8fff7;">
                                    <h6 class="mb-4 text-success">
                                        <i class="material-symbols-rounded me-1 text-success">woman</i> Ibu
                                    </h6>
                                    <div class="row mb-2">
                                        <div class="col-5 fw-bold">Nama</div>
                                        <div class="col-7">: {{ $siswa->nama_ibu ?: '-' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-5 fw-bold">Thn Lahir</div>
                                        <div class="col-7">: {{ $siswa->tahun_lahir_ibu ?: '-' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-5 fw-bold">Pendidikan</div>
                                        <div class="col-7">: {{ $siswa->pendidikan_ibu ?: '-' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-5 fw-bold">Pekerjaan</div>
                                        <div class="col-7">: {{ $siswa->pekerjaan_ibu ?: '-' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-5 fw-bold">Penghasilan</div>
                                        <div class="col-7">: {{ $siswa->penghasilan_ibu ?: '-' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-5 fw-bold">No Telp</div>
                                        <div class="col-7">: {{ $siswa->no_telepon_ibu ?: '-' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="card shadow-sm p-3" style="background:#fffaf5;">
                                    <h6 class="mb-4 text-warning">
                                        <i class="material-symbols-rounded me-1 text-warning">groups</i> Wali
                                    </h6>
                                    <div class="row mb-2">
                                        <div class="col-5 fw-bold">Nama</div>
                                        <div class="col-7">: {{ $siswa->nama_wali ?: '-' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-5 fw-bold">Thn Lahir</div>
                                        <div class="col-7">: {{ $siswa->tahun_lahir_wali ?: '-' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-5 fw-bold">Pendidikan</div>
                                        <div class="col-7">: {{ $siswa->pendidikan_wali ?: '-' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-5 fw-bold">Pekerjaan</div>
                                        <div class="col-7">: {{ $siswa->pekerjaan_wali ?: '-' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-5 fw-bold">Penghasilan</div>
                                        <div class="col-7">: {{ $siswa->penghasilan_wali ?: '-' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-5 fw-bold">No Telp</div>
                                        <div class="col-7">: {{ $siswa->no_telepon_wali ?: '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade p-3" id="tabRiwayatPembayaran">
                        <h6 class="text-dark mb-3 d-flex align-items-center justify-content-between">
                            <span>
                                <span class="material-symbols-rounded text-info me-1">history</span>
                                Riwayat Pembayaran
                            </span>
                            <button class="btn btn-sm btn-primary" data-id="{{ $siswa->id }}" id="btnCetakRiwayat">
                                Cetak Riwayat
                            </button>
                        </h6>
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <div class="card shadow-sm p-3" style="background:#f7faff;">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm" id="TBriwayat">
                                            <thead>
                                                <tr>
                                                    <th width="15%">Tanggal Transaksi</th>
                                                    <th width="15%">Alokasi</th>
                                                    <th width="60%">Keterangan</th>
                                                    <th width="10%" class="text-end">Nominal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($riwayat as $item)
                                                    <tr>
                                                        <td width="15%">{{ $item->tanggal_transaksi }}</td>
                                                        <td width="15%">
                                                           {{ $item->spp ? Tanggal::namabulan($item->spp->tanggal) : 'Daftar Ulang' }}
                                                        </td>
                                                        <td width="60%" class="text-wrap">{{ $item->keterangan }}</td>
                                                        <td width="10%" class="text-end">{{ \App\Utils\Angka::format($item->jumlah, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
        <div class="col-12">
            <div class="card shadow-sm m-0">
                <div class="card-body d-flex flex-column flex-md-row gap-2 align-items-stretch align-items-md-center justify-content-between p-2">
                    <div class="w-100 w-md-auto">
                        <a href="{{ route('siswa.index', request()->query()) }}"
                        class="btn btn-secondary w-100 w-md-auto m-0">
                            Kembali ke halaman siswa
                        </a>
                    </div>
                    <div class="d-flex flex-column flex-md-row gap-2 w-100 w-md-auto">
                        <a href="{{ route('siswa.edit', $siswa->id) }}?{{ http_build_query(request()->query()) }}"
                        class="btn btn-warning w-100 w-md-auto m-0">
                            <span class="fa-solid fa-pen-to-square"></span> Edit Siswa
                        </a>
                        <button class="btn btn-danger w-100 w-md-auto m-0"
                                data-id="{{ $siswa->id }}" id="btnDelete">
                            <span class="fa-solid fa-ban"></span> Blokir Siswa
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <form action="" method="post" id="FormHapusSiswa">
        @method('DELETE')
        @csrf
    </form>
@endsection
@section('script')
    <script>   
        let tbRiwayat;

        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            if ($(e.target).attr('href') === '#tabRiwayatPembayaran') {

                if (!$.fn.DataTable.isDataTable('#TBriwayat')) {
                    tbRiwayat = $('#TBriwayat').DataTable({
                        ordering: false,
                        pageLength: 10,
                        lengthChange: true,
                        searching: true,
                        info: true,
                        autoWidth: false,
                        deferRender: true,
                        columns: [
                            { width: '15%' },
                            { width: '15%' },
                            { width: '60%' },
                            { width: '10%', className: 'text-end' }
                        ]
                    });
                } else {
                    tbRiwayat.columns.adjust().draw();
                }
            }
        });

        $(document).on('click', '#btnDelete', function(e) {
            e.preventDefault();
            let hapus_id = $(this).attr('data-id');
            let actionUrl = '/app/siswa/' + hapus_id;
            let urlParams = new URLSearchParams(window.location.search);
            let qs_tahun = urlParams.get('tahun_akademik');
            let qs_kelas = urlParams.get('kelas');

            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Data akan diblokir secara permanen dari aplikasi!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Lanjutkan",
                cancelButtonText: "Batal",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = $('#FormHapusSiswa');
                    $.ajax({
                        type: form.attr('method'),
                        url: actionUrl,
                        data: form.serialize(),
                        success: function(result) {
                            if (result.success) {
                                Swal.fire({
                                    title: "Berhasil!",
                                    text: result.msg,
                                    icon: "success",
                                    confirmButtonText: "Oke"
                                }).then((res) => {
                                    if (res.isConfirmed) {
                                        window.location.href =
                                            '/app/siswa?tahun_akademik=' + qs_tahun +
                                            '&kelas=' + qs_kelas;
                                    }
                                });
                            } else {
                                Swal.fire({
                                    title: "Gagal",
                                    text: result.msg,
                                    icon: "info",
                                    confirmButtonText: "Oke"
                                });
                            }
                        },
                        error: function(response) {
                            let msg = "Terjadi kesalahan pada server. Silakan coba lagi.";
                            if (response.responseJSON && response.responseJSON.msg) {
                                msg = response.responseJSON.msg;
                            }
                            Swal.fire({
                                title: "Gagal",
                                text: msg,
                                icon: "error",
                                confirmButtonText: "Oke"
                            });
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Dibatalkan",
                        text: "Data tidak jadi diblokir.",
                        icon: "info",
                        confirmButtonText: "Oke"
                    });
                }
            });
        });

        $(document).on('click', '#btnCetakRiwayat', function(e) {
            e.preventDefault();
            let id_siswa = $(this).attr('data-id');
            let url = '/app/siswa/riwayatPembayaran/' + id_siswa;
            window.open(url, '_blank');
        })
        </script>
@endsection