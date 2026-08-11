@extends('layouts.tenant.base')

@section('content')
<style>
    .tox .tox-promotion {
    background: repeating-linear-gradient(transparent 0 1px, transparent 1px 39px) center top 39px / 100% calc(100% - 39px) no-repeat;
    background-color: #fff;
    grid-column: 2;
    grid-row: 1;
    padding-inline-end: 8px;
    padding-inline-start: 4px;
    padding-top: 5px;
    display: none;
    }
    .tox .tox-statusbar__branding svg {
    fill: rgba(34, 47, 62, .8);
    height: 1.14em;
    vertical-align: -.28em;
    width: 3.6em;
    display: none;
}
</style>
    <div class="app-main__inner">
        <div class="tab-content">
            <div class="tab-pane fade show active" id="" role="tabpanel">
                <div class="row">
                    <div class="col-md-12">
                        <div class="main-card mb-3 card">
                            <div class="card-body">
                                <h5 class="card-title">Tanda tangan <b>Pelaporan</b></h5>
                                <form action="/app/pengaturan/simpan/ttd-pelaporan" method="post" id="formTtdPelaporan" height>
                                    @csrf
                    
                                    <input type="hidden" name="field" id="field" value="tanda_tangan_pelaporan">
                                    <textarea class="tiny-mce-editor" name="tanda_tangan" id="tanda_tangan">{!! $ttd->tanda_tangan ?? '' !!}</textarea>
                                </form>
                    
                                {{-- @if (!$tanggal)
                                    <small class="text-danger">
                                        Masukkan <span style="text-transform: lowercase">
                                            *{tanggal}* pada form tanda tangan untuk menuliskan tanggal laporan dibuat. <b>Hapus tanda bintang
                                                (*)</b>
                                        </span>
                                    </small>
                                @endif --}}
                                <div class="d-flex justify-content-end mt-3">
                                    <button type="button" id="simpanTtdPelaporan" class="btn btn-dark btn-sm ms-2">
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        if (tinymce.get('tanda_tangan')) {
        tinymce.get('tanda_tangan').remove();
            }

            tinymce.init({
                selector: '#tanda_tangan',
                height: 400,
                readonly: false,
                plugins: 'table visualblocks fullscreen',
                toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | align | table fullscreen | removeformat',
                font_family_formats: 'Arial=arial,helvetica,sans-serif; Courier New=courier new,courier,monospace;',
                valid_children: '+table[tr],+tr[td],+tbody[tr],+thead[tr],+tfoot[tr],+table[tbody],+table[thead],+table[tfoot]',
                extended_valid_elements: 'table[class|style|border|cellspacing|cellpadding|width],tr[class|style],td[class|style|width|colspan|rowspan],tbody[class|style],thead[class|style],tfoot[class|style],strong[class|style]',
                custom_elements: '~tanda_tangan',
                verify_html: false,
                cleanup: false,
                entity_encoding: 'raw',
            });

        $(document).on('click', '#simpanTtdPelaporan', function (e) {
            e.preventDefault();

            tinymce.triggerSave();

            let form = document.getElementById('formTtdPelaporan');
            let actionUrl = form.action;
            let formData = new FormData(form);

            $.ajax({
                url: actionUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json', // 🔴 WAJIB
                success: function (result) {

                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });

                    Toast.fire({
                        icon: 'success',
                        title: result.msg
                    });
                },
                error: function (xhr) {
                    Swal.fire('Galat', 'Gagal menyimpan tanda tangan', 'error');
                    console.log(xhr.responseText);
                }
            });
        });
    </script>
@endsection
