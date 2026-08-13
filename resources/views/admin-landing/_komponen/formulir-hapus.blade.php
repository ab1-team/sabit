@php
    $action = $action ?? '';
    $confirm = $confirm ?? 'Yakin ingin menghapus data ini?';
    $btnClass = $btnClass ?? 'btn btn-sm btn-outline-danger';
    $iconOnly = $iconOnly ?? false;
    $icon = $icon ?? 'delete';
    $label = $label ?? 'Hapus';
    $extraClass = $extraClass ?? '';
@endphp
<form action="{{ $action }}" method="POST" class="d-inline {{ $extraClass }}"
      data-confirm="{{ $confirm }}">
    @csrf
    @method('DELETE')
    <button type="submit" class="{{ $btnClass }}">
        <span class="material-symbols-rounded" style="font-size:16px;">{{ $icon }}</span>
        @if (!$iconOnly) {{ $label }} @endif
    </button>
</form>
