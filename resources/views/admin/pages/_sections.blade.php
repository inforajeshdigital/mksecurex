{{-- Page builder section list. Vars: $page --}}
<div class="a-card">
    <div class="a-card-head">
        <h2><i class="fa-solid fa-layer-group text-primary me-2"></i>Page Sections <small class="text-muted fw-normal">— drag to reorder, toggle to enable/disable</small></h2>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.pages.preview', $page) }}" target="_blank" class="btn btn-sm btn-soft"><i class="fa-solid fa-eye me-1"></i> Preview</a>
            <button class="btn btn-sm btn-gold" data-bs-toggle="collapse" data-bs-target="#addSection"><i class="fa-solid fa-plus me-1"></i> Add Section</button>
        </div>
    </div>
    <div class="a-card-body">
        <div class="collapse mb-4" id="addSection">
            <div class="p-3 rounded-3" style="background:#f7f9fd">
                <div class="fw-semibold mb-2">Choose a section type</div>
                <div class="type-grid">
                    @foreach(config('sections.types') as $type => $t)
                        <a href="{{ route('admin.sections.create', [$page, 'type' => $type]) }}"><i class="fa-solid {{ $t['icon'] }}"></i>{{ $t['label'] }}</a>
                    @endforeach
                </div>
            </div>
        </div>
        @if($page->sections->isEmpty())
            <div class="empty-state"><i class="fa-solid fa-layer-group d-block"></i>No sections yet. Click “Add Section”.</div>
        @endif
        <ul class="section-list" data-sortable="sections">
            @foreach($page->sections as $s)
                <li class="section-item {{ $s->is_active ? '' : 'off' }}" data-id="{{ $s->id }}">
                    <span class="drag-handle"><i class="fa-solid fa-grip-vertical"></i></span>
                    <span class="si-icon"><i class="fa-solid {{ config("sections.types.{$s->type}.icon", 'fa-square') }}"></i></span>
                    <div class="meta">
                        <strong>{{ $s->name ?: $s->label }}</strong>
                        <small>{{ $s->label }} @if($s->get('heading')) · {{ \Illuminate\Support\Str::limit($s->get('heading'), 80) }} @endif</small>
                    </div>
                    <div class="form-check form-switch mb-0" title="Enable / disable"><input class="form-check-input" type="checkbox" data-toggle-url="{{ route('admin.sections.toggle', $s) }}" @checked($s->is_active)></div>
                    <a href="{{ route('admin.sections.edit', $s) }}" class="btn btn-icon btn-soft" title="Edit"><i class="fa-solid fa-pen"></i></a>
                    <form action="{{ route('admin.sections.duplicate', $s) }}" method="POST" class="d-inline">@csrf<button class="btn btn-icon btn-soft" title="Duplicate"><i class="fa-regular fa-copy"></i></button></form>
                    <form action="{{ route('admin.sections.destroy', $s) }}" method="POST" class="d-inline" data-confirm="Delete this section?">@csrf @method('DELETE')<button class="btn btn-icon btn-soft-danger" title="Delete"><i class="fa-solid fa-trash"></i></button></form>
                </li>
            @endforeach
        </ul>
    </div>
</div>
