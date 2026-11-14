@extends('layouts.admin')
@section('title', 'SEO Management')
@section('crumbs', 'SEO › All pages')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4"><a href="{{ route('admin.settings', 'seo') }}" class="stat-card"><div class="si blue"><i class="fa-solid fa-sliders"></i></div><div><div class="fw-bold">SEO Defaults</div><div class="sl">Title suffix, default description, schema</div></div></a></div>
    <div class="col-md-4"><a href="{{ route('admin.seo.robots') }}" class="stat-card"><div class="si gold"><i class="fa-solid fa-robot"></i></div><div><div class="fw-bold">Robots.txt</div><div class="sl">Crawler rules</div></div></a></div>
    <div class="col-md-4"><a href="{{ route('admin.seo.sitemap') }}" class="stat-card"><div class="si green"><i class="fa-solid fa-sitemap"></i></div><div><div class="fw-bold">XML Sitemap</div><div class="sl">Auto-generated</div></div></a></div>
</div>
<div class="a-card">
    <div class="a-card-head"><h2>Per-page SEO</h2><span class="help">Every page, service and industry has independent SEO settings.</span></div>
    <div class="table-responsive">
        <table class="table table-a">
            <thead><tr><th>Page</th><th>Type</th><th>Meta Title</th><th>Description</th><th>Robots</th><th class="text-end"></th></tr></thead>
            <tbody>
            @foreach($items as $it)
                @php($seo = $it['model']->seo)
                @php($tl = mb_strlen($seo?->meta_title ?? ''))
                @php($dl = mb_strlen($seo?->meta_description ?? ''))
                <tr>
                    <td><strong>{{ $it['model']->title }}</strong><div class="small"><a href="{{ $it['url'] }}" target="_blank" class="text-muted">{{ str_replace(url('/'), '', $it['url']) ?: '/' }}</a></div></td>
                    <td><span class="badge bg-light text-dark border">{{ ucfirst($it['type']) }}</span></td>
                    <td class="small" style="max-width:280px">@if($seo?->meta_title){{ $seo->meta_title }} <span class="{{ $tl > 65 ? 'text-danger' : 'text-success' }}">({{ $tl }})</span>@else<span class="text-warning"><i class="fa-solid fa-circle-exclamation"></i> Not set</span>@endif</td>
                    <td class="small">@if($dl)<span class="{{ $dl > 160 || $dl < 70 ? 'text-warning' : 'text-success' }}">{{ $dl }} chars</span>@else<span class="text-warning">Not set</span>@endif</td>
                    <td class="small">{{ ($seo?->robots_index ?? true) ? 'index' : 'noindex' }}, {{ ($seo?->robots_follow ?? true) ? 'follow' : 'nofollow' }}</td>
                    <td class="text-end"><a href="{{ route('admin.seo.edit', [$it['type'], $it['model']->id]) }}" class="btn btn-sm btn-soft"><i class="fa-solid fa-pen me-1"></i> Edit SEO</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
