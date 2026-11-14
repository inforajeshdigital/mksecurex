@extends('layouts.admin')
@section('title', $industry->exists ? 'Edit Industry: '.$industry->title : 'Add Industry')
@section('crumbs', 'Content › Industries')

@section('content')
@php($tt = ['title' => ['label' => 'Title', 'type' => 'text', 'col' => 'col-12'], 'text' => ['label' => 'Description (optional)', 'type' => 'textarea']])
<form action="{{ $industry->exists ? route('admin.industries.update', $industry) : route('admin.industries.store') }}" method="POST" enctype="multipart/form-data">
    @csrf @if($industry->exists) @method('PUT') @endif
    <div class="a-card">
        <ul class="nav nav-tabs-a px-3 pt-2">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#i-main" type="button">Content</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#i-work" type="button">Workforce & Services</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#i-chal" type="button">Challenges & Solutions</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#i-ben" type="button">Benefits & FAQs</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#i-seo" type="button"><i class="fa-solid fa-magnifying-glass-chart me-1"></i>SEO</button></li>
        </ul>
        <div class="a-card-body">
            <div class="tab-content">
                <div class="tab-pane fade show active" id="i-main">
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <div class="row g-3">
                                <div class="col-md-8"><label class="form-label">Industry Title *</label><input type="text" name="title" class="form-control" value="{{ old('title', $industry->title) }}" required data-slug-source="#slug"></div>
                                <div class="col-md-4"><label class="form-label">Icon</label><div class="input-group"><span class="input-group-text"><i class="fa-solid {{ $industry->icon ?: 'fa-icons' }}"></i></span><input type="text" name="icon" class="form-control" value="{{ old('icon', $industry->icon) }}" placeholder="fa-industry"></div></div>
                                <div class="col-12"><label class="form-label">URL Slug</label><div class="input-group"><span class="input-group-text small">/industries/</span><input type="text" id="slug" name="slug" class="form-control" value="{{ old('slug', $industry->slug) }}" data-slug-target></div></div>
                                <div class="col-12"><label class="form-label">Short Description</label><textarea name="short_description" class="form-control" rows="2" data-counter="200">{{ old('short_description', $industry->short_description) }}</textarea></div>
                                <div class="col-12"><label class="form-label">Introduction</label><textarea name="intro" class="form-control" rows="3">{{ old('intro', $industry->intro) }}</textarea></div>
                                <div class="col-12"><label class="form-label">Industry Overview</label><textarea name="description" class="form-control rich" data-height="280">{{ old('description', $industry->description) }}</textarea></div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="published" @selected(old('status', $industry->status) === 'published')>Published</option><option value="draft" @selected(old('status', $industry->status) === 'draft')>Draft</option></select></div>
                            @include('admin.partials.image-field', ['name' => 'image', 'label' => 'Card / Featured Image', 'value' => $industry->image, 'size' => '1200x800', 'sizeNote' => 'used on industry cards'])
                            @include('admin.partials.image-field', ['name' => 'banner_image', 'label' => 'Banner Image (top of detail page)', 'value' => $industry->banner_image, 'size' => '1920x600', 'sizeNote' => 'wide, text sits on the left'])
                            <div class="mb-3"><label class="form-label">Image Alt Text</label><input type="text" name="image_alt" class="form-control" value="{{ old('image_alt', $industry->image_alt) }}"></div>
                            @include('admin.partials.display-options', ['model' => $industry])
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="i-work">
                    <div class="row g-4">
                        <div class="col-lg-6">@include('admin.partials.repeater', ['name' => 'workforce_requirements', 'items' => old('workforce_requirements', $industry->workforce_requirements ?? []), 'fields' => $tt, 'label' => 'Workforce Requirements', 'addLabel' => 'Add requirement'])</div>
                        <div class="col-lg-6">
                            <label class="form-label">Services We Provide for This Industry</label>
                            @php($sel = old('service_ids', $industry->exists ? $industry->services->pluck('id')->all() : []))
                            <div class="border rounded-3 p-3">
                                @foreach($services as $s)<div class="form-check"><input class="form-check-input" type="checkbox" name="service_ids[]" value="{{ $s->id }}" id="sv{{ $s->id }}" @checked(in_array($s->id, $sel))><label class="form-check-label" for="sv{{ $s->id }}">{{ $s->title }}</label></div>@endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="i-chal">
                    <div class="row g-4">
                        <div class="col-lg-6">@include('admin.partials.repeater', ['name' => 'challenges', 'items' => old('challenges', $industry->challenges ?? []), 'fields' => $tt, 'label' => 'Operational Challenges', 'addLabel' => 'Add challenge'])</div>
                        <div class="col-lg-6">@include('admin.partials.repeater', ['name' => 'solutions', 'items' => old('solutions', $industry->solutions ?? []), 'fields' => $tt, 'label' => 'Industry-Specific Solutions', 'addLabel' => 'Add solution'])</div>
                    </div>
                </div>
                <div class="tab-pane fade" id="i-ben">
                    <div class="row g-4">
                        <div class="col-lg-6">@include('admin.partials.repeater', ['name' => 'benefits', 'items' => old('benefits', $industry->benefits ?? []), 'fields' => $tt, 'label' => 'Benefits', 'addLabel' => 'Add benefit'])</div>
                        <div class="col-lg-6">@include('admin.partials.repeater', ['name' => 'faqs', 'items' => old('faqs', $industry->exists ? $industry->faqs->map(fn ($f) => ['question' => $f->question, 'answer' => $f->answer])->all() : []), 'fields' => ['question' => ['label' => 'Question', 'type' => 'text', 'col' => 'col-12'], 'answer' => ['label' => 'Answer', 'type' => 'textarea']], 'label' => 'Industry FAQs', 'addLabel' => 'Add FAQ'])</div>
                    </div>
                </div>
                <div class="tab-pane fade" id="i-seo">
                    @include('admin.partials.seo-fields', ['model' => $industry, 'url' => $industry->exists ? $industry->url : url('industries'), 'defaultTitle' => ($industry->title ?: 'Industry').setting('seo_title_suffix'), 'defaultDesc' => $industry->short_description])
                </div>
            </div>
            <div class="sticky-actions">
                <a href="{{ route('admin.industries.index') }}" class="btn btn-light">Back</a>
                @if($industry->exists)<a href="{{ $industry->url }}" target="_blank" class="btn btn-soft"><i class="fa-solid fa-eye me-1"></i> View</a>@endif
                <button class="btn btn-primary px-4"><i class="fa-solid fa-floppy-disk me-1"></i> Save Industry</button>
            </div>
        </div>
    </div>
</form>
@endsection
