@php
    $name = $name ?? '';
    $label = $label ?? '';
    $value = $value ?? old($name);
    $required = $required ?? false;
    $placeholder = $placeholder ?? '';
    $filled = $value !== null && $value !== '';
    $extraAttrs = $extraAttrs ?? '';
    $help = $help ?? null;
    $colClass = $colClass ?? 'col-md-12';
    $inputClass = $inputClass ?? '';
    $rows = $rows ?? 3;
@endphp

<div class="{{ $colClass }}">
    <div class="input-group input-group-outline mb-3 @if($filled) is-filled @endif">
        <textarea name="{{ $name }}" rows="{{ $rows }}" class="form-control {{ $inputClass }}"
                  placeholder="{{ $placeholder }}"
                  {{ $required ? 'required' : '' }}{!! $extraAttrs !!}>{!! $value !!}</textarea>
        @if ($label)
            <label class="form-label">
                {!! $label !!}
                @if ($required)<span class="text-danger">*</span>@endif
            </label>
        @endif
    </div>
    @if ($help)
        <div class="small text-muted mb-3" style="margin-top:-.5rem;">{!! $help !!}</div>
    @endif
</div>
