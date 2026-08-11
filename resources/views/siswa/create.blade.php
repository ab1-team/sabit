@extends('layouts.tenant.base')
@section('content')
    <div class="row">
        <form id="FormSiswa" method="POST" action="{{ route('siswa.store') }}" class="text-start" enctype="multipart/form-data">
            @csrf
            <div class="col-12">
                <div class="card my-4 shadow-sm">
                    <div class="card-header p-1 position-relative mt-n4 mx-3 bg-white rounded-3">
                        <ul class="nav nav-pills nav-fill p-1 bg-secondary">
                            <li class="nav-item">
                                <a class="nav-link active d-flex align-items-center justify-content-center gap-1 py-2"
                                    data-bs-toggle="tab" href="#tabSiswa">
                                    <i class="material-symbols-rounded fs-5">school</i>
                                    <span>Data Siswa</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center justify-content-center gap-1 py-2"
                                    data-bs-toggle="tab" href="#tabWali">
                                    <i class="material-symbols-rounded fs-5">people</i>
                                    <span>Data Orang tua / Wali</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content">
                        {{-- ==================== TAB DATA SISWA ==================== --}}
                        <div class="tab-pane fade show active p-3" id="tabSiswa">
                            <div class="row">
                                <div class="col-md-2">
                                    <div id="preview-img-box" style="width:150px;height:150px"
                                        class="border bg-light overflow-hidden rounded">
                                        <img id="preview-img" class="w-100 h-100" style="object-fit:cover;">
                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <label for="foto"
                                            class="form-label btn btn-outline-primary text-truncate w-100">
                                            Pilih Foto Siswa
                                        </label>
                                        <input type="file" name="foto" id="foto" class="d-none" accept="image/*">
                                    </div>
                                </div>
                                <div class="col-md-10">
                                    {{-- Baris 1: NIPD, NISN, Nama (3+3+6) --}}
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="input-group input-group-outline mb-3">
                                                <label class="form-label">NIPD</label>
                                                <input type="text" name="nipd" id="nipd" class="form-control"
                                                    placeholder="-">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="input-group input-group-outline mb-3">
                                                <label class="form-label">NISN</label>
                                                <input type="text" name="nisn" id="nisn" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline mb-3">
                                                <label class="form-label">Nama Lengkap</label>
                                                <input type="text" name="nama" id="nama" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Baris 2: Tempat, Tgl Lahir, JK, NIK (3+3+3+3) --}}
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="input-group input-group-outline mb-3">
                                                <label class="form-label">Tempat Lahir</label>
                                                <input type="text" name="tempat_lahir" id="tempat_lahir"
                                                    class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="input-group input-group-outline mb-3">
                                                <label class="form-label">Tanggal Lahir</label>
                                                <input type="text" name="tanggal_lahir" id="tanggal_lahir"
                                                    class="form-control datepicker" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="input-group input-group-outline mb-3">
                                                <select name="jenis_kelamin" id="jenis_kelamin"
                                                    class="form-select select2" required>
                                                    <option value="" disabled selected>Pilih Jenis Kelamin</option>
                                                    <option value="L">Laki-laki</option>
                                                    <option value="P">Perempuan</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="input-group input-group-outline mb-3">
                                                <label class="form-label">NIK</label>
                                                <input type="text" name="nik" id="nik" class="form-control"
                                                    maxlength="16" placeholder="-">
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Baris 3: No KK, Agama, Kode POS, Status Awal (3+3+3+3) --}}
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="input-group input-group-outline mb-3">
                                                <label class="form-label">No KK</label>
                                                <input type="text" name="no_kk" id="no_kk" class="form-control"
                                                    maxlength="16" placeholder="-">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="input-group input-group-outline mb-3">
                                                <select name="agama" id="agama" class="form-select select2" required>
                                                    <option value="" disabled selected>Pilih Agama</option>
                                                    <option value="Islam">Islam</option>
                                                    <option value="Kristen Protestan">Kristen Protestan</option>
                                                    <option value="Katolik">Katolik</option>
                                                    <option value="Hindu">Hindu</option>
                                                    <option value="Budha">Budha</option>
                                                    <option value="Konghucu">Konghucu</option>
                                                    <option value="Kepercayaan">Kepercayaan kepada Tuhan YME</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="input-group input-group-outline mb-3">
                                                <label class="form-label">Kode POS</label>
                                                <input type="text" name="kode_pos" id="kode_pos" class="form-control"
                                                    placeholder="-">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="input-group input-group-outline mb-3">
                                                <select name="status_awal" id="status_awal" class="form-select select2" required>
                                                    <option value="" disabled selected>Status Awal</option>
                                                    <option value="baru">Baru</option>
                                                    <option value="pindahan">Pindahan</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Baris 4: Kecamatan, Kelurahan, Dusun, RT/RW (3+3+3+3) --}}
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="input-group input-group-outline mb-3">
                                                <label class="form-label">Kecamatan</label>
                                                <input type="text" name="kecamatan" id="kecamatan" class="form-control"
                                                    placeholder="-">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="input-group input-group-outline mb-3">
                                                <label class="form-label">Kelurahan</label>
                                                <input type="text" name="kelurahan" id="kelurahan" class="form-control"
                                                    placeholder="-">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="input-group input-group-outline mb-3">
                                                <label class="form-label">Dusun</label>
                                                <input type="text" name="dusun" id="dusun" class="form-control"
                                                    placeholder="-">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="row">
                                                <div class="col-6 pe-1">
                                                    <div class="input-group input-group-outline mb-3">
                                                        <label class="form-label">RT</label>
                                                        <input type="text" name="rt" id="rt"
                                                            class="form-control" maxlength="3" placeholder="-">
                                                    </div>
                                                </div>
                                                <div class="col-6 ps-1">
                                                    <div class="input-group input-group-outline mb-3">
                                                        <label class="form-label">RW</label>
                                                        <input type="text" name="rw" id="rw"
                                                            class="form-control" maxlength="3" placeholder="-">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                    {{-- Baris 5: Alamat (12) --}}
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="input-group input-group-outline mb-3">
                                                <label class="form-label">Alamat Lengkap</label>
                                                <textarea name="alamat" id="alamat" rows="1" class="form-control" placeholder="-"></textarea>
                                            </div>
                                        </div>
                                    </div>
                            {{-- Baris 6: Keb. Khusus, Jenis Tinggal, Transportasi, Status Siswa (3+3+3+3) --}}
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">Keb. Khusus</label>
                                        <input type="text" name="kebutuhan_khusus" id="kebutuhan_khusus"
                                            class="form-control" placeholder="-">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-outline mb-3">
                                        <select name="jenis_tinggal" id="jenis_tinggal" class="form-select select2">
                                            <option value="" disabled selected>Jenis Tinggal</option>
                                            <option value="orang_tua">Orang Tua</option>
                                            <option value="asrama">Asrama</option>
                                            <option value="kost">Kost</option>
                                            <option value="wali">Wali</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">Transportasi</label>
                                        <input type="text" name="transportasi" id="transportasi" class="form-control"
                                            placeholder="-">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="btn-group w-100" role="group" aria-label="Status Siswa">
                                        <input type="radio" class="btn-check" name="status_siswa"
                                            id="status_aktif" value="aktif" autocomplete="off" checked>
                                        <label class="btn btn-outline-primary flex-fill"
                                            for="status_aktif">Aktif</label>
                                        <input type="radio" class="btn-check" name="status_siswa"
                                            id="status_nonaktif" value="nonaktif" autocomplete="off">
                                        <label class="btn btn-outline-primary flex-fill"
                                            for="status_nonaktif">Nonaktif</label>
                                    </div>
                                </div>
                            </div>
                            {{-- Baris 7: HP, Email, Tgl Masuk, SKHUN (3+3+3+3) --}}
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">No Handphone</label>
                                        <input type="text" name="hp" id="hp" class="form-control" placeholder="-">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" id="email" class="form-control"
                                            placeholder="-">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">Tanggal Masuk</label>
                                        <input type="text" name="tanggal_masuk" id="tanggal_masuk"
                                            class="form-control datepicker" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">SKHUN</label>
                                        <input type="text" name="skhun" id="skhun" class="form-control"
                                            placeholder="-">
                                    </div>
                                </div>
                            </div>
                            {{-- Baris 8: Password(6), Penerima KPS(3), No KPS(3) = 12 --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">Password</label>
                                        <input type="password" name="password" id="password" class="form-control"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">Penerima KPS</label>
                                        <input type="text" name="penerima_kps" id="penerima_kps" class="form-control"
                                            placeholder="-">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">No KPS</label>
                                        <input type="text" name="no_kps" id="no_kps" class="form-control"
                                            placeholder="-">
                                    </div>
                                </div>
                            </div>
                            {{-- Baris 9: Tahun Ajaran(3), Kelas(3), Ruangan(3), SppNominal(3) = 12 --}}
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="input-group input-group-outline mb-3">
                                        <select name="tahun_akademik" id="tahun_akademik"
                                            class="form-select select2" required>
                                            <option value="" disabled selected>Tahun Ajaran</option>
                                            @foreach ($tahunAkademmik as $tA)
                                                <option value="{{ $tA->nama_tahun }}"
                                                    {{ \Illuminate\Support\Str::startsWith($tA->nama_tahun, date('Y')) ? 'selected' : '' }}>
                                                    {{ $tA->nama_tahun }} - {{ ucfirst($tA->keterangan) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-outline mb-3">
                                        <select name="kelas" id="kelas" class="form-select select2" required>
                                            <option value="" disabled selected>Pilih Kelas</option>
                                            @foreach ($kelas as $kls)
                                                <option value="{{ $kls->kode_kelas }}|{{ $kls->tingkat }}">
                                                    {{ $kls->kode_kelas }} - {{ $kls->nama_kelas }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-outline mb-3">
                                        <select name="ruangan" id="ruangan" class="form-select select2" required>
                                            <option value="" disabled selected>Pilih Ruangan</option>
                                            @foreach ($ruang as $R)
                                                <option value="{{ $R->kode_ruangan }}">
                                                    {{ $R->kode_ruangan }} - {{ $R->nama_ruangan }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-outline mb-3 is-filled">
                                        <label class="form-label">Nominal SPP / Bulan (ribu)</label>
                                        <input type="text" name="spp_nominal" id="spp_nominal"
                                            class="form-control nominal text-end"
                                            value="{{ \App\Utils\Angka::format(($nominalSpp ?? 0) * 100, 0) }}"
                                            placeholder="0">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ==================== TAB WALI ==================== --}}
                        <div class="tab-pane fade p-3" id="tabWali">
                            <h6 class="text-dark mb-3">Data Ayah</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">Nama Ayah</label>
                                        <input type="text" name="nama_ayah" id="nama_ayah" class="form-control"
                                            placeholder="-">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">Tahun Lahir</label>
                                        <input type="text" name="tahun_lahir_ayah" id="tahun_lahir_ayah"
                                            class="form-control" maxlength="4" placeholder="-">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">Pendidikan</label>
                                        <input type="text" name="pendidikan_ayah" id="pendidikan_ayah"
                                            class="form-control" placeholder="-">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">Pekerjaan</label>
                                        <input type="text" name="pekerjaan_ayah" id="pekerjaan_ayah" class="form-control"
                                            placeholder="-">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">Penghasilan</label>
                                        <input type="text" name="penghasilan_ayah" id="penghasilan_ayah"
                                            class="form-control" placeholder="-">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">Kebutuhan Khusus</label>
                                        <input type="text" name="kebutuhan_khusus_ayah" id="kebutuhan_khusus_ayah"
                                            class="form-control" placeholder="-">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">No Telepon</label>
                                        <input type="text" name="no_telp_ayah" id="no_telp_ayah" class="form-control"
                                            placeholder="-">
                                    </div>
                                </div>
                            </div>

                            <h6 class="text-dark mt-4 mb-3">Data Ibu</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">Nama Ibu</label>
                                        <input type="text" name="nama_ibu" id="nama_ibu" class="form-control"
                                            placeholder="-">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">Tahun Lahir</label>
                                        <input type="text" name="tahun_lahir_ibu" id="tahun_lahir_ibu"
                                            class="form-control" maxlength="4" placeholder="-">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">Pendidikan</label>
                                        <input type="text" name="pendidikan_ibu" id="pendidikan_ibu" class="form-control"
                                            placeholder="-">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">Pekerjaan</label>
                                        <input type="text" name="pekerjaan_ibu" id="pekerjaan_ibu" class="form-control"
                                            placeholder="-">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">Penghasilan</label>
                                        <input type="text" name="penghasilan_ibu" id="penghasilan_ibu"
                                            class="form-control" placeholder="-">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">Kebutuhan Khusus</label>
                                        <input type="text" name="kebutuhan_khusus_ibu" id="kebutuhan_khusus_ibu"
                                            class="form-control" placeholder="-">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">No Telepon</label>
                                        <input type="text" name="no_telp_ibu" id="no_telp_ibu" class="form-control"
                                            placeholder="-">
                                    </div>
                                </div>
                            </div>

                            <h6 class="text-dark mt-4 mb-3">Data Wali</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">Nama Wali</label>
                                        <input type="text" name="nama_wali" id="nama_wali" class="form-control"
                                            placeholder="-">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">Tahun Lahir</label>
                                        <input type="text" name="tahun_lahir_wali" id="tahun_lahir_wali"
                                            class="form-control" maxlength="4" placeholder="-">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">Pendidikan</label>
                                        <input type="text" name="pendidikan_wali" id="pendidikan_wali"
                                            class="form-control" placeholder="-">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">Pekerjaan</label>
                                        <input type="text" name="pekerjaan_wali" id="pekerjaan_wali"
                                            class="form-control" placeholder="-">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">Penghasilan</label>
                                        <input type="text" name="penghasilan_wali" id="penghasilan_wali"
                                            class="form-control" placeholder="-">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">Kebutuhan Khusus</label>
                                        <input type="text" name="kebutuhan_khusus_wali" id="kebutuhan_khusus_wali"
                                            class="form-control" placeholder="-">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">No Telepon</label>
                                        <input type="text" name="no_telp_wali" id="no_telp_wali" class="form-control"
                                            placeholder="-">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 mb-1">
                <div class="card my-4 shadow-sm mb-1">
                    <div class="card-body
                                d-flex
                                flex-column flex-md-row
                                align-items-start align-items-md-center
                                justify-content-between
                                gap-2
                                p-2 pb-1">
                        <span class="fw-bold" style="font-size: 14px;">
                            Isi semua kolom bertanda <span class="text-danger">*</span>.
                            Kolom tanpa tanda boleh dikosongkan atau isi dengan tanda strip (-).
                        </span>
                        <button type="submit" class="btn btn-info w-100 w-md-auto mb-1" id="simpan">
                            Simpan data Siswa
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('script')
    <script>
        var fotoInput = document.getElementById('foto');
        var labelFoto = document.querySelector('label[for="foto"]');
        var previewImg = document.getElementById('preview-img');

        fotoInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                var file = this.files[0];
                var valid = ['image/jpeg', 'image/jpg', 'image/png'];

                if (!valid.includes(file.type)) {
                    this.value = '';
                    labelFoto.textContent = 'Pilih Foto Siswa';
                    previewImg.src = "";

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Hanya boleh upload JPG, JPEG, PNG!',
                        showConfirmButton: false,
                        timer: 3000
                    });
                } else {
                    labelFoto.textContent = file.name;
                    previewImg.src = URL.createObjectURL(file);
                }
            }
        });

        $(document).ready(function() {
            $('.select2').select2({ theme: 'bootstrap-5' });

            // Tandai field yang sudah terisi (untuk style label naik)
            $('#FormSiswa input, #FormSiswa textarea, #FormSiswa select').each(function() {
                if ($(this).val() && $(this).val() !== '' && $(this).attr('type') !== 'password') {
                    $(this).closest('.input-group').addClass('is-filled');
                }
            });
        });

        flatpickr('.datepicker', { dateFormat: "Y-m-d" });

        $('.datepicker').on('change', function() {
            $(this).parent().addClass('is-filled');
        });

        // Mask nominal SPP
        $(".nominal").maskMoney({
            thousands: ',',
            decimal: '.',
            allowZero: true,
            allowNegative: false
        });

        // Auto-fill nominal SPP ketika tahun ajaran diganti
        function fetchNominalSpp(tahun) {
            if (!tahun) return;
            $.ajax({
                url: '/app/siswa/nominal-spp',
                data: { tahun_akademik: tahun },
                success: function(res) {
                    var nominal = parseInt(res.nominal || 0, 10) * 100;
                    $('#spp_nominal').val(nominal).maskMoney('mask');
                    $('#spp_nominal').closest('.input-group').addClass('is-filled');
                }
            });
        }

        $('#tahun_akademik').on('change', function() {
            fetchNominalSpp($(this).val());
        });

        // Trigger awal jika sudah ada nilai tahun ajaran (mis. default selected)
        if ($('#tahun_akademik').val()) {
            fetchNominalSpp($('#tahun_akademik').val());
        }

        // Tandai is-filled saat user mengetik
        $('#FormSiswa input, #FormSiswa textarea').on('input', function() {
            $(this).closest('.input-group').addClass('is-filled');
        });

        var sedangMenyimpan = false;

        $(document).on('click', '#simpan', function(e) {
            e.preventDefault();
            if (sedangMenyimpan) return;
            sedangMenyimpan = true;
            $('#simpan').prop('disabled', true).text('Menyimpan...');

            // Konversi placeholder "-" / "" jadi nilai aman sebelum submit
            $('#FormSiswa input[type="text"], #FormSiswa textarea').each(function() {
                let v = $(this).val();
                if (v === '' || v === '-') {
                    // biarkan kosong untuk dikirim (server sudah nullable)
                    $(this).val('');
                }
            });

            var form = $('#FormSiswa')[0];
            var actionUrl = $('#FormSiswa').attr('action');
            var formData = new FormData(form);

            $.ajax({
                type: 'POST',
                url: actionUrl,
                data: formData,
                contentType: false,
                processData: false,
                success: function(result) {
                    if (result.success) {
                        Swal.fire({
                            title: result.msg,
                            text: "Simpan Data Siswa?",
                            icon: "success",
                            showDenyButton: true,
                            confirmButtonText: "Tambah Lagi",
                            denyButtonText: `Ke Daftar Siswa`
                        }).then((res) => {
                            if (res.isConfirmed) {
                                window.location.reload();
                            } else if (res.isDenied) {
                                window.location.href = '/app/siswa';
                            }
                        });
                    }
                },
                error: function(xhr) {
                    sedangMenyimpan = false;
                    $('#simpan').prop('disabled', false).text('Simpan data Siswa');
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        var errors = xhr.responseJSON.errors;
                        var list = Object.keys(errors)
                            .map(function(field) {
                                var label = $('[name="' + field + '"]')
                                    .closest('.input-group')
                                    .find('.form-label').text().trim();
                                return '<li>' + (label || field) + '</li>';
                            })
                            .join('');
                        Swal.fire({
                            icon: 'error',
                            title: 'Data belum lengkap',
                            html: 'Inputan berikut masih kosong / tidak valid:<ul class="text-start">' +
                                list + '</ul>'
                        });
                        // Highlight field invalid
                        $.each(errors, function(key) {
                            var el = $('[name="' + key + '"]');
                            el.addClass('is-invalid');
                            el.closest('.input-group').addClass('is-invalid');
                        });
                    } else {
                        Swal.fire('Error', 'Cek kembali input yang anda masukkan', 'error');
                    }
                }
            });
        });
    </script>
@endsection