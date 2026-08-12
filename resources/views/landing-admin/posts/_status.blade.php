@if (($post->is_published ?? false))
    <span class="lp-status-badge is-published">Published</span>
@else
    <span class="lp-status-badge is-draft">Draft</span>
@endif
@if (($post->is_featured ?? false))
    <span class="lp-status-badge is-published ms-1" style="background:rgba(245,158,11,.15);color:#b45309;">Featured</span>
@endif
