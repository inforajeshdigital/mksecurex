@extends('layouts.app')
@php($schemaFaqs = $industry->faqs)

@section('content')
    @include('front.partials.page-hero', [
        'title' => $industry->title,
        'subtitle' => $industry->short_description,
        'badge' => 'Industries We Serve',
        'image' => $industry->banner_image ?: $industry->image,
        'imageAlt' => $industry->image_alt,
        'breadcrumbs' => $breadcrumbs,
        'buttons' => [['text' => 'Request a Quote', 'url' => '#enquiry'], ['text' => 'View Services', 'url' => '#industry-services']],
    ])

    <section class="section">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-8">
                    @if($industry->intro)<p class="detail-intro reveal">{{ $industry->intro }}</p>@endif
                    @if($industry->image)
                        <img src="{{ img_url($industry->image) }}" alt="{{ $industry->image_alt ?: $industry->title }}" class="w-100 rounded-4 shadow my-4 reveal" style="aspect-ratio:16/8;object-fit:cover" loading="lazy">
                    @endif
                    <h2 class="h3 mt-4 reveal">Industry Overview</h2>
                    <div class="rich-content reveal">{!! $industry->description !!}</div>

                    @if(!empty($industry->workforce_requirements))
                        <h2 class="h3 mt-5 mb-3 reveal">Workforce Requirements</h2>
                        <div class="row g-3">
                            @foreach($industry->workforce_requirements as $w)
                                <div class="col-md-6 reveal"><div class="mini-card"><span class="mi"><i class="fa-solid fa-user-check"></i></span><div><h4 class="mb-0">{{ $w['title'] }}</h4>@if($w['text'])<p>{{ $w['text'] }}</p>@endif</div></div></div>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="col-lg-4">
                    <div class="sticky-side">
                        <h3 class="h5 mb-3">Other Industries</h3>
                        <ul class="side-links mb-4">
                            @foreach($navIndustries as $in)
                                <li><a href="{{ $in->url }}" class="{{ $in->id === $industry->id ? 'active' : '' }}">{{ $in->title }} <i class="fa-solid fa-angle-right"></i></a></li>
                            @endforeach
                        </ul>
                        <div class="side-card">
                            <h4>Staffing for {{ $industry->title }}</h4>
                            <p class="small">Talk to our team about your site, shifts and headcount.</p>
                            <a href="#enquiry" class="btn btn-gold btn-sm">Request a Quote</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if($industry->services->isNotEmpty())
        <section class="section bg-light-mk" id="industry-services">
            <div class="container">
                <div class="text-center mb-5 reveal"><span class="eyebrow">Our Solutions</span><h2 class="section-title">Services We Provide for {{ $industry->title }}</h2></div>
                @include('front.partials.service-grid', ['services' => $industry->services])
            </div>
        </section>
    @endif

    @if(!empty($industry->challenges) || !empty($industry->solutions))
        <section class="section">
            <div class="container">
                <div class="row g-5">
                    @if(!empty($industry->challenges))
                        <div class="col-lg-6">
                            <span class="eyebrow reveal">Operational Challenges</span>
                            <h2 class="section-title reveal">Challenges in {{ $industry->title }}</h2>
                            @foreach($industry->challenges as $c)
                                <div class="mini-card mb-3 reveal" style="height:auto"><span class="mi" style="background:#fdecec;color:#c0392b"><i class="fa-solid fa-triangle-exclamation"></i></span><div><h4>{{ $c['title'] }}</h4><p>{{ $c['text'] }}</p></div></div>
                            @endforeach
                        </div>
                    @endif
                    @if(!empty($industry->solutions))
                        <div class="col-lg-6">
                            <span class="eyebrow reveal">Industry-Specific Solutions</span>
                            <h2 class="section-title reveal">How We Solve Them</h2>
                            @foreach($industry->solutions as $sol)
                                <div class="mini-card mb-3 reveal" style="height:auto"><span class="mi"><i class="fa-solid fa-lightbulb"></i></span><div><h4>{{ $sol['title'] }}</h4><p>{{ $sol['text'] }}</p></div></div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    @if(!empty($industry->benefits))
        <section class="section bg-dark-section">
            <div class="dark-deco"></div>
            <div class="container position-relative">
                <div class="text-center mb-5 reveal"><span class="eyebrow">Benefits</span><h2 class="section-title">Benefits of Working With {{ setting('site_name') }}</h2></div>
                <div class="row g-4 justify-content-center">
                    @foreach($industry->benefits as $b)
                        <div class="col-md-6 col-lg-4 reveal d{{ $loop->index % 3 }}"><div class="card-mk text-center"><div class="icon-box mx-auto"><i class="fa-solid fa-circle-check"></i></div><h3>{{ $b['title'] }}</h3>@if($b['text'])<p class="mb-0">{{ $b['text'] }}</p>@endif</div></div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if($industry->faqs->isNotEmpty())
        <section class="section">
            <div class="container" style="max-width:900px">
                <div class="text-center mb-5 reveal"><span class="eyebrow">FAQs</span><h2 class="section-title">{{ $industry->title }} – FAQs</h2></div>
                @include('front.partials.faq-list', ['faqs' => $industry->faqs])
            </div>
        </section>
    @endif

    @if($related->isNotEmpty())
        <section class="section bg-light-mk">
            <div class="container">
                <div class="text-center mb-5 reveal"><span class="eyebrow">Explore More</span><h2 class="section-title">Related Industries</h2></div>
                <div class="row g-4 justify-content-center">
                    @foreach($related as $in)
                        <div class="col-sm-6 col-lg-4 reveal"><a href="{{ $in->url }}" class="industry-card"><i class="fa-solid {{ $in->icon }} ph-icon"></i><div class="content"><div class="ic"><i class="fa-solid {{ $in->icon }}"></i></div><h3>{{ $in->title }}</h3><p>{{ $in->short_description }}</p><span class="go">Explore Industry <i class="fa-solid fa-arrow-right ms-1"></i></span></div></a></div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="section">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-5 reveal">
                    <span class="eyebrow">Get Started</span>
                    <h2 class="section-title">Discuss Your {{ $industry->title }} Requirement</h2>
                    <p class="section-lead">Share a few details and our team will contact you to plan the right team for your site.</p>
                </div>
                <div class="col-lg-7 reveal d2">@include('front.partials.enquiry-form')</div>
            </div>
        </div>
    </section>

    @if(setting_on('cta_enabled'))@include('front.partials.cta')@endif
@endsection
