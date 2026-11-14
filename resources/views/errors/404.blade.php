@extends('layouts.app')
@php($seo = \App\Services\SeoService::build(null, ['title' => 'Page Not Found']))
@php($seo['robots'] = 'noindex, follow')

@section('content')
<section class="section bg-light-mk" style="min-height:70vh;display:flex;align-items:center">
    <div class="container text-center">
        <div class="err-code">404</div>
        <h1 class="h2 mb-3">Page Not Found</h1>
        <p class="section-lead mx-auto mb-4">Sorry, the page you are looking for does not exist, has been moved or is temporarily unavailable.</p>
        <div class="d-flex flex-wrap gap-3 justify-content-center">
            <a href="{{ url('/') }}" class="btn btn-blue"><i class="fa-solid fa-house"></i> Go to Homepage</a>
            <a href="{{ url('contact-us') }}" class="btn btn-outline-blue"><i class="fa-solid fa-envelope"></i> Contact Us</a>
            <a href="{{ url('contact-us') }}#enquiry" class="btn btn-gold"><i class="fa-solid fa-file-signature"></i> Request a Quote</a>
        </div>
    </div>
</section>
@endsection
