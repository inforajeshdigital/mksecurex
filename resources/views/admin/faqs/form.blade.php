@extends('layouts.admin')
@section('title', $faq->exists ? 'Edit FAQ' : 'Add FAQ')
@section('crumbs', 'Content › FAQs')

@section('content')
<form action="{{ $faq->exists ? route('admin.faqs.update', $faq) : route('admin.faqs.store') }}" method="POST">
    @csrf @if($faq->exists) @method('PUT') @endif
    <div class="a-card" style="max-width:900px"><div class="a-card-body">
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Category</label><select name="category" class="form-select">@foreach($categories as $k => $l)<option value="{{ $k }}" @selected(old('category', $faq->category) === $k)>{{ $l }}</option>@endforeach</select></div>
            <div class="col-md-6 d-flex align-items-end"><div class="form-check form-switch mb-2"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="act" @checked(old('is_active', $faq->is_active))><label class="form-check-label fw-semibold" for="act">Active</label></div></div>
            <div class="col-12"><label class="form-label">Question *</label><input type="text" name="question" class="form-control" value="{{ old('question', $faq->question) }}" required></div>
            <div class="col-12"><label class="form-label">Answer *</label><textarea name="answer" rows="5" class="form-control" required>{{ old('answer', $faq->answer) }}</textarea></div>
        </div>
        <div class="sticky-actions mt-4"><a href="{{ route('admin.faqs.index') }}" class="btn btn-light">Cancel</a><button class="btn btn-primary px-4"><i class="fa-solid fa-floppy-disk me-1"></i> Save FAQ</button></div>
    </div></div>
</form>
@endsection
