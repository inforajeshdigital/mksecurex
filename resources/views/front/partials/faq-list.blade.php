@php($fid = 'faq'.substr(md5(uniqid('', true)), 0, 6))
<div class="accordion faq-mk" id="{{ $fid }}">
    @foreach($faqs as $f)
        <div class="accordion-item reveal">
            <h3 class="accordion-header">
                <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $fid }}-{{ $loop->index }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}">{{ $f->question }}</button>
            </h3>
            <div id="{{ $fid }}-{{ $loop->index }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#{{ $fid }}">
                <div class="accordion-body">{!! nl2br(e($f->answer)) !!}</div>
            </div>
        </div>
    @endforeach
</div>
