@extends('layouts.admin')
@section('title', 'Activity Log')
@section('crumbs', 'System › Activity')

@section('content')
<div class="a-card">
    <div class="table-responsive">
        <table class="table table-a">
            <thead><tr><th>When</th><th>User</th><th>Action</th><th>Description</th><th>IP</th></tr></thead>
            <tbody>
            @forelse($logs as $l)
                <tr>
                    <td class="small text-muted text-nowrap">{{ $l->created_at->format('d M Y, h:i A') }}</td>
                    <td>{{ $l->user->name ?? 'System' }}</td>
                    <td><span class="badge bg-light text-dark border">{{ $l->action }}</span></td>
                    <td class="small">{{ $l->description }}</td>
                    <td class="small text-muted">{{ $l->ip_address }}</td>
                </tr>
            @empty
                <tr><td colspan="5"><div class="empty-state">No activity yet.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())<div class="p-3 border-top">{{ $logs->links() }}</div>@endif
</div>
@endsection
