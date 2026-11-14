@extends('layouts.admin')
@section('title', 'Industries Management')
@section('crumbs', 'Content › Industries')

@section('content')
<div class="a-card">
    <div class="a-card-head">
        <form class="d-flex gap-2 flex-wrap" method="GET">
            <input type="search" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Search industries…" style="width:220px">
            <select name="status" class="form-select form-select-sm" style="width:140px" onchange="this.form.submit()"><option value="">All status</option><option value="published" @selected(request('status') === 'published')>Published</option><option value="draft" @selected(request('status') === 'draft')>Draft</option></select>
        </form>
        <a href="{{ route('admin.industries.create') }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i> Add Industry</a>
    </div>
    <div class="table-responsive">
        <table class="table table-a">
            <thead><tr><th style="width:40px"></th><th>Industry</th><th>Linked Services</th><th>SEO</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
            <tbody data-sortable="industries">
            @forelse($industries as $i)
                <tr data-id="{{ $i->id }}">
                    <td><span class="drag-handle"><i class="fa-solid fa-grip-vertical"></i></span></td>
                    <td><div class="d-flex align-items-center gap-3">@if($i->image)<img src="{{ img_url($i->image) }}" class="thumb-sm" alt="">@else<span class="thumb-sm"><i class="fa-solid {{ $i->icon ?: 'fa-building' }}"></i></span>@endif<div><strong>{{ $i->title }}</strong><div class="small text-muted">/industries/{{ $i->slug }}</div></div></div></td>
                    <td>{{ $i->services_count }}</td>
                    <td><a href="{{ route('admin.seo.edit', ['industry', $i->id]) }}" class="small">Edit SEO</a></td>
                    <td><span class="badge-soft {{ $i->status }}">{{ ucfirst($i->status) }}</span></td>
                    <td class="text-end text-nowrap">
                        <a href="{{ $i->url }}" target="_blank" class="btn btn-icon btn-soft"><i class="fa-solid fa-eye"></i></a>
                        <a href="{{ route('admin.industries.edit', $i) }}" class="btn btn-icon btn-soft"><i class="fa-solid fa-pen"></i></a>
                        <form action="{{ route('admin.industries.destroy', $i) }}" method="POST" class="d-inline" data-confirm="Delete this industry?">@csrf @method('DELETE')<button class="btn btn-icon btn-soft-danger"><i class="fa-solid fa-trash"></i></button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6"><div class="empty-state"><i class="fa-solid fa-industry d-block"></i>No industries found.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
