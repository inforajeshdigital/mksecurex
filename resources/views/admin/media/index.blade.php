@extends('layouts.admin')
@section('title', 'Media Library')
@section('crumbs', 'Content › Media')

@section('content')
<div class="a-card">
    <div class="a-card-body">
        <form action="{{ route('admin.media.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="dropzone">
                <i class="fa-solid fa-cloud-arrow-up fa-2x text-primary mb-2"></i>
                <div class="fw-semibold">Drop images here or click to upload</div>
                <div class="small text-muted">JPG, PNG, WebP, GIF · max 4 MB each · up to 20 files · JPG/PNG auto-optimised to WebP</div>
                <div class="small mt-2" style="color:#7a5200"><i class="fa-solid fa-ruler-combined me-1"></i> Size guide: Hero slide 1920×1080 · Page/Service banner 1920×600 · Service card 1200×750 · Industry card 1200×800 · Text+Image 1200×1000 · Gallery 1200×900 · Logo 400×150 · Social share 1200×630 · Photo 300×300</div>
                <input type="file" name="files[]" accept=".jpg,.jpeg,.png,.webp,.gif,.ico" multiple hidden>
            </div>
        </form>
    </div>
</div>

<div class="a-card">
    <div class="a-card-head">
        <h2>{{ $media->total() }} files</h2>
        <form method="GET"><input type="search" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Search title / alt / file" style="width:240px"></form>
    </div>
    <div class="a-card-body">
        @if($media->isEmpty())
            <div class="empty-state"><i class="fa-regular fa-images d-block"></i>No media yet.</div>
        @else
            <div class="media-grid">
                @foreach($media as $m)
                    <div class="media-item" data-bs-toggle="modal" data-bs-target="#m{{ $m->id }}">
                        @unless($m->alt)<span class="no-alt">NO ALT</span>@endunless
                        <div class="mi-img" style="background-image:url('{{ $m->url }}')"></div>
                        <div class="mi-cap">{{ $m->title ?: $m->file_name }}</div>
                    </div>
                @endforeach
            </div>
            <div class="mt-3">{{ $media->links() }}</div>
        @endif
    </div>
</div>

@foreach($media as $m)
<div class="modal fade" id="m{{ $m->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header"><h5 class="modal-title text-truncate">{{ $m->file_name }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="row g-4">
                    <div class="col-md-5">
                        <img src="{{ $m->url }}" alt="{{ $m->alt }}" class="w-100 rounded-3 border" style="max-height:260px;object-fit:contain;background:#f7f9fd">
                        <div class="small text-muted mt-2">
                            {{ $m->width && $m->height ? $m->width.'×'.$m->height.' · ' : '' }}{{ $m->human_size }} · {{ $m->created_at->format('d M Y') }}
                        </div>
                        <div class="input-group input-group-sm mt-2"><input type="text" class="form-control" value="{{ $m->path }}" readonly onclick="this.select()"><button class="btn btn-soft" type="button" onclick="navigator.clipboard.writeText('{{ $m->url }}');adminToast('URL copied')"><i class="fa-regular fa-copy"></i></button></div>
                    </div>
                    <div class="col-md-7">
                        <form action="{{ route('admin.media.update', $m) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="mb-2"><label class="form-label">Title</label><input type="text" name="title" class="form-control" value="{{ $m->title }}"></div>
                            <div class="mb-2"><label class="form-label">Alt Text <small class="text-muted">(describe the image for SEO & accessibility)</small></label><input type="text" name="alt" class="form-control" value="{{ $m->alt }}"></div>
                            <div class="mb-2"><label class="form-label">Caption</label><input type="text" name="caption" class="form-control" value="{{ $m->caption }}"></div>
                            <div class="mb-3"><label class="form-label">Description</label><textarea name="description" rows="2" class="form-control">{{ $m->description }}</textarea></div>
                            <button class="btn btn-primary btn-sm"><i class="fa-solid fa-floppy-disk me-1"></i> Save Details</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <form action="{{ route('admin.media.destroy', $m) }}" method="POST" data-confirm="Delete this file permanently? Pages using it will show no image.">@csrf @method('DELETE')<button class="btn btn-soft-danger btn-sm"><i class="fa-solid fa-trash me-1"></i> Delete File</button></form>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
