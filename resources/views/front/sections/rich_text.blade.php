<section class="py-4 @include('front.sections._bg')">
    <div class="container">
        <div class="mx-auto reveal" style="max-width:860px">
            @if($s->get('heading'))<h2 class="h3 mb-3">{{ $s->get('heading') }}</h2>@endif
            <div class="rich-content">{!! $s->get('content') !!}</div>
        </div>
    </div>
</section>
