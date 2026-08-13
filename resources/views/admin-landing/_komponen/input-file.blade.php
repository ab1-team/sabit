@php
    $name = $name ?? '';
    $label = $label ?? '';
    $current = $current ?? null;
    $currentUrl = $currentUrl ?? null;
    $required = $required ?? false;
    $accept = $accept ?? 'image/*';
    $help = $help ?? null;
    $colClass = $colClass ?? 'col-md-12';
    $emptyIcon = $emptyIcon ?? 'add_photo_alternate';
    $inputId = $inputId ?? ($name . 'Input');
    $boxId = $boxId ?? ($name . 'PreviewBox');
    $hint = $hint ?? 'Klik untuk pilih file';
    $fileName = $fileName ?? ($current ?: null);
    $extraInfo = $extraInfo ?? null;
@endphp

<div class="{{ $colClass }}">
    <label class="form-label small fw-bold d-block">{!! $label !!} @if($required)<span class="text-danger">*</span>@endif</label>
    <label for="{{ $inputId }}" class="lp-preview-box d-block" id="{{ $boxId }}">
        @if ($currentUrl)
            <img src="{{ $currentUrl }}" alt="{{ $label }}" id="{{ $boxId }}Img">
        @else
            <span class="material-symbols-rounded lp-preview-empty" id="{{ $boxId }}Empty">{{ $emptyIcon }}</span>
        @endif
        <span class="lp-preview-hint">{{ $hint }}</span>
    </label>
    <input type="file" name="{{ $name }}" class="d-none" accept="{{ $accept }}"
           id="{{ $inputId }}" {{ $required ? 'required' : '' }}>
    @if ($fileName)
        <div class="small text-muted mt-1 text-center">File saat ini: <code>{{ $fileName }}</code></div>
    @endif
    @if ($extraInfo)
        <div class="small text-muted mt-1 text-center">{!! $extraInfo !!}</div>
    @endif
    @if ($help)
        <div class="small text-muted mt-1 text-center">{{ $help }}</div>
    @endif
</div>
