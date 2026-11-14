{{-- Global CTA band from settings (or overridden by section values) --}}
@php
    $h = $heading ?? setting('cta_heading');
    $t = $text ?? setting('cta_text');
    $b1 = $btn1 ?? ['text' => setting('cta_primary_text'), 'url' => setting('cta_primary_url')];
    $b2 = $btn2 ?? ['text' => setting('cta_secondary_text'), 'url' => setting('cta_secondary_url')];
    $nt = setting_on('cta_new_tab') ? 'target="_blank" rel="noopener"' : '';
@endphp
<section class="section-sm">
    <div class="container">
        <div class="cta-band reveal">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <h2 class="mb-2">{{ $h }}</h2>
                    @if($t)<p class="mb-0">{{ $t }}</p>@endif
                </div>
                <div class="col-lg-4 d-flex flex-wrap gap-3 justify-content-lg-end">
                    @if(!empty($b1['text']))<a href="{{ link_url($b1['url']) }}" class="btn btn-cta btn-lg" {!! $nt !!}>{{ $b1['text'] }} <i class="fa-solid fa-arrow-right"></i></a>@endif
                    @if(!empty($b2['text']))<a href="{{ link_url($b2['url']) }}" class="btn btn-outline-light-mk btn-lg" {!! $nt !!}>{{ $b2['text'] }}</a>@endif
                </div>
            </div>
        </div>
    </div>
</section>
