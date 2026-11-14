@extends('layouts.app')

@php
    $types = $sections->pluck('type');
    $hasHero = $types->contains('hero_slider') || $types->contains('page_hero');
    $hasCta = $types->contains('cta') || $types->contains('enquiry_form') || $types->contains('contact_info');
    $schemaFaqs = $types->contains('faqs') ? \App\Models\Faq::whereNull('faqable_type')->where('is_active', true)->whereIn('category', $sections->where('type', 'faqs')->map(fn ($s) => $s->get('category', 'general'))->all())->get() : collect();
@endphp

@section('content')
    @unless($hasHero)
        @include('front.partials.page-hero', ['title' => $page->title, 'subtitle' => $page->excerpt, 'image' => $page->banner_image, 'imageAlt' => $page->banner_alt, 'breadcrumbs' => $breadcrumbs])
    @endunless

    @foreach($sections as $section)
        <div id="section-{{ $section->id }}" class="{{ !empty($preview) && !$section->is_active ? 'section-disabled' : '' }}">
            @includeIf('front.sections.'.$section->type, ['s' => $section])
        </div>
    @endforeach

    @if(!$isHome && !$hasCta && setting_on('cta_enabled'))
        @include('front.partials.cta')
    @endif
@endsection
