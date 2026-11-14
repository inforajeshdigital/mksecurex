@extends('layouts.admin')
@section('title', $service->exists ? 'Edit Service: '.$service->title : 'Add Service')
@section('crumbs', 'Content › Services')

@section('content')
@php($tt = ['title' => ['label' => 'Title', 'type' => 'text', 'col' => 'col-12'], 'text' => ['label' => 'Description', 'type' => 'textarea']])
<form action="{{ $service->exists ? route('admin.services.update', $service) : route('admin.services.store') }}" method="POST" enctype="multipart/form-data">
    @csrf @if($service->exists) @method('PUT') @endif
    <div class="a-card">
        <ul class="nav nav-tabs-a px-3 pt-2">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#s-main" type="button">Content</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#s-features" type="button">Features & Benefits</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#s-process" type="button">Process</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#s-rel" type="button">Industries & Related</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#s-faq" type="button">FAQs</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#s-seo" type="button"><i class="fa-solid fa-magnifying-glass-chart me-1"></i>SEO</button></li>
        </ul>
        <div class="a-card-body">
            <div class="tab-content">
                <div class="tab-pane fade show active" id="s-main">
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <div class="row g-3">
                                <div class="col-md-8"><label class="form-label">Service Title *</label><input type="text" name="title" class="form-control" value="{{ old('title', $service->title) }}" required data-slug-source="#slug"></div>
                                <div class="col-md-4"><label class="form-label">Icon</label><div class="input-group"><span class="input-group-text"><i class="fa-solid {{ $service->icon ?: 'fa-icons' }}"></i></span><input type="text" name="icon" class="form-control" value="{{ old('icon', $service->icon) }}" placeholder="fa-user-shield"></div></div>
                                <div class="col-12"><label class="form-label">URL Slug</label><div class="input-group"><span class="input-group-text small">/services/</span><input type="text" id="slug" name="slug" class="form-control" value="{{ old('slug', $service->slug) }}" data-slug-target></div></div>
                                <div class="col-12"><label class="form-label">Short Description <small class="text-muted">(cards & listing)</small></label><textarea name="short_description" class="form-control" rows="2" data-counter="200">{{ old('short_description', $service->short_description) }}</textarea></div>
                                <div class="col-12"><label class="form-label">SEO-optimised Introduction</label><textarea name="intro" class="form-control" rows="3">{{ old('intro', $service->intro) }}</textarea></div>
                                <div class="col-12"><label class="form-label">Detailed Description / Overview</label><textarea name="description" class="form-control rich" data-height="300">{{ old('description', $service->description) }}</textarea></div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="published" @selected(old('status', $service->status) === 'published')>Published</option><option value="draft" @selected(old('status', $service->status) === 'draft')>Draft</option></select></div>
                            @include('admin.partials.image-field', ['name' => 'image', 'label' => 'Featured Image (cards)', 'value' => $service->image])
                            @include('admin.partials.image-field', ['name' => 'banner_image', 'label' => 'Banner Image (detail page hero)', 'value' => $service->banner_image])
                            <div class="mb-3"><label class="form-label">Image Alt Text</label><input type="text" name="image_alt" class="form-control" value="{{ old('image_alt', $service->image_alt) }}"></div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="s-features">
                    <div class="row g-4">
                        <div class="col-lg-6">@include('admin.partials.repeater', ['name' => 'features', 'items' => old('features', $service->features ?? []), 'fields' => $tt, 'label' => 'Key Features', 'addLabel' => 'Add feature'])</div>
                        <div class="col-lg-6">@include('admin.partials.repeater', ['name' => 'benefits', 'items' => old('benefits', $service->benefits ?? []), 'fields' => $tt, 'label' => 'Benefits', 'addLabel' => 'Add benefit'])</div>
                    </div>
                </div>
                <div class="tab-pane fade" id="s-process">
                    @include('admin.partials.repeater', ['name' => 'process', 'items' => old('process', $service->process ?? []), 'fields' => $tt, 'label' => 'Working Process Steps', 'addLabel' => 'Add step'])
                </div>
                <div class="tab-pane fade" id="s-rel">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <label class="form-label">Industries Where This Service Is Applicable</label>
                            @php($selInd = old('industry_ids', $service->exists ? $service->industries->pluck('id')->all() : []))
                            <div class="border rounded-3 p-3" style="max-height:340px;overflow:auto">
                                @foreach($industries as $i)<div class="form-check"><input class="form-check-input" type="checkbox" name="industry_ids[]" value="{{ $i->id }}" id="ind{{ $i->id }}" @checked(in_array($i->id, $selInd))><label class="form-check-label" for="ind{{ $i->id }}">{{ $i->title }}</label></div>@endforeach
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label">Related Services <small class="text-muted">(empty = auto)</small></label>
                            @php($selRel = old('related_ids', $service->exists ? $service->related->pluck('id')->all() : []))
                            <div class="border rounded-3 p-3" style="max-height:340px;overflow:auto">
                                @foreach($allServices as $r)<div class="form-check"><input class="form-check-input" type="checkbox" name="related_ids[]" value="{{ $r->id }}" id="rel{{ $r->id }}" @checked(in_array($r->id, $selRel))><label class="form-check-label" for="rel{{ $r->id }}">{{ $r->title }}</label></div>@endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="s-faq">
                    @include('admin.partials.repeater', ['name' => 'faqs', 'items' => old('faqs', $service->exists ? $service->faqs->map(fn ($f) => ['question' => $f->question, 'answer' => $f->answer])->all() : []), 'fields' => ['question' => ['label' => 'Question', 'type' => 'text', 'col' => 'col-12'], 'answer' => ['label' => 'Answer', 'type' => 'textarea']], 'label' => 'Service FAQs', 'addLabel' => 'Add FAQ'])
                </div>
                <div class="tab-pane fade" id="s-seo">
                    @include('admin.partials.seo-fields', ['model' => $service, 'url' => $service->exists ? $service->url : url('services'), 'defaultTitle' => ($service->title ?: 'Service').setting('seo_title_suffix'), 'defaultDesc' => $service->short_description])
                </div>
            </div>
            <div class="sticky-actions">
                <a href="{{ route('admin.services.index') }}" class="btn btn-light">Back</a>
                @if($service->exists)<a href="{{ $service->url }}" target="_blank" class="btn btn-soft"><i class="fa-solid fa-eye me-1"></i> View</a>@endif
                <button class="btn btn-primary px-4"><i class="fa-solid fa-floppy-disk me-1"></i> Save Service</button>
            </div>
        </div>
    </div>
</form>
@endsection
