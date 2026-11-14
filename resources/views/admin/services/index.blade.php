@extends('layouts.admin')
@section('title', 'Services Management')
@section('crumbs', 'Content › Services')

@section('content')
<div class="a-card">
    <div class="a-card-head">
        <form class="d-flex gap-2 flex-wrap" method="GET">
            <input type="search" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Search services…" style="width:220px">
            <select name="status" class="form-select form-select-sm" style="width:140px" onchange="this.form.submit()"><option value="">All status</option><option value="published" @selected(request('status') === 'published')>Published</option><option value="draft" @selected(request('status') === 'draft')>Draft</option></select>
        </form>
        <a href="{{ route('admin.services.create') }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i> Add Service</a>
    </div>
    <div class="table-responsive">
        <table class="table table-a">
            <thead><tr><th style="width:40px"></th><th>Service</th><th>Industries</th><th>SEO</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
            <tbody data-sortable="services">
            @forelse($services as $s)
                <tr data-id="{{ $s->id }}">
                    <td><span class="drag-handle"><i class="fa-solid fa-grip-vertical"></i></span></td>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            @if($s->image)<img src="{{ img_url($s->image) }}" class="thumb-sm" alt="">@else<span class="thumb-sm"><i class="fa-solid {{ $s->icon ?: 'fa-briefcase' }}"></i></span>@endif
                            <div><strong>{{ $s->title }}</strong><div class="small text-muted">/services/{{ $s->slug }}</div></div>
                        </div>
                    </td>
                    <td>{{ $s->industries_count }}</td>
                    <td><a href="{{ route('admin.seo.edit', ['service', $s->id]) }}" class="small">Edit SEO</a></td>
                    <td><span class="badge-soft {{ $s->status }}">{{ ucfirst($s->status) }}</span></td>
                    <td class="text-end text-nowrap">
                        <a href="{{ $s->url }}" target="_blank" class="btn btn-icon btn-soft" title="View"><i class="fa-solid fa-eye"></i></a>
                        <a href="{{ route('admin.services.edit', $s) }}" class="btn btn-icon btn-soft" title="Edit"><i class="fa-solid fa-pen"></i></a>
                        <form action="{{ route('admin.services.destroy', $s) }}" method="POST" class="d-inline" data-confirm="Delete this service?">@csrf @method('DELETE')<button class="btn btn-icon btn-soft-danger"><i class="fa-solid fa-trash"></i></button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6"><div class="empty-state"><i class="fa-solid fa-briefcase d-block"></i>No services found.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
