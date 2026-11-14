@extends('layouts.admin')
@section('title', 'Dashboard')
@section('crumbs', 'Overview of your website')

@section('content')
<div class="welcome">
    <div class="row align-items-center g-3 position-relative" style="z-index:1">
        <div class="col-md-8">
            <h2 class="mb-1">Welcome back, {{ auth()->user()->name }}!</h2>
            <p class="mb-0">Here’s what’s happening with {{ setting('site_name') }} today.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ url('/') }}" target="_blank" class="btn btn-gold"><i class="fa-solid fa-globe me-1"></i> View Website</a>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    @foreach([
        ['Total Enquiries', $stats['total'], 'fa-envelope-open-text', 'blue', route('admin.enquiries.index')],
        ['New Enquiries', $stats['new'], 'fa-bell', 'gold', route('admin.enquiries.index', ['status' => 'new'])],
        ['Contacted / Converted', $stats['contacted'], 'fa-handshake', 'green', route('admin.enquiries.index', ['status' => 'contacted'])],
        ['Total Services', $stats['services'], 'fa-briefcase', 'purple', route('admin.services.index')],
        ['Total Industries', $stats['industries'], 'fa-industry', 'cyan', route('admin.industries.index')],
        ['Published Pages', $stats['pages'], 'fa-file-lines', 'red', route('admin.pages.index')],
    ] as [$label, $val, $icon, $color, $link])
        <div class="col-6 col-md-4 col-xl-2">
            <a href="{{ $link }}" class="stat-card flex-column align-items-start">
                <div class="si {{ $color }}"><i class="fa-solid {{ $icon }}"></i></div>
                <div><div class="sv">{{ number_format($val) }}</div><div class="sl">{{ $label }}</div></div>
            </a>
        </div>
    @endforeach
</div>

<div class="row g-4">
    <div class="col-xl-8">
        <div class="a-card">
            <div class="a-card-head"><h2><i class="fa-solid fa-chart-column text-primary me-2"></i>Enquiries – last 14 days</h2></div>
            <div class="a-card-body">
                @php($max = max(1, $chart->max('value')))
                <div class="chart-bars">@foreach($chart as $c)<div class="b" style="height:{{ max(2, $c['value'] / $max * 100) }}%" title="{{ $c['label'] }}: {{ $c['value'] }}">@if($c['value'])<span>{{ $c['value'] }}</span>@endif</div>@endforeach</div>
                <div class="chart-labels">@foreach($chart as $c)<span>{{ $c['label'] }}</span>@endforeach</div>
            </div>
        </div>
        <div class="a-card">
            <div class="a-card-head">
                <h2><i class="fa-solid fa-inbox text-primary me-2"></i>Recent Enquiries</h2>
                <a href="{{ route('admin.enquiries.index') }}" class="btn btn-sm btn-soft">View all</a>
            </div>
            <div class="table-responsive">
                <table class="table table-a">
                    <thead><tr><th>Name</th><th>Service</th><th>Phone</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody>
                    @forelse($recent as $e)
                        <tr class="{{ $e->read_at ? '' : 'unread' }}">
                            <td><a href="{{ route('admin.enquiries.show', $e) }}" class="text-decoration-none">{{ $e->full_name }}</a><div class="small text-muted">{{ $e->company_name }}</div></td>
                            <td class="small">{{ $e->service_name ?: '—' }}</td>
                            <td class="small">{{ $e->phone }}</td>
                            <td><span class="badge-soft {{ $e->status }}">{{ ucfirst($e->status) }}</span></td>
                            <td class="small text-muted">{{ $e->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="empty-state"><i class="fa-regular fa-envelope d-block"></i>No enquiries yet.</div></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="a-card">
            <div class="a-card-head"><h2><i class="fa-solid fa-bolt text-warning me-2"></i>Quick Actions</h2></div>
            <div class="a-card-body d-grid gap-2">
                @if(admin_can('homepage'))<a href="{{ route('admin.homepage') }}" class="btn btn-soft text-start"><i class="fa-solid fa-house me-2"></i>Edit Homepage</a>@endif
                @if(admin_can('services'))<a href="{{ route('admin.services.create') }}" class="btn btn-soft text-start"><i class="fa-solid fa-plus me-2"></i>Add Service</a>@endif
                @if(admin_can('media'))<a href="{{ route('admin.media.index') }}" class="btn btn-soft text-start"><i class="fa-solid fa-upload me-2"></i>Upload Images</a>@endif
                @if(admin_can('contact'))<a href="{{ route('admin.settings', 'contact') }}" class="btn btn-soft text-start"><i class="fa-solid fa-address-book me-2"></i>Update Contact Details</a>@endif
                @if(admin_can('seo'))<a href="{{ route('admin.seo.index') }}" class="btn btn-soft text-start"><i class="fa-solid fa-magnifying-glass-chart me-2"></i>SEO Management</a>@endif
            </div>
        </div>
        <div class="a-card">
            <div class="a-card-head"><h2><i class="fa-solid fa-clock-rotate-left text-primary me-2"></i>Recent Activity</h2></div>
            <div class="a-card-body">
                @forelse($activity as $a)
                    <div class="d-flex gap-2 mb-3 small">
                        <span class="a-avatar flex-shrink-0" style="width:28px;height:28px;font-size:.7rem">{{ strtoupper(mb_substr($a->user->name ?? 'S', 0, 1)) }}</span>
                        <div><div>{{ $a->description }}</div><div class="text-muted" style="font-size:.74rem">{{ $a->user->name ?? 'System' }} · {{ $a->created_at->diffForHumans() }}</div></div>
                    </div>
                @empty
                    <div class="text-muted small">No activity yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
