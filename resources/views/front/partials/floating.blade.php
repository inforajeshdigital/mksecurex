@php
    $wa = setting_on('whatsapp_enabled') ? wa_link() : null;
    $call = setting_on('call_enabled') && setting('call_number') ? setting('call_number') : null;
    $vis = function ($prefix) {
        $d = setting_on($prefix.'_desktop'); $m = setting_on($prefix.'_mobile');
        return (! $d ? 'hide-desktop ' : '').(! $m ? 'hide-mobile' : '');
    };
@endphp
@if($wa || $call)
<div class="float-actions pos-{{ setting('floating_position', 'right') }}">
    @if($call)
        <a href="{{ tel_link($call) }}" class="float-btn call {{ $vis('call') }}" aria-label="Call us"><i class="fa-solid fa-phone"></i><span class="tip">Call {{ $call }}</span></a>
    @endif
    @if($wa)
        <a href="{{ $wa }}" class="float-btn wa {{ $vis('whatsapp') }}" target="_blank" rel="noopener" aria-label="Chat on WhatsApp"><i class="fa-brands fa-whatsapp"></i><span class="tip">Chat on WhatsApp</span></a>
    @endif
</div>
@endif
<button class="back-top" aria-label="Back to top"><i class="fa-solid fa-arrow-up"></i></button>
