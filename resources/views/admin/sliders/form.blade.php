@extends('layouts.admin')
@section('title', $slider->exists ? 'Edit Slide' : 'Add Slide')
@section('crumbs', 'Homepage › Hero Slides')

@section('content')
<form action="{{ $slider->exists ? route('admin.sliders.update', $slider) : route('admin.sliders.store') }}" method="POST" enctype="multipart/form-data">
    @csrf @if($slider->exists) @method('PUT') @endif
    <div class="row g-4">
        <div class="col-xl-8">
            <div class="a-card"><div class="a-card-body">
                <div class="row g-3">
                    <div class="col-md-4"><label class="form-label">Badge</label><input type="text" name="badge" class="form-control" value="{{ old('badge', $slider->badge) }}"></div>
                    <div class="col-md-8"><label class="form-label">Heading *</label><input type="text" name="heading" class="form-control" value="{{ old('heading', $slider->heading) }}" required></div>
                    <div class="col-12"><label class="form-label">Sub-heading</label><input type="text" name="subheading" class="form-control" value="{{ old('subheading', $slider->subheading) }}"></div>
                    <div class="col-12"><label class="form-label">Short Introduction</label><textarea name="description" rows="3" class="form-control">{{ old('description', $slider->description) }}</textarea></div>
                    <div class="col-md-6"><label class="form-label">Primary Button Text</label><input type="text" name="btn1_text" class="form-control" value="{{ old('btn1_text', $slider->btn1_text) }}"></div>
                    <div class="col-md-6"><label class="form-label">Primary Button URL</label><input type="text" name="btn1_url" class="form-control" value="{{ old('btn1_url', $slider->btn1_url) }}"></div>
                    <div class="col-md-6"><label class="form-label">Secondary Button Text</label><input type="text" name="btn2_text" class="form-control" value="{{ old('btn2_text', $slider->btn2_text) }}"></div>
                    <div class="col-md-6"><label class="form-label">Secondary Button URL</label><input type="text" name="btn2_url" class="form-control" value="{{ old('btn2_url', $slider->btn2_url) }}"></div>
                </div>
            </div></div>
        </div>
        <div class="col-xl-4">
            <div class="a-card"><div class="a-card-body">
                <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="act" @checked(old('is_active', $slider->is_active))><label class="form-check-label fw-semibold" for="act">Published</label></div>
                @include('admin.partials.image-field', ['name' => 'image', 'label' => 'Slide Image', 'value' => $slider->image, 'size' => '1920x1080', 'sizeNote' => 'landscape; keep the main subject on the right side'])
                <div class="mb-3"><label class="form-label">Image Alt Text</label><input type="text" name="image_alt" class="form-control" value="{{ old('image_alt', $slider->image_alt) }}"></div>
                <label class="form-label">Where should this image show?</label>
                <div class="display-opts">
                    <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="show_background" value="1" id="sbg" @checked(old('show_background', $slider->exists ? $slider->show_background : true))><label class="form-check-label fw-semibold" for="sbg">Full-width background<small>Image behind the text with a dark blue overlay</small></label></div>
                    <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="show_side_image" value="1" id="ssi" @checked(old('show_side_image', $slider->exists ? $slider->show_side_image : false))><label class="form-check-label fw-semibold" for="ssi">Framed image on the right<small>Desktop only. Turn one of these off to avoid seeing the same photo twice.</small></label></div>
                </div>
                <div class="form-text mb-2"><i class="fa-solid fa-circle-half-stroke me-1"></i>Text covering the photo or banner too dark? Change <a href="{{ route('admin.settings', 'general') }}#s_hero_layout">Banner layout</a> (Split / Full image) and overlay in Website Settings.</div>
            </div></div>
            <div class="d-grid gap-2"><button class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Save Slide</button><a href="{{ route('admin.homepage') }}#slides" class="btn btn-light">Cancel</a></div>
        </div>
    </div>
</form>
@endsection
