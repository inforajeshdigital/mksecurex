@extends('layouts.admin')
@section('title', 'Change Password')
@section('crumbs', 'Account › Password')

@section('content')
<form action="{{ route('admin.password.update') }}" method="POST">
    @csrf @method('PUT')
    <div class="a-card" style="max-width:520px"><div class="a-card-body">
        <div class="mb-3"><label class="form-label">Current Password</label><input type="password" name="current_password" class="form-control" required autocomplete="current-password"></div>
        <div class="mb-3"><label class="form-label">New Password</label><input type="password" name="password" class="form-control" required autocomplete="new-password"><div class="form-text">Min 8 characters with letters and numbers.</div></div>
        <div class="mb-3"><label class="form-label">Confirm New Password</label><input type="password" name="password_confirmation" class="form-control" required autocomplete="new-password"></div>
        <button class="btn btn-primary w-100"><i class="fa-solid fa-key me-1"></i> Update Password</button>
    </div></div>
</form>
@endsection
