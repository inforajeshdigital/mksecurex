@php($slides = \App\Models\Slider::where('is_active', true)->orderBy('sort_order')->get())
@if($slides->isNotEmpty())
<section class="hero" data-hero aria-roledescription="carousel">
    @foreach($slides as $sl)
        <div class="hero-slide {{ $loop->first ? 'active' : '' }}" aria-roledescription="slide" aria-label="{{ $loop->iteration }} of {{ $slides->count() }}">
            <div class="hero-pattern"></div>
            @if($sl->image)
                <div class="hero-bg" style="background-image:url('{{ img_url($sl->image) }}')" role="img" aria-label="{{ $sl->image_alt ?: $sl->heading }}"></div>
                <div class="hero-overlay"></div>
            @endif
            <div class="hero-swoosh"></div><div class="hero-swoosh two"></div>
            <div class="container">
                <div class="hero-inner">
                    <div class="row align-items-center w-100 g-5">
                        <div class="col-lg-7 hero-anim">
                            @if($sl->badge)<div><span class="badge-pill"><i class="fa-solid fa-shield-halved"></i> {{ $sl->badge }}</span></div>@endif
                            @if($loop->first)<h1>{{ $sl->heading }}</h1>@else<h2 class="h1" style="color:#fff;font-size:clamp(2.1rem,5vw,3.9rem);margin:1.2rem 0 1rem">{{ $sl->heading }}</h2>@endif
                            @if($sl->subheading)<p class="hero-sub">{{ $sl->subheading }}</p>@endif
                            @if($sl->description)<p class="hero-desc">{{ $sl->description }}</p>@endif
                            <div class="d-flex flex-wrap gap-3">
                                @if($sl->btn1_text)<a href="{{ link_url($sl->btn1_url) }}" class="btn btn-gold btn-lg">{{ $sl->btn1_text }} <i class="fa-solid fa-arrow-right"></i></a>@endif
                                @if($sl->btn2_text)<a href="{{ link_url($sl->btn2_url) }}" class="btn btn-outline-light-mk btn-lg">{{ $sl->btn2_text }}</a>@endif
                            </div>
                        </div>
                        <div class="col-lg-5 d-none d-lg-block">
                            @if($sl->image)
                                <div class="hero-img-card"><img src="{{ img_url($sl->image) }}" alt="{{ $sl->image_alt ?: $sl->heading }}" @if(!$loop->first) loading="lazy" @endif></div>
                            @else
                                <div class="hero-emblem">
                                    <div class="ring"></div><div class="ring r2"></div>
                                    <div class="shield"><i class="fa-solid fa-user-shield"></i></div>
                                    <div class="chip c1"><i class="fa-solid fa-shield-halved"></i> Trained Guards</div>
                                    <div class="chip c2"><i class="fa-solid fa-people-group"></i> Skilled Manpower</div>
                                    <div class="chip c3"><i class="fa-solid fa-building-circle-check"></i> Facility Support</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    @if($slides->count() > 1)
    <div class="hero-controls">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="hero-dots">@foreach($slides as $sl)<button class="{{ $loop->first ? 'active' : '' }}" aria-label="Go to slide {{ $loop->iteration }}"></button>@endforeach</div>
            <div class="hero-arrows d-flex gap-2"><button data-prev aria-label="Previous slide"><i class="fa-solid fa-arrow-left"></i></button><button data-next aria-label="Next slide"><i class="fa-solid fa-arrow-right"></i></button></div>
        </div>
    </div>
    @endif
</section>
<div class="hero-trust">
    <div class="container">
        <div class="trust-bar reveal">
            <div class="row g-3">
                <div class="col-md-4"><div class="trust-item"><i class="fa-solid fa-layer-group"></i><div><strong>{{ $navServices->count() }} Integrated Services</strong><span>Security, manpower & facility</span></div></div></div>
                <div class="col-md-4"><div class="trust-item"><i class="fa-solid fa-business-time"></i><div><strong>Short & Long-Term</strong><span>Flexible staffing engagements</span></div></div></div>
                <div class="col-md-4"><div class="trust-item"><i class="fa-solid fa-headset"></i><div><strong>Single Point of Contact</strong><span>Dedicated coordination</span></div></div></div>
            </div>
        </div>
    </div>
</div>
@endif
