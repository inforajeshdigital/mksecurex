<div class="repeater-row">
    <div class="rr-tools">
        <span class="btn btn-sm btn-light rr-drag" title="Drag to reorder" style="cursor:grab"><i class="fa-solid fa-grip-vertical"></i></span>
        <button type="button" class="btn btn-sm btn-soft-danger" data-repeater-remove title="Remove"><i class="fa-solid fa-trash"></i></button>
    </div>
    <div class="rr-num">Item {{ is_numeric($index) ? $index + 1 : '' }}</div>
    <div class="row g-2">
        @foreach($fields as $key => $f)
            @php($val = $row[$key] ?? '')
            @php($col = $f['col'] ?? ($f['type'] === 'textarea' ? 'col-12' : 'col-md-6'))
            <div class="{{ $col }}">
                <label class="form-label small mb-1">{{ $f['label'] }}</label>
                @if($f['type'] === 'textarea')
                    <textarea name="{{ $name }}[{{ $index }}][{{ $key }}]" class="form-control form-control-sm" rows="2">{{ $val }}</textarea>
                @elseif($f['type'] === 'image')
                    <div class="img-field p-2" data-rec-w="1200" data-rec-h="900">
                        <div class="size-hint py-1"><i class="fa-solid fa-ruler-combined"></i> 1200 × 900 px</div>
                        <div class="preview" style="height:80px">@if($val)<img src="{{ img_url($val) }}" alt="">@else<i class="fa-regular fa-image"></i>@endif</div>
                        <input type="hidden" name="{{ $name }}[{{ $index }}][image_current]" value="{{ $val }}">
                        <input type="hidden" name="item_image_{{ $index }}_path" value="">
                        <div class="actions">
                            <label class="btn btn-sm btn-soft mb-0"><i class="fa-solid fa-upload"></i><input type="file" name="item_image_{{ $index }}" accept="image/*" hidden></label>
                            @if(admin_can('media'))<button type="button" class="btn btn-sm btn-soft" data-media-pick><i class="fa-solid fa-photo-film"></i></button>@endif
                        </div>
                    </div>
                @else
                    <div class="input-group input-group-sm">
                        @if($f['type'] === 'icon')<span class="input-group-text"><i class="fa-solid {{ $val ?: 'fa-icons' }}"></i></span>@endif
                        <input type="text" name="{{ $name }}[{{ $index }}][{{ $key }}]" value="{{ $val }}" class="form-control" @if($f['type'] === 'icon') placeholder="e.g. fa-shield-halved" @endif>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
