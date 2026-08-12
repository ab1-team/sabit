@php
    $subtitle = $subtitle ?? null;
    $back = $back ?? null;
    $actions = $actions ?? null;
    $titleSlot = $titleSlot ?? '';
@endphp
<div class="lp-page-header d-flex flex-wrap justify-content-between align-items-start mb-3">
    <div class="flex-grow-1 min-w-0">
        @if ($subtitle)
            <div class="lp-page-eyebrow">{{ $subtitle }}</div>
        @endif
        {!! $titleSlot !!}
    </div>
    @if ($actions || $back)
        <div class="d-flex flex-wrap gap-2 align-items-center ms-2">
            {!! $actions !!}
            @if ($back)
                <a href="{{ $back }}" class="btn btn-sm btn-light">
                    <span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle;">arrow_back</span>
                    Kembali
                </a>
            @endif
        </div>
    @endif
</div>
