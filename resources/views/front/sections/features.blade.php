@php($cols = ['2' => 'col-md-6', '3' => 'col-md-6 col-lg-4', '4' => 'col-md-6 col-lg-3'][$s->get('columns', '3')] ?? 'col-md-6 col-lg-4')
<section class="section @include('front.sections._bg')">
    @if($s->get('background') === 'dark')<div class="dark-deco"></div>@endif
    <div class="container position-relative">
        @include('front.sections._head')
        <div class="row g-4">
            @foreach($s->items() as $it)
                <div class="{{ $cols }} reveal d{{ $loop->index % 4 }}">
                    <div class="card-mk">
                        <div class="icon-box"><i class="fa-solid {{ $it['icon'] ?: 'fa-star' }}"></i></div>
                        <h3>{{ $it['title'] }}</h3>
                        @if($it['text'])<p class="mb-0">{{ $it['text'] }}</p>@endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
