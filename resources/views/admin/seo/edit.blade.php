@extends('layouts.admin')
@section('title', 'SEO: '.$model->title)
@section('crumbs', 'SEO › '.ucfirst($type).' › '.$model->title)

@section('content')
<form action="{{ route('admin.seo.update', [$type, $model->id]) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="a-card">
        <div class="a-card-head">
            <h2><i class="fa-solid fa-magnifying-glass-chart text-primary me-2"></i>{{ $model->title }}</h2>
            <a href="{{ $url }}" target="_blank" class="btn btn-sm btn-soft"><i class="fa-solid fa-arrow-up-right-from-square me-1"></i> {{ str_replace(url('/'), '', $url) ?: '/' }}</a>
        </div>
        <div class="a-card-body">
            @include('admin.partials.seo-fields', ['model' => $model, 'url' => $url, 'defaultTitle' => $model->title.setting('seo_title_suffix'), 'defaultDesc' => $model->short_description ?? $model->excerpt ?? setting('seo_default_description')])
            <div class="sticky-actions">
                <a href="{{ route('admin.seo.index') }}" class="btn btn-light">Back</a>
                <button class="btn btn-primary px-4"><i class="fa-solid fa-floppy-disk me-1"></i> Save SEO</button>
            </div>
        </div>
    </div>
</form>
@endsection
