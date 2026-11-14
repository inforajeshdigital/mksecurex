<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php($seo = $seo ?? \App\Services\SeoService::build(null, ['title' => trim($__env->yieldContent('title')) ?: setting('site_name')]))
    @include('front.partials.seo', ['seo' => $seo])
    <link rel="icon" href="{{ img_url(setting('site_favicon')) }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/site.css') }}?v={{ filemtime(public_path('assets/css/site.css')) }}" rel="stylesheet">
    <style>
        :root {
            --mk-primary: {{ setting('primary_color') }};
            --mk-accent: {{ setting('accent_color') }};
            --mk-dark: {{ setting('dark_color') }};
            --header-bg: {{ setting('header_bg') }};
            --footer-bg: {{ setting('footer_bg') }};
            --cta-bg: {{ setting('cta_bg') }};
            --cta-btn: {{ setting('cta_btn_color') }};
        }
    </style>
    @stack('head')
    {!! setting('head_scripts') !!}
</head>
<body>
@if(!empty($preview))
    <div class="preview-bar"><i class="fa-solid fa-eye me-1"></i> Preview mode — disabled sections are outlined in red and are not visible to visitors. <a href="{{ url()->previous() }}" class="ms-2 text-dark text-decoration-underline">Back to admin</a></div>
@endif
<a href="#main" class="visually-hidden-focusable position-absolute p-2 bg-white">Skip to content</a>

@include('front.partials.header')

<main id="main">
    @yield('content')
</main>

@include('front.partials.footer')
@include('front.partials.floating')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="{{ asset('assets/js/site.js') }}?v={{ filemtime(public_path('assets/js/site.js')) }}" defer></script>
@stack('scripts')
{!! setting('body_scripts') !!}
</body>
</html>
