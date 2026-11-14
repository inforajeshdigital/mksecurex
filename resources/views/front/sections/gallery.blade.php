@php($cols = ['2' => 'col-md-6', '3' => 'col-md-6 col-lg-4', '4' => 'col-6 col-lg-3'][$s->get('columns', '3')] ?? 'col-md-6 col-lg-4')
<section class="section @include('front.sections._bg')">
    <div class="container">
        @include('front.sections._head')
        <div class="row g-4">
            @foreach($s->items() as $it)
                @if(!empty($it['image']))
                <div class="{{ $cols }} reveal">
                    <figure class="m-0 rounded-4 overflow-hidden shadow-sm position-relative">
                        <img src="{{ img_url($it['image']) }}" alt="{{ $it['title'] ?: $s->get('heading') }}" loading="lazy" class="w-100" style="aspect-ratio:4/3;object-fit:cover">
                        @if($it['title'])<figcaption class="position-absolute bottom-0 start-0 end-0 p-3 text-white fw-semibold" style="background:linear-gradient(transparent,rgba(6,23,58,.85))">{{ $it['title'] }}</figcaption>@endif
                    </figure>
                </div>
                @endif
            @endforeach
        </div>
    </div>
</section>
