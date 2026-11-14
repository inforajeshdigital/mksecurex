@extends('layouts.admin')
@section('title', 'Page Management')
@section('crumbs', 'Content › Pages')

@section('content')
<div class="a-card">
    <div class="a-card-head">
        <h2>All Pages</h2>
        <a href="{{ route('admin.pages.create') }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i> New Page</a>
    </div>
    <div class="table-responsive">
        <table class="table table-a">
            <thead><tr><th style="width:40px"></th><th>Title</th><th>URL</th><th>Sections</th><th>Status</th><th>Updated</th><th class="text-end">Actions</th></tr></thead>
            <tbody data-sortable="pages">
            @foreach($pages as $p)
                <tr data-id="{{ $p->id }}">
                    <td><span class="drag-handle"><i class="fa-solid fa-grip-vertical"></i></span></td>
                    <td><strong>{{ $p->title }}</strong> @if($p->is_system)<span class="badge bg-light text-muted border ms-1">System</span>@endif</td>
                    <td class="small"><a href="{{ in_array($p->slug, ['services', 'industries']) ? url($p->slug) : $p->url }}" target="_blank">/{{ $p->slug === 'home' ? '' : $p->slug }}</a></td>
                    <td>{{ in_array($p->slug, ['services', 'industries']) ? 'Auto' : $p->sections_count }}</td>
                    <td><span class="badge-soft {{ $p->status }}">{{ ucfirst($p->status) }}</span></td>
                    <td class="small text-muted">{{ $p->updated_at->format('d M Y') }}</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('admin.pages.preview', $p) }}" target="_blank" class="btn btn-icon btn-soft" title="Preview"><i class="fa-solid fa-eye"></i></a>
                        <a href="{{ $p->slug === 'home' ? route('admin.homepage') : route('admin.pages.edit', $p) }}" class="btn btn-icon btn-soft" title="Edit"><i class="fa-solid fa-pen"></i></a>
                        @unless($p->is_system)
                            <form action="{{ route('admin.pages.destroy', $p) }}" method="POST" class="d-inline" data-confirm="Delete this page?">@csrf @method('DELETE')<button class="btn btn-icon btn-soft-danger" title="Delete"><i class="fa-solid fa-trash"></i></button></form>
                        @endunless
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
