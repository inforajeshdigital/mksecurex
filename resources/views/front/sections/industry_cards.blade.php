@php($cards = \App\Models\Industry::published()->ordered()->when($s->get('limit'), fn ($q, $l) => $q->limit($l))->get())
<section class="section @include('front.sections._bg')">
    <div class="container">
        @include('front.sections._head')
        @include('front.partials.industry-grid', ['industries' => $cards])
        @if($s->get('button_text'))
            <div class="text-center mt-5 reveal"><a href="{{ link_url($s->get('button_url')) }}" class="btn btn-outline-blue">{{ $s->get('button_text') }} <i class="fa-solid fa-arrow-right"></i></a></div>
        @endif
    </div>
</section>
