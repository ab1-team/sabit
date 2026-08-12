@if (($v->is_published ?? false))
    <span class="lp-status-badge is-published">Aktif</span>
@else
    <span class="lp-status-badge is-draft">Draft</span>
@endif
