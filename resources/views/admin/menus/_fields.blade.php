<div class="mb-3"><label class="form-label">Label *</label><input type="text" name="label" class="form-control" value="{{ $item->label }}" required></div>
<div class="mb-3"><label class="form-label">URL</label><input type="text" name="url" class="form-control" value="{{ $item->url }}" placeholder="/about-us or https://…"></div>
<div class="row g-2 mb-3">
    <div class="col-md-6"><label class="form-label">Type</label><select name="type" class="form-select">@foreach(\App\Models\NavigationItem::TYPES as $k => $l)<option value="{{ $k }}" @selected($item->type === $k)>{{ $l }}</option>@endforeach</select></div>
    <div class="col-md-6"><label class="form-label">Parent</label><select name="parent_id" class="form-select"><option value="">— Top level —</option>@foreach($menu->items as $p)@if($p->id !== $item->id)<option value="{{ $p->id }}" @selected($item->parent_id == $p->id)>{{ $p->label }}</option>@endif @endforeach</select></div>
</div>
<div class="d-flex gap-4 mb-3">
    <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="new_tab" value="1" id="nt{{ $item->id }}" @checked($item->new_tab)><label class="form-check-label" for="nt{{ $item->id }}">Open in new tab</label></div>
    <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="ia{{ $item->id }}" @checked($item->is_active)><label class="form-check-label" for="ia{{ $item->id }}">Visible</label></div>
</div>
