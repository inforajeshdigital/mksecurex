@extends('layouts.admin')
@section('title', 'Redirect Management')
@section('crumbs', 'SEO › Redirects')

@section('content')
<div class="a-card">
    <div class="a-card-head">
        <form method="GET"><input type="search" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Search URLs" style="width:240px"></form>
        <a href="{{ route('admin.redirects.create') }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i> Add Redirect</a>
    </div>
    <div class="table-responsive">
        <table class="table table-a">
            <thead><tr><th>Old URL</th><th></th><th>Destination</th><th>Type</th><th>Hits</th><th>Last Hit</th><th>Status</th><th class="text-end"></th></tr></thead>
            <tbody>
            @forelse($redirects as $r)
                <tr>
                    <td class="small"><code>{{ $r->from_path }}</code>@if($r->notes)<div class="text-muted">{{ $r->notes }}</div>@endif</td>
                    <td><i class="fa-solid fa-arrow-right text-muted"></i></td>
                    <td class="small"><code>{{ $r->to_url }}</code></td>
                    <td><span class="badge {{ $r->type == 301 ? 'bg-primary' : 'bg-warning text-dark' }}">{{ $r->type }}</span></td>
                    <td>{{ number_format($r->hits) }}</td>
                    <td class="small text-muted">{{ $r->last_hit_at?->diffForHumans() ?? '—' }}</td>
                    <td><span class="badge-soft {{ $r->is_active ? 'active' : 'inactive' }}">{{ $r->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('admin.redirects.edit', $r) }}" class="btn btn-icon btn-soft"><i class="fa-solid fa-pen"></i></a>
                        <form action="{{ route('admin.redirects.destroy', $r) }}" method="POST" class="d-inline" data-confirm="Delete this redirect?">@csrf @method('DELETE')<button class="btn btn-icon btn-soft-danger"><i class="fa-solid fa-trash"></i></button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8"><div class="empty-state"><i class="fa-solid fa-diamond-turn-right d-block"></i>No redirects. Unknown URLs return a genuine 404.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($redirects->hasPages())<div class="p-3 border-top">{{ $redirects->links() }}</div>@endif
</div>
@endsection
