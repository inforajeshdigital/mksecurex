@extends('layouts.admin')
@section('title', $page->exists ? 'Edit Page: '.$page->title : 'New Page')
@section('crumbs', 'Content › Pages › '.($page->exists ? $page->title : 'New'))

@section('content')
@php($auto = in_array($page->slug, ['services', 'industries']))
<form action="{{ $page->exists ? route('admin.pages.update', $page) : route('admin.pages.store') }}" method="POST" enctype="multipart/form-data">
    @csrf @if($page->exists) @method('PUT') @endif
    <div class="a-card">
        <ul class="nav nav-tabs-a px-3 pt-2" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#p-content" type="button">Page Details</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#p-seo" type="button"><i class="fa-solid fa-magnifying-glass-chart me-1"></i> SEO</button></li>
        </ul>
        <div class="a-card-body">
            <div class="tab-content">
                <div class="tab-pane fade show active" id="p-content">
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <div class="mb-3"><label class="form-label">Page Title *</label><input type="text" name="title" class="form-control" value="{{ old('title', $page->title) }}" required data-slug-source="#slug"></div>
                            <div class="mb-3">
                                <label class="form-label">URL Slug</label>
                                <div class="input-group"><span class="input-group-text small">{{ url('/') }}/</span><input type="text" id="slug" name="slug" class="form-control" value="{{ old('slug', $page->slug) }}" data-slug-target @disabled($page->is_system)></div>
                                @if($page->is_system)<div class="form-text">System page URL is fixed.</div>@endif
                            </div>
                            <div class="mb-3"><label class="form-label">Short Description / Banner Subtitle</label><textarea name="excerpt" class="form-control" rows="3">{{ old('excerpt', $page->excerpt) }}</textarea></div>
                            @if($auto)<div class="alert alert-info small"><i class="fa-solid fa-circle-info me-1"></i> This listing page shows all published {{ $page->slug }} automatically. Manage the items in the {{ ucfirst($page->slug) }} module.</div>@endif
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-3"><label class="form-label">Status</label>
                                <select name="status" class="form-select"><option value="published" @selected(old('status', $page->status) === 'published')>Published</option><option value="draft" @selected(old('status', $page->status) === 'draft')>Draft</option></select></div>
                            <div class="mb-3"><label class="form-label">Published On</label><input type="date" name="published_on" class="form-control" value="{{ old('published_on', optional($page->published_on)->format('Y-m-d')) }}"></div>
                            @include('admin.partials.image-field', ['name' => 'banner_image', 'label' => 'Banner Image', 'value' => $page->banner_image])
                            <div class="mb-3"><label class="form-label">Banner Image Alt Text</label><input type="text" name="banner_alt" class="form-control" value="{{ old('banner_alt', $page->banner_alt) }}"></div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="p-seo">
                    @include('admin.partials.seo-fields', ['model' => $page, 'url' => $page->exists ? $page->url : url('/'), 'defaultTitle' => ($page->title ?: 'Page').setting('seo_title_suffix'), 'defaultDesc' => $page->excerpt ?: setting('seo_default_description')])
                </div>
            </div>
            <div class="sticky-actions">
                <a href="{{ route('admin.pages.index') }}" class="btn btn-light">Back</a>
                @if($page->exists)<a href="{{ route('admin.pages.preview', $page) }}" target="_blank" class="btn btn-soft"><i class="fa-solid fa-eye me-1"></i> Preview</a>@endif
                <button class="btn btn-primary px-4"><i class="fa-solid fa-floppy-disk me-1"></i> Save Page</button>
            </div>
        </div>
    </div>
</form>

@if($page->exists && !$auto)
    @include('admin.pages._sections')
@endif
@endsection
