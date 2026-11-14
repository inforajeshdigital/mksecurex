@extends('layouts.app')

@section('content')
    @include('front.partials.page-hero', [
        'title' => $page->title ?? 'Industries We Serve',
        'subtitle' => $page->excerpt ?? null,
        'badge' => 'Industries',
        'image' => $page?->hero_image,
        'imageAlt' => $page->banner_alt ?? null,
        'breadcrumbs' => $breadcrumbs,
    ])

    <section class="section">
        <div class="container">
            <div class="text-center mx-auto mb-5 reveal" style="max-width:760px">
                <span class="eyebrow">Sector Expertise</span>
                <h2 class="section-title">Support Planned Around How Your Industry Works</h2>
                <p class="section-lead">Each industry has its own roles, shift patterns, risks and site conditions. Explore how we support yours.</p>
            </div>
            @include('front.partials.industry-grid', ['industries' => $industries])
        </div>
    </section>

    <section class="section bg-light-mk">
        <div class="container">
            <div class="row g-4">
                @foreach($industries as $in)
                    <div class="col-md-6 reveal d{{ $loop->index % 2 }}">
                        <div class="card-mk">
                            <div class="d-flex gap-3 align-items-start">
                                <div class="icon-box m-0" style="flex:0 0 64px"><i class="fa-solid {{ $in->icon ?: 'fa-building' }}"></i></div>
                                <div>
                                    <h3><a href="{{ $in->url }}" class="text-reset">{{ $in->title }}</a></h3>
                                    <p>{{ $in->short_description }}</p>
                                    <div>@foreach($in->services as $sv)<a href="{{ $sv->url }}" class="tag">{{ $sv->title }}</a>@endforeach</div>
                                    <a href="{{ $in->url }}" class="btn-link-mk mt-2">Explore Industry <i class="fa-solid fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @if(setting_on('cta_enabled'))@include('front.partials.cta')@endif
@endsection
