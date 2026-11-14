{{-- Inner page banner. Vars: $title, $subtitle, $badge, $image, $imageAlt, $breadcrumbs, $buttons --}}
<section class="page-hero">
    <div class="hero-pattern"></div>
    @if(!empty($image))<div class="hero-bg" style="background-image:url('{{ img_url($image) }}')" role="img" aria-label="{{ $imageAlt ?? $title }}"></div>@endif
    <div class="hero-swoosh"></div>
    <div class="container">
        @if(!empty($breadcrumbs))
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb-mk">
                    <li><a href="{{ url('/') }}"><i class="fa-solid fa-house"></i> Home</a></li>
                    @foreach($breadcrumbs as $b)
                        <li @if($loop->last) aria-current="page" @endif>@if($loop->last){{ $b['label'] }}@else<a href="{{ $b['url'] }}">{{ $b['label'] }}</a>@endif</li>
                    @endforeach
                </ol>
            </nav>
        @endif
        @if(!empty($badge))<span class="eyebrow mt-2">{{ $badge }}</span>@endif
        <h1>{{ $title }}</h1>
        @if(!empty($subtitle))<p class="lead mb-0">{{ $subtitle }}</p>@endif
        @if(!empty($buttons))
            <div class="d-flex flex-wrap gap-3 mt-4">
                @foreach($buttons as $i => $b)
                    @if(!empty($b['text']))<a href="{{ link_url($b['url']) }}" class="btn {{ $i === 0 ? 'btn-gold' : 'btn-outline-light-mk' }}">{{ $b['text'] }}</a>@endif
                @endforeach
            </div>
        @endif
    </div>
</section>
