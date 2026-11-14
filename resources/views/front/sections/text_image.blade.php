@php($imgLeft = $s->get('layout', 'right') === 'left')
<section class="section @include('front.sections._bg')">
    <div class="container">
        <div class="row align-items-center g-5 {{ $imgLeft ? 'flex-lg-row-reverse' : '' }}">
            <div class="col-lg-6 reveal">
                @if($s->get('badge'))<span class="eyebrow">{{ $s->get('badge') }}</span>@endif
                @if($s->get('heading'))<h2 class="section-title">{{ $s->get('heading') }}</h2>@endif
                @if($s->get('content'))<div class="rich-content section-lead">{!! $s->get('content') !!}</div>@endif
                @if(count($s->items()))
                    @php($hasText = collect($s->items())->contains(fn ($i) => !empty($i['text'])))
                    @if($hasText)
                        <ul class="point-list">
                            @foreach($s->items() as $it)
                                <li><span class="pi"><i class="fa-solid {{ $it['icon'] ?: 'fa-check' }}"></i></span><div><h4>{{ $it['title'] }}</h4>@if($it['text'])<p>{{ $it['text'] }}</p>@endif</div></li>
                            @endforeach
                        </ul>
                    @else
                        <ul class="check-list my-4">@foreach($s->items() as $it)<li>{{ $it['title'] }}</li>@endforeach</ul>
                    @endif
                @endif
                @if($s->get('button_text'))<a href="{{ link_url($s->get('button_url')) }}" class="btn btn-blue mt-2">{{ $s->get('button_text') }} <i class="fa-solid fa-arrow-right"></i></a>@endif
            </div>
            <div class="col-lg-6 reveal d2">
                <div class="media-frame {{ $imgLeft ? 'left' : '' }} position-relative">
                    @if($s->get('image'))
                        <img src="{{ img_url($s->get('image')) }}" alt="{{ $s->get('image_alt', $s->get('heading')) }}" loading="lazy" width="640" height="540">
                    @else
                        <div class="media-ph"><img class="logo-ph" src="{{ img_url(setting('site_logo')) }}" alt="{{ setting('site_logo_alt') }}" loading="lazy"></div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
