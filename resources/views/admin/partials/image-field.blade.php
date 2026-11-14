{{-- Vars: $name, $label, $value, $help, $size ("1920x600"), $sizeNote (where/how it is shown) --}}
@php
    $sz = $size ?? null;
    [$rw, $rh] = $sz ? array_map('intval', explode('x', strtolower($sz))) + [0, 0] : [0, 0];
@endphp
<div class="mb-3">
    <label class="form-label">{{ $label ?? 'Image' }}</label>
    <div class="img-field" @if($rw) data-rec-w="{{ $rw }}" data-rec-h="{{ $rh }}" @endif>
        @if($rw)
            <div class="size-hint"><i class="fa-solid fa-ruler-combined"></i> Recommended size: <strong>{{ $rw }} × {{ $rh }} px</strong>@if(!empty($sizeNote))<span> · {{ $sizeNote }}</span>@endif</div>
        @endif
        <div class="preview">@if(!empty($value))<img src="{{ img_url($value) }}" alt="">@else<i class="fa-regular fa-image"></i>@endif</div>
        <div class="size-check small"></div>
        <input type="hidden" name="{{ $name }}_path" value="">
        <div class="actions">
            <label class="btn btn-sm btn-soft mb-0"><i class="fa-solid fa-upload me-1"></i> Upload<input type="file" name="{{ $name }}" accept=".jpg,.jpeg,.png,.webp,.gif,.ico" hidden></label>
            @if(admin_can('media'))<button type="button" class="btn btn-sm btn-soft" data-media-pick><i class="fa-solid fa-photo-film me-1"></i> Library</button>@endif
            @if(!empty($value))
                <div class="form-check ms-auto mb-0"><input class="form-check-input" type="checkbox" name="{{ $name }}_remove" value="1" id="rm_{{ $name }}"><label class="form-check-label small" for="rm_{{ $name }}">Remove</label></div>
            @endif
        </div>
        <div class="form-text">{{ $help ?? 'JPG, PNG or WebP · max 4 MB · JPG/PNG auto-converted to WebP.' }}</div>
    </div>
</div>
