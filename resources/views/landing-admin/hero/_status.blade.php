@if (($slide->is_active ?? false))
    <span class="lp-status-badge is-active">Aktif</span>
@else
    <span class="lp-status-badge is-inactive">Nonaktif</span>
@endif
