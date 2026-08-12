@if (($p->is_published ?? false))
    <span class="lp-status-badge is-published">Published</span>
@else
    <span class="lp-status-badge is-draft">Draft</span>
@endif
