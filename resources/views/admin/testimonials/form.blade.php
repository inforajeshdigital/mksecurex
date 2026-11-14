@extends('layouts.admin')
@section('title', $testimonial->exists ? 'Edit Testimonial' : 'Add Testimonial')
@section('crumbs', 'Content › Testimonials')

@section('content')
<form action="{{ $testimonial->exists ? route('admin.testimonials.update', $testimonial) : route('admin.testimonials.store') }}" method="POST" enctype="multipart/form-data">
    @csrf @if($testimonial->exists) @method('PUT') @endif
    <div class="row g-4">
        <div class="col-xl-8"><div class="a-card"><div class="a-card-body"><div class="row g-3">
            <div class="col-md-6"><label class="form-label">Client Name *</label><input type="text" name="client_name" class="form-control" value="{{ old('client_name', $testimonial->client_name) }}" required></div>
            <div class="col-md-6"><label class="form-label">Designation</label><input type="text" name="designation" class="form-control" value="{{ old('designation', $testimonial->designation) }}"></div>
            <div class="col-md-8"><label class="form-label">Company</label><input type="text" name="company" class="form-control" value="{{ old('company', $testimonial->company) }}"></div>
            <div class="col-md-4"><label class="form-label">Rating</label><select name="rating" class="form-select">@for($i = 5; $i >= 1; $i--)<option value="{{ $i }}" @selected(old('rating', $testimonial->rating) == $i)>{{ $i }} star{{ $i > 1 ? 's' : '' }}</option>@endfor</select></div>
            <div class="col-12"><label class="form-label">Testimonial *</label><textarea name="content" rows="5" class="form-control" required>{{ old('content', $testimonial->content) }}</textarea></div>
        </div></div></div></div>
        <div class="col-xl-4">
            <div class="a-card"><div class="a-card-body">
                <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="act" @checked(old('is_active', $testimonial->is_active))><label class="form-check-label fw-semibold" for="act">Active</label></div>
                @include('admin.partials.image-field', ['name' => 'image', 'label' => 'Photo / Logo', 'value' => $testimonial->image])
            </div></div>
            <div class="d-grid gap-2"><button class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Save</button><a href="{{ route('admin.testimonials.index') }}" class="btn btn-light">Cancel</a></div>
        </div>
    </div>
</form>
@endsection
