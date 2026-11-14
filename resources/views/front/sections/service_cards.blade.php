{{-- Homepage shows 6 cards by default (2 full rows); other pages show all. "Max items" in admin overrides this. --}}
@php($cards = \App\Models\Service::published()->ordered()->when((int) $s->get('limit') ?: ((($page->slug ?? '') === 'home') ? 6 : null), fn ($q, $l) => $q->limit($l))->get())
<section class="section @include('front.sections._bg')">
    <div class="container">
        @include('front.sections._head')
        @include('front.partials.service-grid', ['services' => $cards])
        @if($s->get('button_text'))
            <div class="text-center mt-5 reveal"><a href="{{ link_url($s->get('button_url')) }}" class="btn btn-outline-blue">{{ $s->get('button_text') }} <i class="fa-solid fa-arrow-right"></i></a></div>
        @endif
    </div>
</section>
