@extends('layouts.admin')
@section('title', 'Testimonials')
@section('crumbs', 'Content › Testimonials')

@section('content')
<div class="alert alert-warning small"><i class="fa-solid fa-triangle-exclamation me-1"></i> Publish only genuine feedback from verified clients. Placeholder entries are kept inactive.</div>
<div class="a-card">
    <div class="a-card-head"><h2>All Testimonials</h2><a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i> Add Testimonial</a></div>
    <div class="table-responsive">
        <table class="table table-a">
            <thead><tr><th style="width:40px"></th><th>Client</th><th>Testimonial</th><th>Rating</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
            <tbody data-sortable="testimonials">
            @forelse($testimonials as $t)
                <tr data-id="{{ $t->id }}">
                    <td><span class="drag-handle"><i class="fa-solid fa-grip-vertical"></i></span></td>
                    <td><strong>{{ $t->client_name }}</strong><div class="small text-muted">{{ collect([$t->designation, $t->company])->filter()->implode(', ') }}</div></td>
                    <td class="small" style="max-width:380px">{{ \Illuminate\Support\Str::limit($t->content, 110) }}</td>
                    <td class="text-warning text-nowrap">@for($i = 1; $i <= 5; $i++)<i class="fa-{{ $i <= $t->rating ? 'solid' : 'regular' }} fa-star small"></i>@endfor</td>
                    <td><span class="badge-soft {{ $t->is_active ? 'active' : 'inactive' }}">{{ $t->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('admin.testimonials.edit', $t) }}" class="btn btn-icon btn-soft"><i class="fa-solid fa-pen"></i></a>
                        <form action="{{ route('admin.testimonials.destroy', $t) }}" method="POST" class="d-inline" data-confirm="Delete this testimonial?">@csrf @method('DELETE')<button class="btn btn-icon btn-soft-danger"><i class="fa-solid fa-trash"></i></button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6"><div class="empty-state">No testimonials yet.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
