<section class="section-sm {{ in_array($s->get('background'), ['dark', 'primary']) ? '' : 'bg-primary-section' }} @include('front.sections._bg')">
    <div class="container">
        @if($s->get('heading'))<h2 class="visually-hidden">{{ $s->get('heading') }}</h2>@endif
        <div class="row g-3">
            @foreach($s->items() as $it)
                <div class="col-6 col-lg-3 reveal d{{ $loop->index }}">
                    <div class="stat">
                        @if($it['icon'])<i class="fa-solid {{ $it['icon'] }} d-block"></i>@endif
                        <div class="val">@if(is_numeric($it['value']))<span data-count="{{ $it['value'] }}">0</span>@else{{ $it['value'] }}@endif{{ $it['suffix'] ?? '' }}</div>
                        <div class="lbl">{{ $it['title'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
