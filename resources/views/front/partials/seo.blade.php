<title>{{ $seo['title'] }}</title>
<meta name="description" content="{{ $seo['description'] }}">
@if($seo['keywords'])<meta name="keywords" content="{{ $seo['keywords'] }}">@endif
<meta name="robots" content="{{ $seo['robots'] }}">
<link rel="canonical" href="{{ $seo['canonical'] }}">
<meta property="og:site_name" content="{{ setting('site_name') }}">
<meta property="og:title" content="{{ $seo['og']['title'] }}">
<meta property="og:description" content="{{ $seo['og']['description'] }}">
<meta property="og:url" content="{{ $seo['og']['url'] }}">
<meta property="og:type" content="{{ $seo['og']['type'] }}">
@if($seo['og']['image'])<meta property="og:image" content="{{ $seo['og']['image'] }}">@endif
<meta property="og:locale" content="en_IN">
<meta name="twitter:card" content="{{ $seo['twitter']['card'] }}">
<meta name="twitter:title" content="{{ $seo['twitter']['title'] }}">
<meta name="twitter:description" content="{{ $seo['twitter']['description'] }}">
@if($seo['twitter']['image'])<meta name="twitter:image" content="{{ $seo['twitter']['image'] }}">@endif
@php
    $schemas = [];
    if (setting_on('seo_schema_organization') && !empty($isHome)) {
        $sameAs = array_values(array_filter([setting('social_facebook'), setting('social_instagram'), setting('social_linkedin'), setting('social_twitter'), setting('social_youtube')]));
        $schemas[] = array_filter([
            '@context' => 'https://schema.org', '@type' => 'Organization', 'name' => setting('site_name'), 'url' => url('/'),
            'logo' => img_url(setting('site_logo')), 'email' => setting('contact_email'), 'telephone' => setting('contact_phone'), 'sameAs' => $sameAs ?: null,
        ]);
        $schemas[] = ['@context' => 'https://schema.org', '@type' => 'WebSite', 'name' => setting('site_name'), 'url' => url('/')];
    }
    if (setting_on('seo_schema_localbusiness') && !empty($isHome)) {
        $schemas[] = ['@context' => 'https://schema.org', '@type' => 'LocalBusiness', 'name' => setting('site_name'), 'url' => url('/'), 'image' => img_url(setting('site_logo')),
            'telephone' => setting('contact_phone'), 'email' => setting('contact_email'), 'address' => setting('contact_address'), 'openingHours' => setting('contact_hours')];
    }
    if (!empty($breadcrumbs)) {
        $list = [['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')]];
        foreach ($breadcrumbs as $i => $b) { $list[] = ['@type' => 'ListItem', 'position' => $i + 2, 'name' => $b['label'], 'item' => $b['url']]; }
        $schemas[] = ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $list];
    }
    if (!empty($service)) {
        $schemas[] = ['@context' => 'https://schema.org', '@type' => 'Service', 'name' => $service->title, 'description' => $service->short_description, 'url' => $service->url,
            'provider' => ['@type' => 'Organization', 'name' => setting('site_name'), 'url' => url('/')]];
    }
    if (setting_on('seo_schema_faq') && !empty($schemaFaqs) && count($schemaFaqs)) {
        $schemas[] = ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => collect($schemaFaqs)->map(fn ($f) => ['@type' => 'Question', 'name' => $f->question, 'acceptedAnswer' => ['@type' => 'Answer', 'text' => strip_tags($f->answer)]])->values()->all()];
    }
@endphp
@foreach($schemas as $schema)
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endforeach
@if(!empty($seo['schema']))
<script type="application/ld+json">{!! str_replace('</', '<\/', $seo['schema']) !!}</script>
@endif
