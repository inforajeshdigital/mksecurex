@extends('layouts.admin')
@php($def = config("sections.types.{$section->type}"))
@section('title', ($section->exists ? 'Edit' : 'Add').' Section: '.$def['label'])
@section('crumbs', $page->title.' › Sections')

@section('content')
@php($d = fn ($k, $default = '') => old("data.$k", $section->data[$k] ?? $default))
@php($has = fn ($f) => in_array($f, $def['fields']))
<form action="{{ $section->exists ? route('admin.sections.update', $section) : route('admin.sections.store', $page) }}" method="POST" enctype="multipart/form-data">
    @csrf @if($section->exists) @method('PUT') @endif
    <input type="hidden" name="type" value="{{ $section->type }}">
    <div class="row g-4">
        <div class="col-xl-8">
            <div class="a-card">
                <div class="a-card-head"><h2><i class="fa-solid {{ $def['icon'] }} text-primary me-2"></i>{{ $def['label'] }}</h2></div>
                <div class="a-card-body">
                    @if(!empty($def['help']))<div class="alert alert-info small"><i class="fa-solid fa-circle-info me-1"></i>{{ $def['help'] }} @if($section->type === 'hero_slider')<a href="{{ route('admin.homepage') }}#slides">Manage slides</a>@endif</div>@endif
                    <div class="row g-3">
                        @if($has('badge'))<div class="col-md-4"><label class="form-label">Badge / Eyebrow</label><input type="text" name="data[badge]" class="form-control" value="{{ $d('badge') }}"></div>@endif
                        @if($has('heading'))<div class="{{ $has('badge') ? 'col-md-8' : 'col-12' }}"><label class="form-label">Heading</label><input type="text" name="data[heading]" class="form-control" value="{{ $d('heading') }}"></div>@endif
                        @if($has('subheading'))<div class="col-12"><label class="form-label">Sub-heading / Description</label><textarea name="data[subheading]" class="form-control" rows="2">{{ $d('subheading') }}</textarea></div>@endif
                        @if($has('content'))<div class="col-12"><label class="form-label">Content</label><textarea name="data[content]" class="form-control rich">{{ $d('content') }}</textarea></div>@endif
                        @if($has('html'))<div class="col-12"><label class="form-label">Custom HTML</label><textarea name="data[html]" class="form-control code-area" rows="12" spellcheck="false">{{ $d('html') }}</textarea></div>@endif
                        @if($has('button_text'))
                            <div class="col-md-6"><label class="form-label">Button Text</label><input type="text" name="data[button_text]" class="form-control" value="{{ $d('button_text') }}"></div>
                            <div class="col-md-6"><label class="form-label">Button URL</label><input type="text" name="data[button_url]" class="form-control" value="{{ $d('button_url') }}" placeholder="/contact-us"></div>
                        @endif
                        @if($has('button2_text'))
                            <div class="col-md-6"><label class="form-label">Second Button Text</label><input type="text" name="data[button2_text]" class="form-control" value="{{ $d('button2_text') }}"></div>
                            <div class="col-md-6"><label class="form-label">Second Button URL</label><input type="text" name="data[button2_url]" class="form-control" value="{{ $d('button2_url') }}"></div>
                        @endif
                    </div>

                    @if(!empty($def['items']))
                        @php($labels = ['icon' => ['label' => 'Icon (Font Awesome class)', 'type' => 'icon', 'col' => 'col-md-4'], 'title' => ['label' => 'Title', 'type' => 'text', 'col' => 'col-md-8'], 'text' => ['label' => 'Text', 'type' => 'textarea'], 'value' => ['label' => 'Value (number)', 'type' => 'text', 'col' => 'col-md-4'], 'suffix' => ['label' => 'Suffix (+, %, K)', 'type' => 'text', 'col' => 'col-md-4'], 'image' => ['label' => 'Image', 'type' => 'image', 'col' => 'col-md-4']])
                        @php($fields = collect($def['items'])->mapWithKeys(fn ($k) => [$k => $labels[$k]])->all())
                        @if(isset($fields['value'])) @php($fields['title']['label'] = 'Label') @php($fields['title']['col'] = 'col-md-4') @endif
                        <hr class="my-4">
                        @include('admin.partials.repeater', ['name' => 'items', 'items' => old('items', $section->data['items'] ?? []), 'fields' => $fields, 'label' => 'Items / Cards', 'addLabel' => 'Add item'])
                        @if(in_array('icon', $def['items']))<div class="form-text">Find icons at <a href="https://fontawesome.com/search?o=r&m=free&s=solid" target="_blank" rel="noopener">fontawesome.com</a> (free solid). Use the class like <code>fa-shield-halved</code>.</div>@endif
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="a-card">
                <div class="a-card-head"><h2>Section Settings</h2></div>
                <div class="a-card-body">
                    <div class="mb-3"><label class="form-label">Internal Name <small class="text-muted">(optional)</small></label><input type="text" name="name" class="form-control" value="{{ old('name', $section->name) }}" placeholder="{{ $def['label'] }}"></div>
                    <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="act" @checked(old('is_active', $section->is_active))><label class="form-check-label fw-semibold" for="act">Enabled (visible on website)</label></div>
                    @if($has('background'))
                        <div class="mb-3"><label class="form-label">Background</label><select name="data[background]" class="form-select">@foreach(config('sections.backgrounds') as $k => $l)<option value="{{ $k }}" @selected($d('background', 'white') === $k)>{{ $l }}</option>@endforeach</select></div>
                    @endif
                    @if($has('layout'))
                        <div class="mb-3"><label class="form-label">Image Position</label><select name="data[layout]" class="form-select"><option value="right" @selected($d('layout', 'right') === 'right')>Image right</option><option value="left" @selected($d('layout') === 'left')>Image left</option></select></div>
                    @endif
                    @if($has('columns'))
                        <div class="mb-3"><label class="form-label">Columns</label><select name="data[columns]" class="form-select">@foreach(['2', '3', '4'] as $c)<option @selected($d('columns', '3') == $c)>{{ $c }}</option>@endforeach</select></div>
                    @endif
                    @if($has('limit'))
                        <div class="mb-3"><label class="form-label">Max items <small class="text-muted">(empty = all)</small></label><input type="number" min="1" max="50" name="data[limit]" class="form-control" value="{{ $d('limit') }}"></div>
                    @endif
                    @if($has('category'))
                        <div class="mb-3"><label class="form-label">FAQ Category</label><select name="data[category]" class="form-select">@foreach(\App\Http\Controllers\Admin\FaqController::CATEGORIES as $k => $l)<option value="{{ $k }}" @selected($d('category', 'general') === $k)>{{ $l }}</option>@endforeach</select>
                            <div class="form-text"><a href="{{ route('admin.faqs.index') }}">Manage FAQs</a></div></div>
                    @endif
                    @if($has('image'))
                        @include('admin.partials.image-field', ['name' => 'section_image', 'label' => 'Image', 'value' => $section->data['image'] ?? null, 'size' => $section->type === 'page_hero' ? '1920x600' : '1200x1000', 'sizeNote' => $section->type === 'page_hero' ? 'wide banner background' : 'shown beside the text'])
                    @endif
                    @if($has('image_alt'))
                        <div class="mb-3"><label class="form-label">Image Alt Text</label><input type="text" name="data[image_alt]" class="form-control" value="{{ $d('image_alt') }}"></div>
                    @endif
                    @if($section->type === 'service_cards')<div class="form-text"><a href="{{ route('admin.services.index') }}">Manage services & their order</a></div>@endif
                    @if($section->type === 'industry_cards')<div class="form-text"><a href="{{ route('admin.industries.index') }}">Manage industries & their order</a></div>@endif
                    @if($section->type === 'testimonials')<div class="form-text"><a href="{{ route('admin.testimonials.index') }}">Manage testimonials</a></div>@endif
                    @if($section->type === 'contact_info')<div class="form-text"><a href="{{ route('admin.settings', 'contact') }}">Edit contact details & map</a></div>@endif
                </div>
            </div>
            <div class="d-grid gap-2">
                <button class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Save Section</button>
                @if($section->exists)<button class="btn btn-soft" name="after" value="stay"><i class="fa-solid fa-check me-1"></i> Save & Continue Editing</button>@endif
                <a href="{{ $page->slug === 'home' ? route('admin.homepage') : route('admin.pages.edit', $page) }}" class="btn btn-light">Cancel</a>
            </div>
        </div>
    </div>
</form>
@endsection
