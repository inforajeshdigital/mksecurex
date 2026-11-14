@php($items = \App\Models\Testimonial::where('is_active', true)->orderBy('sort_order')->get())
@if($items->isNotEmpty())
<section class="section @include('front.sections._bg')">
    <div class="container" data-track>
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
            <div class="reveal">
                @if($s->get('badge'))<span class="eyebrow">{{ $s->get('badge') }}</span>@endif
                @if($s->get('heading'))<h2 class="section-title mb-0">{{ $s->get('heading') }}</h2>@endif
            </div>
            @if($items->count() > 3)
                <div class="slider-nav d-flex gap-2"><button data-prev aria-label="Previous"><i class="fa-solid fa-arrow-left"></i></button><button data-next aria-label="Next"><i class="fa-solid fa-arrow-right"></i></button></div>
            @endif
        </div>
        <div class="testi-track">
            @foreach($items as $t)
                <figure class="testi m-0">
                    <i class="fa-solid fa-quote-right q"></i>
                    <div class="stars" aria-label="{{ $t->rating }} out of 5">@for($i = 1; $i <= 5; $i++)<i class="fa-{{ $i <= $t->rating ? 'solid' : 'regular' }} fa-star"></i>@endfor</div>
                    <blockquote class="m-0"><p>“{{ $t->content }}”</p></blockquote>
                    <figcaption class="who">
                        <span class="avatar">@if($t->image)<img src="{{ img_url($t->image) }}" alt="{{ $t->client_name }}" loading="lazy">@else{{ strtoupper(mb_substr($t->client_name, 0, 1)) }}@endif</span>
                        <span><strong>{{ $t->client_name }}</strong><small>{{ collect([$t->designation, $t->company])->filter()->implode(', ') }}</small></span>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endif
