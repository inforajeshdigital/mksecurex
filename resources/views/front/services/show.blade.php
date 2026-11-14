@extends('layouts.app')
@php($schemaFaqs = $service->faqs)

@section('content')
    @include('front.partials.page-hero', [
        'title' => $service->title,
        'subtitle' => $service->short_description,
        'badge' => 'Our Services',
        'image' => $service->banner_image ?: $service->image,
        'imageAlt' => $service->image_alt,
        'breadcrumbs' => $breadcrumbs,
        'buttons' => [['text' => 'Enquire Now', 'url' => '#enquiry'], ['text' => 'Call Us', 'url' => tel_link(setting('contact_phone'))]],
    ])

    <section class="section">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-8">
                    @if($service->intro)<p class="detail-intro reveal">{{ $service->intro }}</p>@endif

                    @if($service->image)
                        <img src="{{ img_url($service->image) }}" alt="{{ $service->image_alt ?: $service->title }}" class="w-100 rounded-4 shadow my-4 reveal" style="aspect-ratio:16/8;object-fit:cover" loading="lazy">
                    @endif

                    <h2 class="h3 mt-4 reveal">Service Overview</h2>
                    <div class="rich-content reveal">{!! $service->description !!}</div>

                    @if(!empty($service->features))
                        <h2 class="h3 mt-5 mb-4 reveal">Key Features</h2>
                        <div class="row g-3">
                            @foreach($service->features as $f)
                                <div class="col-md-6 reveal d{{ $loop->index % 2 }}"><div class="mini-card"><span class="mi"><i class="fa-solid fa-check"></i></span><div><h4>{{ $f['title'] }}</h4>@if($f['text'])<p>{{ $f['text'] }}</p>@endif</div></div></div>
                            @endforeach
                        </div>
                    @endif

                    @if(!empty($service->benefits))
                        <h2 class="h3 mt-5 mb-4 reveal">Benefits for Your Business</h2>
                        <div class="row g-4">
                            @foreach($service->benefits as $b)
                                <div class="col-md-6 reveal"><div class="card-mk p-4"><div class="icon-box mb-3" style="width:52px;height:52px;font-size:1.2rem"><i class="fa-solid fa-award"></i></div><h3 class="h5">{{ $b['title'] }}</h3><p class="mb-0">{{ $b['text'] }}</p></div></div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="col-lg-4">
                    <div class="sticky-side">
                        <div class="mb-4">
                            <h3 class="h5 mb-3">All Services</h3>
                            <ul class="side-links">
                                @foreach($navServices as $sv)
                                    <li><a href="{{ $sv->url }}" class="{{ $sv->id === $service->id ? 'active' : '' }}">{{ $sv->title }} <i class="fa-solid fa-angle-right"></i></a></li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="side-card">
                            <h4>Need {{ $service->title }}?</h4>
                            <p class="small">Share your requirement and get a plan tailored to your site.</p>
                            <a href="#enquiry" class="btn btn-gold btn-sm mb-2">Request a Quote</a>
                            @if(setting('contact_phone'))<p class="mb-0 mt-2 small"><i class="fa-solid fa-phone text-accent me-2"></i><a class="text-white" href="{{ tel_link(setting('contact_phone')) }}">{{ setting('contact_phone') }}</a></p>@endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if(!empty($service->process))
        <section class="section bg-light-mk">
            <div class="container">
                <div class="text-center mb-5 reveal"><span class="eyebrow">Our Working Process</span><h2 class="section-title">How We Deliver {{ $service->title }}</h2></div>
                <div class="row g-5 process-wrap">
                    @foreach($service->process as $p)
                        <div class="col-sm-6 col-lg-3 reveal d{{ $loop->index }}"><div class="step"><div class="num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</div><h3>{{ $p['title'] }}</h3><p class="mb-0">{{ $p['text'] }}</p></div></div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if($service->industries->isNotEmpty())
        <section class="section">
            <div class="container">
                <div class="text-center mb-5 reveal"><span class="eyebrow">Industries</span><h2 class="section-title">Industries Where This Service Is Applicable</h2></div>
                @include('front.partials.industry-grid', ['industries' => $service->industries])
            </div>
        </section>
    @endif

    <section class="section bg-dark-section">
        <div class="dark-deco"></div>
        <div class="container position-relative">
            <div class="row g-4 align-items-center">
                <div class="col-lg-5 reveal">
                    <span class="eyebrow">Why Choose {{ setting('site_name') }}?</span>
                    <h2 class="section-title">A Partner Focused on Discipline & Accountability</h2>
                    <p class="section-lead">We plan around your requirement, deploy with clear instructions and stay engaged through supervision and reporting.</p>
                </div>
                <div class="col-lg-7">
                    <div class="row g-3">
                        @foreach([['fa-user-shield', 'Screened & briefed personnel'], ['fa-headset', 'Single point of contact'], ['fa-rotate', 'Quick replacements'], ['fa-file-contract', 'Transparent scope & reporting']] as [$ic, $tx])
                            <div class="col-sm-6 reveal d{{ $loop->index }}"><div class="card-mk p-4 d-flex gap-3 align-items-center"><div class="icon-box m-0" style="width:52px;height:52px;font-size:1.2rem;flex:0 0 52px"><i class="fa-solid {{ $ic }}"></i></div><h3 class="h6 m-0">{{ $tx }}</h3></div></div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if($service->faqs->isNotEmpty())
        <section class="section">
            <div class="container" style="max-width:900px">
                <div class="text-center mb-5 reveal"><span class="eyebrow">FAQs</span><h2 class="section-title">{{ $service->title }} – FAQs</h2></div>
                @include('front.partials.faq-list', ['faqs' => $service->faqs])
            </div>
        </section>
    @endif

    @if($related->isNotEmpty())
        <section class="section bg-light-mk">
            <div class="container">
                <div class="text-center mb-5 reveal"><span class="eyebrow">Explore More</span><h2 class="section-title">Related Services</h2></div>
                @include('front.partials.service-grid', ['services' => $related])
            </div>
        </section>
    @endif

    <section class="section">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-5 reveal">
                    <span class="eyebrow">Enquire Now</span>
                    <h2 class="section-title">Get a Plan for {{ $service->title }}</h2>
                    <p class="section-lead">Tell us your headcount, location and timing — our team will get back with a suitable proposal.</p>
                    <ul class="check-list mt-4"><li>Requirement-based planning</li><li>Clear scope of work</li><li>Supervised deployment</li></ul>
                </div>
                <div class="col-lg-7 reveal d2">@include('front.partials.enquiry-form', ['selected' => $service->id])</div>
            </div>
        </div>
    </section>

    @if(setting_on('cta_enabled'))@include('front.partials.cta')@endif
@endsection
