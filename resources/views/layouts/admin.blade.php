<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Dashboard') · {{ setting('site_name') }} Admin</title>
    <link rel="icon" href="{{ img_url(setting('site_favicon')) }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/admin.css') }}?v={{ filemtime(public_path('assets/css/admin.css')) }}" rel="stylesheet">
    @stack('head')
</head>
<body>
@php $user = auth()->user(); @endphp
<aside class="a-sidebar">
    <div class="a-brand">
        <img src="{{ img_url(setting('site_logo')) }}" alt="">
        <div><strong>{{ \Illuminate\Support\Str::limit(setting('site_name'), 22) }}</strong><small>Admin Panel</small></div>
    </div>
    <nav class="a-nav">
        @foreach(config('admin.menu') as $m)
            @if(isset($m['heading']))
                <div class="heading">{{ $m['heading'] }}</div>
            @elseif($user->canAccess($m['perm']))
                @php
                    $active = isset($m['match'])
                        ? request()->routeIs('admin.settings*') && request()->route('group') === $m['match']
                        : request()->routeIs(...($m['active'] ?? [$m['route'].'*', str_replace('.index', '', $m['route']).'.*']));
                @endphp
                <a href="{{ route($m['route'], $m['params'] ?? []) }}" class="{{ $active ? 'active' : '' }}">
                    <i class="fa-solid {{ $m['icon'] }}"></i> {{ $m['label'] }}
                    @if(($m['badge'] ?? null) === 'enquiries' && $newEnquiries)<span class="badge rounded-pill">{{ $newEnquiries }}</span>@endif
                </a>
            @endif
        @endforeach
    </nav>
    <div class="a-side-foot d-flex justify-content-between">
        <a href="{{ url('/') }}" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square me-1"></i> View Website</a>
        <span class="text-white-50">v1.0</span>
    </div>
</aside>
<div class="a-backdrop"></div>

<div class="a-main">
    <header class="a-topbar">
        <button class="icon-btn d-lg-none" data-sidebar-toggle aria-label="Menu"><i class="fa-solid fa-bars"></i></button>
        <div class="me-auto min-w-0">
            <h1 class="page-title text-truncate">@yield('title', 'Dashboard')</h1>
            <div class="crumbs d-none d-sm-block">@yield('crumbs', 'Admin')</div>
        </div>
        @if($user->canAccess('enquiries'))
            <a href="{{ route('admin.enquiries.index', ['status' => 'new']) }}" class="icon-btn" title="New enquiries"><i class="fa-regular fa-bell"></i>@if($newEnquiries)<span class="dot">{{ $newEnquiries }}</span>@endif</a>
        @endif
        <a href="{{ url('/') }}" target="_blank" class="icon-btn d-none d-sm-grid" title="View website"><i class="fa-solid fa-globe"></i></a>
        <div class="dropdown">
            <button class="a-user dropdown-toggle border" data-bs-toggle="dropdown">
                <span class="a-avatar">@if($user->avatar)<img src="{{ img_url($user->avatar) }}" alt="">@else{{ strtoupper(mb_substr($user->name, 0, 1)) }}@endif</span>
                <span class="d-none d-md-inline text-start lh-sm"><strong class="d-block small">{{ $user->name }}</strong><small class="text-muted">{{ $user->role?->name }}</small></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                <li><a class="dropdown-item" href="{{ route('admin.profile') }}"><i class="fa-regular fa-user me-2"></i>My Profile</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.password') }}"><i class="fa-solid fa-key me-2"></i>Change Password</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><form action="{{ route('admin.logout') }}" method="POST">@csrf<button class="dropdown-item text-danger"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</button></form></li>
            </ul>
        </div>
    </header>

    <main class="a-content">
        @if(!empty($pendingMigrations) && $user->canAccess('maintenance'))
            <div class="alert alert-warning border-0 shadow-sm rounded-3 d-flex flex-wrap align-items-center gap-2">
                <i class="fa-solid fa-database"></i>
                <span class="me-auto"><strong>Database update available</strong> — a new website update needs {{ $pendingMigrations }} database change(s). Some new options won’t save until you apply it.</span>
                <form action="{{ route('admin.maintenance.migrate') }}" method="POST">@csrf<button class="btn btn-sm btn-primary"><i class="fa-solid fa-rotate me-1"></i> Run Database Updates</button></form>
            </div>
        @endif
        @include('admin.partials.alerts')
        @yield('content')
    </main>
    <footer class="a-footer">© {{ date('Y') }} {{ setting('site_name') }} · Admin Panel</footer>
</div>

{{-- Media picker modal (used by image fields) --}}
<div class="modal fade" id="mediaPicker" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header">
                <h5 class="modal-title">Choose from Media Library</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex gap-2 mb-3 flex-wrap">
                    <input type="search" class="form-control" style="max-width:300px" placeholder="Search images…" data-picker-search>
                    <label class="btn btn-gold mb-0"><i class="fa-solid fa-upload me-1"></i> Upload new<input type="file" accept="image/*" multiple hidden data-picker-upload></label>
                </div>
                <div class="media-grid"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
<script>
    window.ADMIN = {
        reorderUrl: @json(route('admin.reorder')),
        pickerUrl: @json(auth()->user()->canAccess('media') ? route('admin.media.picker') : ''),
        mediaStoreUrl: @json(auth()->user()->canAccess('media') ? route('admin.media.store') : ''),
    };
</script>
<script src="{{ asset('assets/js/admin.js') }}?v={{ filemtime(public_path('assets/js/admin.js')) }}"></script>
@stack('scripts')
</body>
</html>
