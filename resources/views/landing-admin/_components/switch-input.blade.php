@php
    $name = $name ?? '';
    $label = $label ?? '';
    $value = $value ?? old($name, $checkedDefault ?? false);
    $checked = $value ? true : false;
    $extraAttrs = $extraAttrs ?? '';
    $colClass = $colClass ?? 'col-md-12';
    $inputId = $inputId ?? ($name . '_switch');
@endphp

<div class="{{ $colClass }} d-flex align-items-end pb-3">
    <div class="form-check form-switch m-0">
        <input class="form-check-input" type="checkbox" name="{{ $name }}" value="1"
               id="{{ $inputId }}"
               {{ $checked ? 'checked' : '' }}
               {!! $extraAttrs !!}>
        <label class="form-check-label" for="{{ $inputId }}">{!! $label !!}</label>
    </div>
</div>
