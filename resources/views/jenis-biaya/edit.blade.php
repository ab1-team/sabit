@extends('layouts.tenant.base')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div
                        class="bg-gradient-secondary shadow-secondary border-radius-lg pt-3 pb-1 d-flex justify-content-between align-items-center">
                        <h6 class="text-white text-capitalize ps-3" id="headerTitle">
                            Edit Jenis dan Nominal Pembayaran
                        </h6>
                    </div>
                </div>
                <br>
                <div class="card-body">
                    <form id="FormJenisBiaya" method="POST" action="/app/jenis-biaya/{{ $jenis_biaya->id }}"
                        class="text-start">
                        @csrf
                        @method('PUT')
                        <div class="row g-2">
                            <div class="col-12 col-md-4">
                                <label class="form-label">Tahun Akademik</label>
                                <div class="input-group input-group-outline mb-3">
                                    <select name="angkatan" id="angkatan" class="form-control select2" required>
                                        <option value="">-- Pilih Tahun Akademik --</option>
                                        @foreach ($tahunAkademiks ?? [] as $ta)
                                            <option value="{{ $ta->nama_tahun }}"
                                                {{ $jenis_biaya->angkatan == $ta->nama_tahun ? 'selected' : '' }}>
                                                {{ $ta->nama_tahun }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">Pilih Jenis Pembayaran</label>
                                <div class="input-group input-group-outline mb-3">
                                    <select name="id_jp" id="id_jp" class="form-control select2">
                                        <option value="">-- Pilih Jenis Pembayaran --</option>
                                        @foreach ($jenisPembayaran as $jp)
                                            <option value="{{ $jp->id }}" data-kode="{{ $jp->kode_akun }}" data-jumlah="{{ $jp->jumlah }}"
                                                {{ $jp->id == $jenis_biaya->id_jp ? 'selected' : '' }}>
                                                {{ $jp->kode_akun }} - {{ $jp->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">Kode Akun</label>
                                <div class="input-group input-group-outline mb-3">
                                    <input type="text" id="kode_akun" class="form-control"
                                        value="{{ $jenis_biaya->get_jenis_pembayaran->kode_akun ?? '' }}" readonly>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">Total Beban</label>
                                <div class="input-group input-group-outline mb-3">
                                    <input type="text" name="total_beban" class="form-control nominal"
                                        value="{{ \App\Utils\Angka::format($jenis_biaya->total_beban, 2) }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="action-toolbar mt-3">
                            <a href="/app/jenis-biaya" class="btn btn-secondary w-100 w-md-auto">Kembali</a>
                            <button type="submit" class="btn btn-info w-100 w-md-auto" id="simpan">Update Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5',
                allowClear: false
            });

            $(".nominal").maskMoney({
                allowNegative: true
            });

            $('#id_jp').on('change', function() {
                var opt = $(this).find(':selected');
                $('#kode_akun').val(opt.data('kode') || '');
                var jumlah = opt.data('jumlah');
                if (jumlah) {
                    $('input[name="total_beban"]').maskMoney('mask', jumlah);
                }
            });
        });

        $(document).on('click', '#simpan', function(e) {
            e.preventDefault();
            $('small').html('');
            var form = $('#FormJenisBiaya');
            var actionUrl = form.attr('action');
            var nominalInput = form.find('input[name="total_beban"]');
            if (nominalInput.length && typeof nominalInput.maskMoney === 'function') {
                nominalInput.val(nominalInput.maskMoney('unmasked')[0]);
            }

            $.ajax({
                type: 'POST',
                url: actionUrl,
                data: form.serialize(),
                success: function(result) {
                    if (result.success) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: result.msg,
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didClose: () => {
                                window.location.href = '/app/jenis-biaya';
                            }
                        });
                    }
                },
                error: function(result) {
                    const response = result.responseJSON;
                    Swal.fire('Error', 'Cek kembali input yang anda masukkan', 'error');
                    if (response && typeof response === 'object') {
                        $.each(response, function(key, message) {
                            $('#' + key).closest('.input-group.input-group-static').addClass(
                                'is-invalid');
                            $('#msg_' + key).html(message);
                        });
                    }
                }
            });
        });
    </script>
@endsection
