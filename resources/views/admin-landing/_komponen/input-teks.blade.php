    @php
    $name = $name ?? '';
    $label = $label ?? '';
    $type = $type ?? 'text';
    $value = $value ?? old($name);
    $required = $required ?? false;
    $placeholder = $placeholder ?? '';
    $filled = $value !== null && $value !== '';
    $extraAttrs = $extraAttrs ?? '';
    $help = $help ?? null;
    $colClass = $colClass ?? 'col-md-12';
    $inputClass = $inputClass ?? '';
    $accept = $accept ?? null;
    $min = $min ?? null;
    $max = $max ?? null;
    $rows = $rows ?? null;
@endphp

<div class="{{ $colClass }}">
    <div class="input-group input-group-outline mb-3 @if($filled) is-filled @endif">
        @if ($type === 'textarea')
            <textarea name="{{ $name }}" rows="{{ $rows ?? 3 }}" class="form-control {{ $inputClass }}"
                      {{ $required ? 'required' : '' }}
                      @if($placeholder !== '') placeholder="{{ $placeholder }}"@endif{!! $extraAttrs !!}>{!! $value !!}</textarea>
        @else
            <label class="form-label">
                {!! $label !!}
                @if ($required)<span class="text-danger">*</span>@endif
            </label>
            <input type="{{ $type }}"
                   name="{{ $name }}"
                   class="form-control {{ $inputClass }}"
                   value="{{ $value }}"
                   @if($placeholder !== '') placeholder="{{ $placeholder }}"@endif
                   @if($required) required @endif
                   @if($accept) accept="{{ $accept }}" @endif
                   @if($min !== null) min="{{ $min }}" @endif
                   @if($max !== null) max="{{ $max }}" @endif
                   {!! $extraAttrs !!}>
        @endif
    </div>
    @if ($help)
        <div class="small text-muted mb-3" style="margin-top:-.5rem;">{!! $help !!}</div>
    @endif
</div>
