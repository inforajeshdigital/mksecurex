@extends('layouts.admin')
@section('title', 'Backup & Maintenance')
@section('crumbs', 'System › Maintenance')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="a-card h-100"><div class="a-card-body text-center">
            <div class="si {{ setting_on('maintenance_mode') ? 'red' : 'green' }} mx-auto mb-3 stat-card border-0 p-0 justify-content-center" style="width:64px;height:64px;font-size:1.5rem;display:grid;place-items:center;border-radius:16px"><i class="fa-solid fa-power-off"></i></div>
            <h3 class="h5">Maintenance Mode</h3>
            <p class="small text-muted">Currently <strong>{{ setting_on('maintenance_mode') ? 'ON — visitors see the maintenance page' : 'OFF — website is live' }}</strong>. Logged-in admins can still browse the site.</p>
            <form action="{{ route('admin.maintenance.toggle') }}" method="POST" data-confirm="Toggle maintenance mode?">@csrf<button class="btn {{ setting_on('maintenance_mode') ? 'btn-success' : 'btn-warning' }}">{{ setting_on('maintenance_mode') ? 'Turn OFF (go live)' : 'Turn ON' }}</button></form>
            <a href="{{ route('admin.settings', 'maintenance') }}" class="d-block small mt-2">Edit maintenance message</a>
        </div></div>
    </div>
    <div class="col-lg-4">
        <div class="a-card h-100"><div class="a-card-body text-center">
            <div class="mx-auto mb-3" style="width:64px;height:64px;font-size:1.5rem;display:grid;place-items:center;border-radius:16px;background:#e8f0ff;color:#0b3d91"><i class="fa-solid fa-database"></i></div>
            <h3 class="h5">Database Backup</h3>
            <p class="small text-muted">Download a full SQL backup of all website content, settings and enquiries. Restore it via phpMyAdmin → Import.</p>
            <form action="{{ route('admin.maintenance.backup') }}" method="POST">@csrf<button class="btn btn-primary"><i class="fa-solid fa-download me-1"></i> Download Backup (.sql)</button></form>
        </div></div>
    </div>
    <div class="col-lg-4">
        <div class="a-card h-100"><div class="a-card-body text-center">
            <div class="mx-auto mb-3" style="width:64px;height:64px;font-size:1.5rem;display:grid;place-items:center;border-radius:16px;background:#fff5dc;color:#c27f00"><i class="fa-solid fa-broom"></i></div>
            <h3 class="h5">Clear Cache</h3>
            <p class="small text-muted">Clears settings, menu, sitemap, view, route and config caches. Use this if a change doesn’t appear on the website.</p>
            <form action="{{ route('admin.maintenance.cache') }}" method="POST">@csrf<button class="btn btn-gold"><i class="fa-solid fa-rotate me-1"></i> Clear All Cache</button></form>
        </div></div>
    </div>
    <div class="col-12">
        <div class="a-card">
            <div class="a-card-head"><h2>System Information</h2></div>
            <div class="table-responsive"><table class="table table-a">
                @foreach($info as $k => $v)<tr><td class="fw-semibold" style="width:260px">{{ $k }}</td><td>{{ $v }}</td></tr>@endforeach
            </table></div>
        </div>
    </div>
</div>
@endsection
