@extends('layouts.admin')
@section('title', 'My Profile')
@section('crumbs', 'Account › Profile')

@section('content')
<form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="row g-4" style="max-width:1000px">
        <div class="col-md-8"><div class="a-card"><div class="a-card-body"><div class="row g-3">
            <div class="col-md-6"><label class="form-label">Name *</label><input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required></div>
            <div class="col-md-6"><label class="form-label">Email *</label><input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required></div>
            <div class="col-md-6"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}"></div>
            <div class="col-md-6"><label class="form-label">Role</label><input type="text" class="form-control" value="{{ $user->role?->name }}" disabled></div>
            <div class="col-12"><button class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Save Profile</button> <a href="{{ route('admin.password') }}" class="btn btn-soft ms-1"><i class="fa-solid fa-key me-1"></i> Change Password</a></div>
        </div></div></div></div>
        <div class="col-md-4"><div class="a-card"><div class="a-card-body">@include('admin.partials.image-field', ['name' => 'avatar', 'label' => 'Avatar', 'value' => $user->avatar, 'size' => '300x300', 'sizeNote' => 'square'])</div></div></div>
    </div>
</form>
@endsection
