@extends('layouts.admin')
@section('title', 'Users & Roles')
@section('crumbs', 'System › Users')

@section('content')
<div class="row g-4">
    <div class="col-xl-8">
        <div class="a-card">
            <div class="a-card-head"><h2>Admin Users</h2><a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-user-plus me-1"></i> Add User</a></div>
            <div class="table-responsive">
                <table class="table table-a">
                    <thead><tr><th>User</th><th>Role</th><th>Last Login</th><th>Status</th><th class="text-end"></th></tr></thead>
                    <tbody>
                    @foreach($users as $u)
                        <tr>
                            <td><div class="d-flex gap-2 align-items-center"><span class="a-avatar">{{ strtoupper(mb_substr($u->name, 0, 1)) }}</span><div><strong>{{ $u->name }}</strong> @if($u->id === auth()->id())<span class="badge bg-light text-muted border">You</span>@endif<div class="small text-muted">{{ $u->email }}</div></div></div></td>
                            <td>{{ $u->role?->name ?? '—' }}</td>
                            <td class="small text-muted">{{ $u->last_login_at?->diffForHumans() ?? 'Never' }}</td>
                            <td><span class="badge-soft {{ $u->is_active ? 'active' : 'inactive' }}">{{ $u->is_active ? 'Active' : 'Inactive' }}</span></td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-icon btn-soft"><i class="fa-solid fa-pen"></i></a>
                                @if($u->id !== auth()->id())<form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="d-inline" data-confirm="Delete this user?">@csrf @method('DELETE')<button class="btn btn-icon btn-soft-danger"><i class="fa-solid fa-trash"></i></button></form>@endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="a-card">
            <div class="a-card-head"><h2>Roles & Permissions</h2><a href="{{ route('admin.roles.create') }}" class="btn btn-sm btn-soft"><i class="fa-solid fa-plus"></i></a></div>
            <div class="a-card-body">
                @foreach($roles as $r)
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <div><strong>{{ $r->name }}</strong><div class="small text-muted">{{ $r->description }} · {{ $r->users_count }} user(s)</div></div>
                        <div class="text-nowrap">
                            <a href="{{ route('admin.roles.edit', $r) }}" class="btn btn-icon btn-soft"><i class="fa-solid fa-pen"></i></a>
                            @if($r->slug !== 'super-admin' && !$r->users_count)<form action="{{ route('admin.roles.destroy', $r) }}" method="POST" class="d-inline" data-confirm="Delete this role?">@csrf @method('DELETE')<button class="btn btn-icon btn-soft-danger"><i class="fa-solid fa-trash"></i></button></form>@endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
