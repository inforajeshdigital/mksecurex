<footer class="site-footer">
    <div class="footer-top">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4 col-md-6">
                    <a href="{{ url('/') }}" class="footer-logo"><img src="{{ img_url(setting('footer_logo', setting('site_logo'))) }}" alt="{{ setting('site_logo_alt', setting('site_name')) }}" width="150" height="64" loading="lazy"></a>
                    <p>{{ setting('footer_about') }}</p>
                    <div class="socials">
                        @foreach(['facebook' => 'fa-facebook-f', 'instagram' => 'fa-instagram', 'linkedin' => 'fa-linkedin-in', 'twitter' => 'fa-x-twitter', 'youtube' => 'fa-youtube'] as $k => $icon)
                            @if(setting('social_'.$k))<a href="{{ setting('social_'.$k) }}" target="_blank" rel="noopener" aria-label="{{ ucfirst($k) }}"><i class="fa-brands {{ $icon }}"></i></a>@endif
                        @endforeach
                    </div>
                </div>
                @if(setting_on('footer_show_services') && $navServices->isNotEmpty())
                <div class="col-lg-3 col-md-6">
                    <h5>Our Services</h5>
                    <ul class="footer-links">
                        @foreach($navServices as $s)<li><a href="{{ $s->url }}">{{ $s->title }}</a></li>@endforeach
                    </ul>
                </div>
                @endif
                <div class="col-lg-2 col-md-6">
                    @if(setting_on('footer_show_industries') && $navIndustries->isNotEmpty())
                        <h5>Industries</h5>
                        <ul class="footer-links mb-4">
                            @foreach($navIndustries->take(8) as $i)<li><a href="{{ $i->url }}">{{ $i->title }}</a></li>@endforeach
                        </ul>
                    @endif
                </div>
                <div class="col-lg-3 col-md-6">
                    @if($footerQuick->isNotEmpty())
                        <h5>Quick Links</h5>
                        <ul class="footer-links mb-4">
                            @foreach($footerQuick as $l)<li><a href="{{ $l->href }}" @if($l->new_tab) target="_blank" rel="noopener" @endif>{{ $l->label }}</a></li>@endforeach
                        </ul>
                    @endif
                    <h5>Contact Us</h5>
                    <ul class="list-unstyled footer-contact">
                        @if(setting('contact_address'))<li><i class="fa-solid fa-location-dot"></i><span>{!! nl2br(e(setting('contact_address'))) !!}</span></li>@endif
                        @if(setting('contact_phone'))<li><i class="fa-solid fa-phone"></i><a href="{{ tel_link(setting('contact_phone')) }}">{{ setting('contact_phone') }}</a></li>@endif
                        @if(setting('contact_email'))<li><i class="fa-regular fa-envelope"></i><a href="mailto:{{ setting('contact_email') }}">{{ setting('contact_email') }}</a></li>@endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 text-center">
            <div>{{ str_replace('{year}', date('Y'), setting('footer_copyright')) }}</div>
            <div>@foreach($footerLegal as $l)<a href="{{ $l->href }}" @if($l->new_tab) target="_blank" rel="noopener" @endif>{{ $l->label }}</a>@endforeach</div>
        </div>
    </div>
</footer>
