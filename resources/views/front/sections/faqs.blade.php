@php($faqs = \App\Models\Faq::whereNull('faqable_type')->where('is_active', true)->where('category', $s->get('category', 'general'))->orderBy('sort_order')->get())
@if($faqs->isNotEmpty())
<section class="section @include('front.sections._bg')">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-4">
                <div class="sticky-side reveal">
                    @if($s->get('badge'))<span class="eyebrow">{{ $s->get('badge') }}</span>@endif
                    @if($s->get('heading'))<h2 class="section-title">{{ $s->get('heading') }}</h2>@endif
                    @if($s->get('subheading'))<p class="section-lead">{{ $s->get('subheading') }}</p>@endif
                    <div class="side-card mt-4">
                        <h4>Still have questions?</h4>
                        <p class="small">Talk to our team for a quick answer about your requirement.</p>
                        <a href="{{ url('contact-us') }}" class="btn btn-gold btn-sm">Contact Our Team</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">@include('front.partials.faq-list', ['faqs' => $faqs])</div>
        </div>
    </div>
</section>
@endif
