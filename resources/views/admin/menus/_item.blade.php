<li class="section-item {{ $item->is_active ? '' : 'off' }}" data-id="{{ $item->id }}">
    <span class="drag-handle"><i class="fa-solid fa-grip-vertical"></i></span>
    <span class="si-icon"><i class="fa-solid {{ $item->type === 'custom' ? 'fa-link' : 'fa-list' }}"></i></span>
    <div class="meta"><strong>{{ $item->label }}</strong><small>{{ $item->url ?: '#' }} · {{ \App\Models\NavigationItem::TYPES[$item->type] ?? $item->type }}@if($item->new_tab) · new tab @endif</small></div>
    <button type="button" class="btn btn-icon btn-soft" data-bs-toggle="modal" data-bs-target="#mi{{ $item->id }}"><i class="fa-solid fa-pen"></i></button>
    <form action="{{ route('admin.menus.items.destroy', $item) }}" method="POST" class="d-inline" data-confirm="Delete this menu item{{ $child ? '' : ' and its sub-items' }}?">@csrf @method('DELETE')<button class="btn btn-icon btn-soft-danger"><i class="fa-solid fa-trash"></i></button></form>
</li>
