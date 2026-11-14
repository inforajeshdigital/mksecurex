@extends('layouts.admin')
@section('title', 'Robots.txt Management')
@section('crumbs', 'SEO › Robots.txt')

@section('content')
<div class="row g-4">
    <div class="col-xl-7">
        <form action="{{ route('admin.seo.robots.save') }}" method="POST" class="a-card">
            @csrf @method('PUT')
            <div class="a-card-head"><h2>Edit robots.txt</h2><a href="{{ route('robots') }}" target="_blank" class="btn btn-sm btn-soft"><i class="fa-solid fa-eye me-1"></i> View live</a></div>
            <div class="a-card-body">
                <textarea name="content" rows="14" class="form-control code-area" spellcheck="false" id="robotsTxt">{{ old('content', $content) }}</textarea>
                <div class="form-text"><code>{sitemap_url}</code> is replaced with {{ route('sitemap') }}. Review directives carefully — a wrong <code>Disallow: /</code> hides the whole site from search engines.</div>
                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Save</button>
                    <button class="btn btn-light" name="restore" value="1" onclick="return confirm('Restore the default robots.txt?')"><i class="fa-solid fa-rotate-left me-1"></i> Restore Defaults</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-xl-5">
        <div class="a-card">
            <div class="a-card-head"><h2>Live Preview</h2></div>
            <div class="a-card-body"><pre class="p-3 rounded-3 mb-0 small" style="background:#f7f9fd" id="robotsPreview"></pre></div>
        </div>
        <div class="alert alert-info small"><i class="fa-solid fa-circle-info me-1"></i> Admin paths are blocked by default. Never list private file locations here — robots.txt is public.</div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    const rt = document.getElementById('robotsTxt'), rp = document.getElementById('robotsPreview');
    const upd = () => rp.textContent = rt.value.replaceAll('{sitemap_url}', @json(route('sitemap')));
    rt.addEventListener('input', upd); upd();
</script>
@endpush
