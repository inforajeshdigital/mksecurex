<section class="section @include('front.sections._bg')">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-5 enquiry-side reveal">
                @if($s->get('badge'))<span class="eyebrow">{{ $s->get('badge') }}</span>@endif
                @if($s->get('heading'))<h2 class="section-title">{{ $s->get('heading') }}</h2>@endif
                @if($s->get('subheading'))<p class="section-lead">{{ $s->get('subheading') }}</p>@endif
                @if(count($s->items()))
                    <ul class="point-list mt-4">
                        @foreach($s->items() as $it)
                            <li><span class="pi"><i class="fa-solid {{ $it['icon'] ?: 'fa-check' }}"></i></span><div><h4>{{ $it['title'] }}</h4>@if($it['text'])<p>{{ $it['text'] }}</p>@endif</div></li>
                        @endforeach
                    </ul>
                @endif
                <div class="d-flex flex-wrap gap-3 mt-3">
                    @if(setting('contact_phone'))<a href="{{ tel_link(setting('contact_phone')) }}" class="btn btn-outline-blue"><i class="fa-solid fa-phone"></i> {{ setting('contact_phone') }}</a>@endif
                    @if(wa_link())<a href="{{ wa_link() }}" target="_blank" rel="noopener" class="btn btn-success rounded-pill"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>@endif
                </div>
            </div>
            <div class="col-lg-7 reveal d2">
                @include('front.partials.enquiry-form', ['formType' => 'enquiry', 'selected' => request('service')])
            </div>
        </div>
    </div>
</section>
