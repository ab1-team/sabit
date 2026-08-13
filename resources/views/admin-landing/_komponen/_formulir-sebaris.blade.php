@php
    /**
     * Form Tambah/Edit item generik untuk CRUD halaman admin-landing.
     *
     * Memakai pola "form-collapse" sederhana: tambah/edit di-trigger via
     * JavaScript (Bootstrap collapse). Action & method di-set via parameter.
     *
     * Variabel yang diharapkan dari parent view:
     *   - $action (route URL)
     *   - $method ('POST' atau 'PUT')
     *   - $fields (array of field keys)
     *   - $item (model instance; gunakan (object) untuk tambah baru => semua nilai null)
     */
    $method = $method ?? 'POST';
    $fields = $fields ?? ['title' => 'Judul', 'is_active' => 'Aktif'];
    $item = $item ?? (object) [];
@endphp
<form action="{{ $action }}" method="POST" class="lp-ajax card my-3 shadow-sm" enctype="multipart/form-data">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif
    <div class="card-body p-3">
        <div class="row">
            @foreach ($fields as $key => $meta)
                @php
                    $label = is_string($meta) ? $meta : ($meta['label'] ?? ucfirst($key));
                    $type = is_array($meta) ? ($meta['type'] ?? 'text') : 'text';
                    $required = is_array($meta) ? ($meta['required'] ?? false) : false;
                    $help = is_array($meta) ? ($meta['help'] ?? null) : null;
                    $val = old($key, $item->{$key} ?? '');
                    if (in_array($key, ['is_published', 'is_active', 'is_lead'], true)) {
                        $val = (bool) ($item->{$key} ?? false);
                    }
                @endphp
                <div class="col-md-{{ is_array($meta) && isset($meta['col']) ? $meta['col'] : 12 }} mb-2">
                    @if ($type === 'textarea')
                        <div class="input-group input-group-outline @if ($val) is-filled @endif">
                            <label class="form-label">{{ $label }} @if ($required)<span class="text-danger">*</span>@endif</label>
                            <textarea name="{{ $key }}" class="form-control" rows="3" @if ($required) required @endif>{{ $val }}</textarea>
                        </div>
                    @elseif ($type === 'checkbox')
                        <div class="form-check form-switch">
                            <input type="hidden" name="{{ $key }}" value="0">
                            <input class="form-check-input" type="checkbox" name="{{ $key }}" value="1" id="{{ $key }}_{{ spl_object_id($item) }}" @checked($val)>
                            <label class="form-check-label" for="{{ $key }}_{{ spl_object_id($item) }}">{{ $label }}</label>
                        </div>
                    @elseif ($type === 'number')
                        <div class="input-group input-group-outline @if ($val !== null && $val !== '') is-filled @endif">
                            <label class="form-label">{{ $label }} @if ($required)<span class="text-danger">*</span>@endif</label>
                            <input type="number" name="{{ $key }}" class="form-control" @if ($required) required @endif value="{{ $val }}">
                        </div>
                    @elseif ($type === 'date')
                        <div class="input-group input-group-outline @if ($val) is-filled @endif">
                            <label class="form-label">{{ $label }} @if ($required)<span class="text-danger">*</span>@endif</label>
                            <input type="date" name="{{ $key }}" class="form-control" @if ($required) required @endif value="{{ $val }}">
                        </div>
                    @else
                        <div class="input-group input-group-outline @if ($val) is-filled @endif">
                            <label class="form-label">{{ $label }} @if ($required)<span class="text-danger">*</span>@endif</label>
                            <input type="{{ $type }}" name="{{ $key }}" class="form-control" @if ($required) required @endif value="{{ $val }}">
                        </div>
                    @endif
                    @if ($help)
                        <div class="small text-muted mt-1">{{ $help }}</div>
                    @endif
                </div>
            @endforeach
        </div>
        <div class="d-flex justify-content-end mt-2">
            <button type="submit" class="btn btn-primary btn-sm">
                <span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle;">save</span>
                Simpan
            </button>
        </div>
    </div>
</form>
