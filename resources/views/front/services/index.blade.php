@extends('layouts.app')
@php($schemaFaqs = $faqs)

@section('content')
    @include('front.partials.page-hero', [
        'title' => $page->title ?? 'Our Services',
        'subtitle' => $page->excerpt ?? null,
        'badge' => 'What We Do',
        'image' => $page->banner_image ?? null,
        'imageAlt' => $page->banner_alt ?? null,
        'breadcrumbs' => $breadcrumbs,
    ])

    <section class="section bg-light-mk">
        <div class="container">
            <div class="text-center mx-auto mb-5 reveal" style="max-width:760px">
                <span class="eyebrow">Our Core Services</span>
                <h2 class="section-title">Security, Workforce & Facility Services</h2>
                <p class="section-lead">Choose a single service or combine several into one managed plan, scoped around your site, roles and schedule.</p>
            </div>
            @include('front.partials.service-grid', ['services' => $services])
        </div>
    </section>

    @if($faqs->isNotEmpty())
        <section class="section">
            <div class="container" style="max-width:900px">
                <div class="text-center mb-5 reveal"><span class="eyebrow">FAQs</span><h2 class="section-title">Questions About Our Services</h2></div>
                @include('front.partials.faq-list', ['faqs' => $faqs])
            </div>
        </section>
    @endif

    @if(setting_on('cta_enabled'))@include('front.partials.cta')@endif
@endsection
