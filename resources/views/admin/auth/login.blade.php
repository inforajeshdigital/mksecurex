<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin Login · {{ setting('site_name') }}</title>
    <link rel="icon" href="{{ img_url(setting('site_favicon')) }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/admin.css') }}" rel="stylesheet">
</head>
<body>
<div class="login-wrap">
    <div class="login-art">
        <div><img src="{{ img_url(setting('site_logo')) }}" alt="{{ setting('site_name') }}" style="height:70px;background:#fff;border-radius:14px;padding:6px 12px"></div>
        <div>
            <h1 class="mb-3">Manage your website<br>with confidence.</h1>
            <p class="mb-4" style="max-width:440px">Update pages, services, industries, SEO and enquiries — all from one secure dashboard.</p>
            <div class="d-flex gap-4 small">
                <span><i class="fa-solid fa-shield-halved text-warning me-1"></i> Secure access</span>
                <span><i class="fa-solid fa-bolt text-warning me-1"></i> Fully dynamic</span>
                <span><i class="fa-solid fa-chart-line text-warning me-1"></i> SEO ready</span>
            </div>
        </div>
        <div class="small text-white-50">© {{ date('Y') }} {{ setting('site_name') }}</div>
    </div>
    <div class="login-form">
        <div class="box">
            <div class="d-lg-none text-center mb-4"><img src="{{ img_url(setting('site_logo')) }}" alt="" style="height:70px"></div>
            <h2 class="mb-1">Welcome back 👋</h2>
            <p class="text-muted mb-4">Sign in to the admin panel</p>
            @if($errors->any())<div class="alert alert-danger py-2 small"><i class="fa-solid fa-circle-exclamation me-1"></i>{{ $errors->first() }}</div>@endif
            <form method="POST" action="{{ route('admin.login.submit') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="email">Email address</label>
                    <div class="position-relative"><i class="fa-regular fa-envelope ic"></i><input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus autocomplete="username"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Password</label>
                    <div class="position-relative"><i class="fa-solid fa-lock ic"></i><input type="password" id="password" name="password" class="form-control" required autocomplete="current-password">
                        <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-muted" onclick="const p=document.getElementById('password');p.type=p.type==='password'?'text':'password'"><i class="fa-regular fa-eye"></i></button></div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check"><input class="form-check-input" type="checkbox" name="remember" id="remember"><label class="form-check-label small" for="remember">Remember me</label></div>
                </div>
                <button class="btn btn-primary w-100 py-2"><i class="fa-solid fa-right-to-bracket me-1"></i> Sign In</button>
            </form>
            <p class="text-center mt-4 small"><a href="{{ url('/') }}" class="text-decoration-none"><i class="fa-solid fa-arrow-left me-1"></i> Back to website</a></p>
        </div>
    </div>
</div>
</body>
</html>
