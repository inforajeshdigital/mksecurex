@extends('layouts.admin')
@section('title', 'FAQ Management')
@section('crumbs', 'Content › FAQs')

@section('content')
<div class="a-card">
    <div class="a-card-head">
        <form method="GET"><select name="category" class="form-select form-select-sm" onchange="this.form.submit()" style="width:220px"><option value="">All categories</option>@foreach($categories as $k => $l)<option value="{{ $k }}" @selected(request('category') === $k)>{{ $l }}</option>@endforeach</select></form>
        <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i> Add FAQ</a>
    </div>
    <div class="table-responsive">
        <table class="table table-a">
            <thead><tr><th style="width:40px"></th><th>Question</th><th>Category</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
            <tbody data-sortable="faqs">
            @forelse($faqs as $f)
                <tr data-id="{{ $f->id }}">
                    <td><span class="drag-handle"><i class="fa-solid fa-grip-vertical"></i></span></td>
                    <td><strong>{{ $f->question }}</strong><div class="small text-muted">{{ \Illuminate\Support\Str::limit($f->answer, 100) }}</div></td>
                    <td class="small">{{ $categories[$f->category] ?? $f->category }}</td>
                    <td><span class="badge-soft {{ $f->is_active ? 'active' : 'inactive' }}">{{ $f->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('admin.faqs.edit', $f) }}" class="btn btn-icon btn-soft"><i class="fa-solid fa-pen"></i></a>
                        <form action="{{ route('admin.faqs.destroy', $f) }}" method="POST" class="d-inline" data-confirm="Delete this FAQ?">@csrf @method('DELETE')<button class="btn btn-icon btn-soft-danger"><i class="fa-solid fa-trash"></i></button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5"><div class="empty-state">No FAQs yet.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 small text-muted border-top"><i class="fa-solid fa-circle-info me-1"></i> Service and industry specific FAQs are managed inside each service / industry.</div>
</div>
@endsection
