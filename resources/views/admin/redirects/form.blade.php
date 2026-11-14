@extends('layouts.admin')
@section('title', $redirect->exists ? 'Edit Redirect' : 'Add Redirect')
@section('crumbs', 'SEO › Redirects')

@section('content')
<form action="{{ $redirect->exists ? route('admin.redirects.update', $redirect) : route('admin.redirects.store') }}" method="POST">
    @csrf @if($redirect->exists) @method('PUT') @endif
    <div class="a-card" style="max-width:820px"><div class="a-card-body">
        <div class="row g-3">
            <div class="col-12"><label class="form-label">Old URL (path) *</label><div class="input-group"><span class="input-group-text small">{{ url('/') }}</span><input type="text" name="from_path" class="form-control" value="{{ old('from_path', $redirect->from_path) }}" placeholder="/old-page" required></div></div>
            <div class="col-12"><label class="form-label">Destination URL *</label><input type="text" name="to_url" class="form-control" value="{{ old('to_url', $redirect->to_url) }}" placeholder="/services/security-services or https://…" required></div>
            <div class="col-md-6"><label class="form-label">Redirect Type</label><select name="type" class="form-select"><option value="301" @selected(old('type', $redirect->type) == 301)>301 – Permanent</option><option value="302" @selected(old('type', $redirect->type) == 302)>302 – Temporary</option></select></div>
            <div class="col-md-6 d-flex align-items-end"><div class="form-check form-switch mb-2"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="act" @checked(old('is_active', $redirect->is_active))><label class="form-check-label fw-semibold" for="act">Active</label></div></div>
            <div class="col-12"><label class="form-label">Notes</label><input type="text" name="notes" class="form-control" value="{{ old('notes', $redirect->notes) }}"></div>
        </div>
        <div class="alert alert-light border small mt-3 mb-0"><i class="fa-solid fa-shield-halved me-1"></i> Loops and chains are blocked automatically. Admin URLs cannot be redirected.</div>
        <div class="sticky-actions mt-4"><a href="{{ route('admin.redirects.index') }}" class="btn btn-light">Cancel</a><button class="btn btn-primary px-4"><i class="fa-solid fa-floppy-disk me-1"></i> Save Redirect</button></div>
    </div></div>
</form>
@endsection
