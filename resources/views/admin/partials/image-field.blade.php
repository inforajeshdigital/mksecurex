{{-- Vars: $name, $label, $value, $help --}}
<div class="mb-3">
    <label class="form-label">{{ $label ?? 'Image' }}</label>
    <div class="img-field">
        <div class="preview">@if(!empty($value))<img src="{{ img_url($value) }}" alt="">@else<i class="fa-regular fa-image"></i>@endif</div>
        <input type="hidden" name="{{ $name }}_path" value="">
        <div class="actions">
            <label class="btn btn-sm btn-soft mb-0"><i class="fa-solid fa-upload me-1"></i> Upload<input type="file" name="{{ $name }}" accept=".jpg,.jpeg,.png,.webp,.gif,.ico" hidden></label>
            @if(admin_can('media'))<button type="button" class="btn btn-sm btn-soft" data-media-pick><i class="fa-solid fa-photo-film me-1"></i> Library</button>@endif
            @if(!empty($value))
                <div class="form-check ms-auto mb-0"><input class="form-check-input" type="checkbox" name="{{ $name }}_remove" value="1" id="rm_{{ $name }}"><label class="form-check-label small" for="rm_{{ $name }}">Remove</label></div>
            @endif
        </div>
        <div class="form-text">{{ $help ?? 'JPG, PNG, WebP or GIF up to 4 MB. JPG/PNG are auto-converted to WebP.' }}</div>
    </div>
</div>
