@extends('layouts.admin')
@section('title', 'Homepage Management')
@section('crumbs', 'Content › Homepage')

@section('content')
<ul class="nav nav-tabs-a mb-4" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#t-sections" type="button"><i class="fa-solid fa-layer-group me-1"></i> Sections</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#t-slides" type="button"><i class="fa-solid fa-images me-1"></i> Hero Slides ({{ $sliders->count() }})</button></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.seo.edit', ['page', $page->id]) }}"><i class="fa-solid fa-magnifying-glass-chart me-1"></i> Homepage SEO</a></li>
</ul>
<div class="tab-content">
    <div class="tab-pane fade show active" id="t-sections">@include('admin.pages._sections')</div>
    <div class="tab-pane fade" id="t-slides">
        <div class="a-card">
            <div class="a-card-head">
                <h2>Hero Banner Slides</h2>
                <a href="{{ route('admin.sliders.create') }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i> Add Slide</a>
            </div>
            <div class="table-responsive">
                <table class="table table-a">
                    <thead><tr><th style="width:40px"></th><th>Image</th><th>Heading</th><th>Buttons</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                    <tbody data-sortable="sliders">
                    @forelse($sliders as $sl)
                        <tr data-id="{{ $sl->id }}">
                            <td><span class="drag-handle"><i class="fa-solid fa-grip-vertical"></i></span></td>
                            <td>@if($sl->image)<img src="{{ img_url($sl->image) }}" class="thumb-sm" alt="">@else<span class="thumb-sm"><i class="fa-regular fa-image"></i></span>@endif</td>
                            <td><strong>{{ $sl->heading }}</strong><div class="small text-muted">{{ $sl->badge }}</div></td>
                            <td class="small">{{ collect([$sl->btn1_text, $sl->btn2_text])->filter()->implode(' · ') }}</td>
                            <td><span class="badge-soft {{ $sl->is_active ? 'published' : 'draft' }}">{{ $sl->is_active ? 'Published' : 'Unpublished' }}</span></td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('admin.sliders.edit', $sl) }}" class="btn btn-icon btn-soft"><i class="fa-solid fa-pen"></i></a>
                                <form action="{{ route('admin.sliders.destroy', $sl) }}" method="POST" class="d-inline" data-confirm="Delete this slide?">@csrf @method('DELETE')<button class="btn btn-icon btn-soft-danger"><i class="fa-solid fa-trash"></i></button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="empty-state">No slides yet.</div></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>if (location.hash === '#slides') bootstrap.Tab.getOrCreateInstance(document.querySelector('[data-bs-target="#t-slides"]')).show();</script>
@endpush
