@extends('layouts.admin')
@section('title', $role->exists ? 'Edit Role: '.$role->name : 'Add Role')
@section('crumbs', 'System › Roles')

@section('content')
@php($isSuper = $role->slug === 'super-admin')
<form action="{{ $role->exists ? route('admin.roles.update', $role) : route('admin.roles.store') }}" method="POST">
    @csrf @if($role->exists) @method('PUT') @endif
    <div class="a-card" style="max-width:900px"><div class="a-card-body">
        <div class="row g-3">
            <div class="col-md-5"><label class="form-label">Role Name *</label><input type="text" name="name" class="form-control" value="{{ old('name', $role->name) }}" required @readonly($isSuper)></div>
            <div class="col-md-7"><label class="form-label">Description</label><input type="text" name="description" class="form-control" value="{{ old('description', $role->description) }}"></div>
        </div>
        <hr>
        <label class="form-label d-block">Permissions (modules this role can access)</label>
        @if($isSuper)
            <div class="alert alert-info small mb-0"><i class="fa-solid fa-crown me-1"></i> Super Admin always has full access.</div>
        @else
            @php($perms = old('permissions', $role->permissions ?? []))
            <div class="row g-2">
                @foreach(config('admin.permissions') as $k => $l)
                    <div class="col-md-4"><div class="form-check border rounded-3 p-2 ps-5"><input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $k }}" id="p{{ $k }}" @checked(in_array($k, $perms) || $k === 'dashboard') @disabled($k === 'dashboard')><label class="form-check-label" for="p{{ $k }}">{{ $l }}</label></div></div>
                @endforeach
            </div>
        @endif
        <div class="sticky-actions mt-4"><a href="{{ route('admin.users.index') }}" class="btn btn-light">Cancel</a><button class="btn btn-primary px-4"><i class="fa-solid fa-floppy-disk me-1"></i> Save Role</button></div>
    </div></div>
</form>
@endsection
