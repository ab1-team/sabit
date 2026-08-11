<form id="FormJenisBiaya" method="POST"
    action="{{ $mode === 'edit' ? '/app/jenis-biaya/'.$jenis_biaya->id : '/app/jenis-biaya' }}" class="text-start">
    @csrf
    @if ($mode === 'edit')
        @method('PUT')
    @endif

    <div class="row">
        <div class="col-md-6">
            <label>Tahun Akademik</label>
            <div class="input-group input-group-outline mb-3">
                <select name="angkatan" id="angkatan" class="form-control select2" required>
                    <option value="">-- Pilih Tahun Akademik --</option>
                    @foreach ($tahunAkademiks ?? [] as $ta)
                        <option value="{{ $ta->nama_tahun }}"
                            {{ (isset($jenis_biaya) && $jenis_biaya->angkatan == $ta->nama_tahun) ? 'selected' : '' }}>
                            {{ $ta->nama_tahun }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <label>Kode Akun</label>
            <div class="input-group input-group-outline mb-3">
                <input type="text" id="kode_akun" class="form-control"
                    value="{{ $jenis_biaya->get_jenis_pembayaran->kode_akun ?? '' }}" readonly>
            </div>
        </div>
        <div class="col-md-12">
            <label>Pilih Jenis Pembayaran</label>
            <div class="input-group input-group-outline flex-fill mb-3">
                <select name="id_jp" id="id_jp" class="form-control select2" required>
                    <option value="">-- Pilih Jenis Pembayaran --</option>
                    @foreach ($jenisPembayaran as $jp)
                        <option value="{{ $jp->id }}" data-kode="{{ $jp->kode_akun }}" data-jumlah="{{ $jp->jumlah }}"
                            {{ (isset($jenis_biaya) && $jenis_biaya->id_jp == $jp->id) ? 'selected' : '' }}>
                            {{ $jp->kode_akun }} - {{ $jp->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <label>Total Beban</label>
            <div class="input-group input-group-outline mb-3">
                <input type="text" name="total_beban" class="form-control nominal"
                    value="{{ isset($jenis_biaya) && $jenis_biaya->total_beban ? \App\Utils\Angka::format($jenis_biaya->total_beban, 2) : '' }}"
                    required>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-3">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-info" id="simpan">{{ $mode === 'edit' ? 'Update' : 'Simpan' }}</button>
    </div>
</form>
