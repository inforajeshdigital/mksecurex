@if(setting_on('topbar_enabled'))
<div class="topbar d-none d-md-block">
    <div class="container d-flex justify-content-between align-items-center gap-3">
        <div class="text-truncate"><i class="fa-solid fa-shield-halved me-2"></i>{{ setting('topbar_text') }}</div>
        <div class="d-flex gap-4 flex-shrink-0">
            @if(setting('contact_hours'))<span><i class="fa-regular fa-clock me-1"></i> {{ setting('contact_hours') }}</span>@endif
            @if(setting('contact_email'))<a href="mailto:{{ setting('contact_email') }}"><i class="fa-regular fa-envelope me-1"></i> {{ setting('contact_email') }}</a>@endif
            @if(setting('contact_phone'))<a href="{{ tel_link(setting('contact_phone')) }}"><i class="fa-solid fa-phone me-1"></i> {{ setting('contact_phone') }}</a>@endif
        </div>
    </div>
</div>
@endif

<header class="site-header {{ setting_on('header_sticky') ? 'is-sticky' : '' }}">
    <nav class="navbar navbar-expand-xl">
        <div class="container">
            <a class="navbar-brand brand" href="{{ url('/') }}" aria-label="{{ setting('site_name') }} home">
                <img src="{{ img_url(setting('site_logo')) }}" alt="{{ setting('site_logo_alt', setting('site_name')) }}" width="160" height="62">
            </a>
            <div class="d-flex align-items-center gap-2 d-xl-none">
                @if(setting_on('header_cta_enabled'))
                    <a href="{{ link_url(setting('header_cta_url')) }}" class="btn btn-gold btn-sm d-none d-sm-inline-flex px-3" @if(setting_on('header_cta_new_tab')) target="_blank" rel="noopener" @endif>{{ setting('header_cta_text') }}</a>
                @endif
                <button class="navbar-toggler" type="button" data-menu-open aria-label="Open menu" aria-controls="mainNav">
                    <span class="bar"></span><span class="bar"></span><span class="bar"></span>
                </button>
            </div>

            <div class="collapse navbar-collapse" id="mainNav">
                <button class="btn btn-light rounded-circle menu-close d-xl-none" data-menu-close aria-label="Close menu" style="width:44px;height:44px;padding:0"><i class="fa-solid fa-xmark"></i></button>
                <ul class="navbar-nav mx-auto align-items-xl-center">
                    @foreach($headerMenu as $item)
                        @php($isActive = rtrim(request()->url(), '/') === rtrim($item->href, '/') || ($item->url && $item->url !== '/' && request()->is(trim($item->url, '/').'*')))
                        @if($item->type === 'services' || $item->type === 'industries')
                            @php($list = $item->type === 'services' ? $navServices : $navIndustries)
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ $isActive ? 'active' : '' }}" href="{{ $item->href }}" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">{{ $item->label }}</a>
                                <div class="dropdown-menu mega-menu">
                                    <div class="row g-1">
                                        @foreach($list as $l)
                                            <div class="col-md-6"><a class="dropdown-item" href="{{ $l->url }}"><i class="fa-solid {{ $l->icon ?: 'fa-circle-check' }}"></i>{{ $l->title }}</a></div>
                                        @endforeach
                                    </div>
                                    <a href="{{ $item->href }}" class="view-all">View all {{ strtolower($item->label) }} <i class="fa-solid fa-arrow-right ms-1"></i></a>
                                </div>
                            </li>
                        @elseif($item->children->isNotEmpty())
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ $isActive ? 'active' : '' }}" href="{{ $item->href }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">{{ $item->label }}</a>
                                <div class="dropdown-menu mega-menu" style="min-width:260px">
                                    @foreach($item->children as $c)
                                        <a class="dropdown-item" href="{{ $c->href }}" @if($c->new_tab) target="_blank" rel="noopener" @endif><i class="fa-solid fa-angle-right"></i>{{ $c->label }}</a>
                                    @endforeach
                                </div>
                            </li>
                        @else
                            <li class="nav-item"><a class="nav-link {{ $isActive ? 'active' : '' }}" href="{{ $item->href }}" @if($item->new_tab) target="_blank" rel="noopener" @endif>{{ $item->label }}</a></li>
                        @endif
                    @endforeach
                </ul>
                @if(setting_on('header_cta_enabled'))
                    <a href="{{ link_url(setting('header_cta_url')) }}" class="btn btn-gold mt-3 mt-xl-0" @if(setting_on('header_cta_new_tab')) target="_blank" rel="noopener" @endif><i class="fa-solid fa-file-signature"></i> {{ setting('header_cta_text') }}</a>
                @endif
                <div class="d-xl-none mt-4 pt-3 border-top small">
                    @if(setting('contact_phone'))<p class="mb-2"><i class="fa-solid fa-phone text-accent me-2"></i><a href="{{ tel_link(setting('contact_phone')) }}">{{ setting('contact_phone') }}</a></p>@endif
                    @if(setting('contact_email'))<p class="mb-0"><i class="fa-regular fa-envelope text-accent me-2"></i><a href="mailto:{{ setting('contact_email') }}">{{ setting('contact_email') }}</a></p>@endif
                </div>
            </div>
        </div>
    </nav>
</header>
<div class="menu-backdrop d-xl-none"></div>
