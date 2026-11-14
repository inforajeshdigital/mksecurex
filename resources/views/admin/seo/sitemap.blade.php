@extends('layouts.admin')
@section('title', 'Sitemap Management')
@section('crumbs', 'SEO › XML Sitemap')

@section('content')
<div class="a-card">
    <div class="a-card-head">
        <h2>{{ count($urls) }} URLs in sitemap.xml</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('sitemap') }}" target="_blank" class="btn btn-sm btn-soft"><i class="fa-solid fa-eye me-1"></i> View sitemap.xml</a>
            <form action="{{ route('admin.seo.sitemap.refresh') }}" method="POST">@csrf<button class="btn btn-sm btn-primary"><i class="fa-solid fa-rotate me-1"></i> Regenerate</button></form>
        </div>
    </div>
    <div class="p-3 small text-muted border-bottom"><i class="fa-solid fa-circle-info me-1"></i> The sitemap updates automatically when pages, services or industries are created, edited, published or deleted. Drafts, noindex pages, admin/login URLs and pages canonicalised elsewhere are excluded. Submit <code>{{ route('sitemap') }}</code> in Google Search Console.</div>
    <div class="table-responsive">
        <table class="table table-a">
            <thead><tr><th>URL</th><th>Last Modified</th><th>Priority</th></tr></thead>
            <tbody>
            @foreach($urls as $u)
                <tr><td class="small"><a href="{{ $u['loc'] }}" target="_blank">{{ $u['loc'] }}</a></td><td class="small text-muted">{{ $u['lastmod'] ? \Carbon\Carbon::parse($u['lastmod'])->format('d M Y H:i') : '—' }}</td><td>{{ $u['priority'] }}</td></tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
