<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5 reveal">
                @if($s->get('badge'))<span class="eyebrow">{{ $s->get('badge') }}</span>@endif
                @if($s->get('heading'))<h2 class="section-title">{{ $s->get('heading') }}</h2>@endif
                @if($s->get('subheading'))<p class="section-lead mb-4">{{ $s->get('subheading') }}</p>@endif
                @if(setting('contact_address'))<div class="contact-item"><i class="fa-solid fa-location-dot"></i><div><h4>Office Address</h4><p>{!! nl2br(e(setting('contact_address'))) !!}</p></div></div>@endif
                @if(setting('contact_phone'))<div class="contact-item"><i class="fa-solid fa-phone"></i><div><h4>Phone</h4><a href="{{ tel_link(setting('contact_phone')) }}">{{ setting('contact_phone') }}</a>@if(setting('contact_phone_alt'))<br><a href="{{ tel_link(setting('contact_phone_alt')) }}">{{ setting('contact_phone_alt') }}</a>@endif</div></div>@endif
                @if(setting('contact_email'))<div class="contact-item"><i class="fa-regular fa-envelope"></i><div><h4>Email</h4><a href="mailto:{{ setting('contact_email') }}">{{ setting('contact_email') }}</a></div></div>@endif
                @if(setting('contact_hours'))<div class="contact-item"><i class="fa-regular fa-clock"></i><div><h4>Business Hours</h4><p>{{ setting('contact_hours') }}</p></div></div>@endif
                @if(wa_link())<a href="{{ wa_link() }}" target="_blank" rel="noopener" class="btn btn-success rounded-pill mt-2"><i class="fa-brands fa-whatsapp"></i> Chat on WhatsApp</a>@endif
            </div>
            <div class="col-lg-7 reveal d2">
                @include('front.partials.enquiry-form', ['formType' => 'contact', 'title' => 'Send Us Your Enquiry', 'subtitle' => 'Fields marked * are required.', 'selected' => request('service')])
            </div>
        </div>
        @if(setting('contact_map') && str_starts_with(setting('contact_map'), 'https://'))
            <div class="mt-5 reveal"><iframe class="map-frame" src="{{ setting('contact_map') }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Office location map" allowfullscreen></iframe></div>
        @endif
    </div>
</section>
