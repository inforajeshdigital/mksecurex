@extends('layouts.admin')
@section('title', $user->exists ? 'Edit User' : 'Add User')
@section('crumbs', 'System › Users')

@section('content')
<form action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}" method="POST">
    @csrf @if($user->exists) @method('PUT') @endif
    <div class="a-card" style="max-width:820px"><div class="a-card-body">
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Name *</label><input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required></div>
            <div class="col-md-6"><label class="form-label">Email *</label><input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required autocomplete="off"></div>
            <div class="col-md-6"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}"></div>
            <div class="col-md-6"><label class="form-label">Role *</label><select name="role_id" class="form-select" @disabled($user->id === auth()->id())>@foreach($roles as $r)<option value="{{ $r->id }}" @selected(old('role_id', $user->role_id) == $r->id)>{{ $r->name }}</option>@endforeach</select>@if($user->id === auth()->id())<input type="hidden" name="role_id" value="{{ $user->role_id }}">@endif</div>
            <div class="col-md-6"><label class="form-label">Password {{ $user->exists ? '(leave blank to keep)' : '*' }}</label><input type="password" name="password" class="form-control" autocomplete="new-password" @if(!$user->exists) required @endif></div>
            <div class="col-md-6"><label class="form-label">Confirm Password</label><input type="password" name="password_confirmation" class="form-control" autocomplete="new-password"></div>
            <div class="col-12"><div class="form-text">Min 8 characters with letters and numbers.</div></div>
            <div class="col-12"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="act" @checked(old('is_active', $user->is_active)) @disabled($user->id === auth()->id())><label class="form-check-label fw-semibold" for="act">Active (can log in)</label></div>@if($user->id === auth()->id())<input type="hidden" name="is_active" value="1">@endif</div>
        </div>
        <div class="sticky-actions mt-4"><a href="{{ route('admin.users.index') }}" class="btn btn-light">Cancel</a><button class="btn btn-primary px-4"><i class="fa-solid fa-floppy-disk me-1"></i> Save User</button></div>
    </div></div>
</form>
@endsection
