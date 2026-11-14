{{-- Generic repeater. Vars: $name (input name), $items (array), $fields ([key => ['label'=>..,'type'=>'text|textarea|icon']]), $label, $addLabel --}}
@php($items = $items ?? [])
<div class="mb-3" data-repeater>
    @if(!empty($label))<label class="form-label d-block">{{ $label }}</label>@endif
    <div data-repeater-list>
        @foreach($items as $i => $row)
            @include('admin.partials.repeater-row', ['index' => $i, 'row' => $row])
        @endforeach
    </div>
    <template>@include('admin.partials.repeater-row', ['index' => '__INDEX__', 'row' => []])</template>
    <button type="button" class="btn btn-sm btn-soft" data-repeater-add><i class="fa-solid fa-plus me-1"></i> {{ $addLabel ?? 'Add item' }}</button>
</div>
