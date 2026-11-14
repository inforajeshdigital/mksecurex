@php($items = $s->items())
<section class="section @include('front.sections._bg')">
    <div class="container">
        @include('front.sections._head')
        <div class="row g-5 process-wrap">
            @foreach($items as $it)
                <div class="col-sm-6 col-lg-{{ max(3, intdiv(12, max(count($items), 1))) }} reveal d{{ $loop->index }}">
                    <div class="step">
                        <div class="num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                        <h3>{{ $it['title'] }}</h3>
                        <p class="mb-0">{{ $it['text'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
