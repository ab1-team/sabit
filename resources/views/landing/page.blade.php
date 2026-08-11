@extends('landing.layout')

@section('title', $page->title . ' — ' . $setting->school_name)

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-9">
                <h2 class="mb-4">{!! $page->title !!}</h2>

                @if ($page->image)
                    <img src="{{ Storage::disk('public')->url('landing/' . $page->image) }}"
                         class="img-fluid rounded mb-4" alt="">
                @endif

                <div class="lp-content">
                    {!! $page->content !!}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
