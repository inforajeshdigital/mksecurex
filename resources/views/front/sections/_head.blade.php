{{-- Section heading block. Vars: $s (section), $center (bool) --}}
@if($s->get('badge') || $s->get('heading') || $s->get('subheading'))
<div class="{{ ($center ?? true) ? 'text-center mx-auto' : '' }} mb-5 reveal" style="{{ ($center ?? true) ? 'max-width:780px' : '' }}">
    @if($s->get('badge'))<span class="eyebrow">{{ $s->get('badge') }}</span>@endif
    @if($s->get('heading'))<h2 class="section-title">{{ $s->get('heading') }}</h2>@endif
    @if($s->get('subheading'))<p class="section-lead mb-0">{{ $s->get('subheading') }}</p>@endif
</div>
@endif
