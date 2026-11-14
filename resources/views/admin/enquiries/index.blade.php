@extends('layouts.admin')
@section('title', 'Enquiry Management')
@section('crumbs', 'Leads › Enquiries')

@section('content')
<ul class="nav nav-tabs-a mb-3">
    <li class="nav-item"><a href="{{ route('admin.enquiries.index') }}" class="nav-link {{ !request('status') && !request('spam') ? 'active' : '' }}">All</a></li>
    @foreach(\App\Models\Enquiry::STATUSES as $k => $l)
        <li class="nav-item"><a href="{{ route('admin.enquiries.index', ['status' => $k]) }}" class="nav-link {{ request('status') === $k ? 'active' : '' }}">{{ $l }}</a></li>
    @endforeach
    <li class="nav-item"><a href="{{ route('admin.enquiries.index', ['spam' => 1]) }}" class="nav-link {{ request('spam') ? 'active' : '' }}">Spam ({{ $spamCount }})</a></li>
</ul>
<div class="a-card">
    <div class="a-card-head">
        <form class="row g-2 flex-grow-1" method="GET">
            @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
            @if(request('spam'))<input type="hidden" name="spam" value="1">@endif
            <div class="col-md-3"><input type="search" name="q" class="form-control form-control-sm" value="{{ request('q') }}" placeholder="Name, email, phone, company"></div>
            <div class="col-md-3"><select name="service" class="form-select form-select-sm"><option value="">All services</option>@foreach($services as $s)<option value="{{ $s->id }}" @selected(request('service') == $s->id)>{{ $s->title }}</option>@endforeach</select></div>
            <div class="col-md-2"><input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}" title="From"></div>
            <div class="col-md-2"><input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}" title="To"></div>
            <div class="col-md-2 d-flex gap-1"><button class="btn btn-sm btn-primary flex-fill"><i class="fa-solid fa-filter"></i> Filter</button><a href="{{ route('admin.enquiries.index') }}" class="btn btn-sm btn-light" title="Reset"><i class="fa-solid fa-rotate-left"></i></a></div>
        </form>
        <a href="{{ route('admin.enquiries.export', request()->query()) }}" class="btn btn-sm btn-gold"><i class="fa-solid fa-file-csv me-1"></i> Export CSV / Excel</a>
    </div>
    <form action="{{ route('admin.enquiries.bulk') }}" method="POST" id="bulkForm" data-confirm="Apply this action to the selected enquiries?">
        @csrf
        <div class="px-3 py-2 border-bottom d-flex gap-2 align-items-center small">
            <select name="action" class="form-select form-select-sm" style="width:200px" required>
                <option value="">Bulk action…</option>
                @foreach(\App\Models\Enquiry::STATUSES as $k => $l)<option value="{{ $k }}">Mark as {{ $l }}</option>@endforeach
                <option value="spam">Mark as spam</option><option value="not_spam">Not spam</option><option value="delete">Delete</option>
            </select>
            <button class="btn btn-sm btn-soft">Apply</button>
            <span class="ms-auto text-muted">{{ $enquiries->total() }} enquiries</span>
        </div>
        <div class="table-responsive">
            <table class="table table-a">
                <thead><tr><th style="width:36px"><input type="checkbox" class="form-check-input" data-check-all=".row-check"></th><th>Name</th><th>Contact</th><th>Service</th><th>Location</th><th>Status</th><th>Date</th><th></th></tr></thead>
                <tbody>
                @forelse($enquiries as $e)
                    <tr class="{{ $e->read_at ? '' : 'unread' }}">
                        <td><input type="checkbox" class="form-check-input row-check" name="ids[]" value="{{ $e->id }}"></td>
                        <td><a href="{{ route('admin.enquiries.show', $e) }}" class="text-decoration-none">{{ $e->full_name }}</a>@unless($e->read_at)<span class="badge bg-primary ms-1" style="font-size:.6rem">NEW</span>@endunless<div class="small text-muted">{{ $e->company_name }}</div></td>
                        <td class="small"><a href="{{ tel_link($e->phone) }}">{{ $e->phone }}</a><div><a href="mailto:{{ $e->email }}" class="text-muted">{{ $e->email }}</a></div></td>
                        <td class="small">{{ $e->service_name ?: '—' }}@if($e->workforce_size)<div class="text-muted">{{ $e->workforce_size }}</div>@endif</td>
                        <td class="small">{{ $e->location ?: '—' }}</td>
                        <td><span class="badge-soft {{ $e->is_spam ? 'spam' : $e->status }}">{{ $e->is_spam ? 'Spam' : ucfirst($e->status) }}</span></td>
                        <td class="small text-muted text-nowrap">{{ $e->created_at->format('d M Y') }}<div>{{ $e->created_at->format('h:i A') }}</div></td>
                        <td><a href="{{ route('admin.enquiries.show', $e) }}" class="btn btn-icon btn-soft"><i class="fa-solid fa-eye"></i></a></td>
                    </tr>
                @empty
                    <tr><td colspan="8"><div class="empty-state"><i class="fa-regular fa-envelope d-block"></i>No enquiries found.</div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </form>
    @if($enquiries->hasPages())<div class="p-3 border-top">{{ $enquiries->links() }}</div>@endif
</div>
@endsection
