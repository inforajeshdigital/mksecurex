@extends('layouts.admin')
@section('title', 'Navigation Management')
@section('crumbs', 'Appearance › Navigation')

@section('content')
@php($menu = $menus[$active])
<ul class="nav nav-tabs-a mb-4">
    @foreach(\App\Models\NavigationMenu::LOCATIONS as $loc => $name)
        <li class="nav-item"><a href="{{ route('admin.menus.index', ['menu' => $loc]) }}" class="nav-link {{ $active === $loc ? 'active' : '' }}">{{ $name }}</a></li>
    @endforeach
</ul>
<div class="row g-4">
    <div class="col-xl-7">
        <div class="a-card">
            <div class="a-card-head"><h2>{{ $menu->name }}</h2><span class="help">Drag to reorder</span></div>
            <div class="a-card-body">
                @if($menu->items->isEmpty())<div class="empty-state">No items yet.</div>@endif
                <ul class="section-list" data-sortable="menu_items">
                    @foreach($menu->items as $item)
                        @include('admin.menus._item', ['item' => $item, 'child' => false])
                    @endforeach
                </ul>
                @foreach($menu->items as $item)
                    @if($item->children->isNotEmpty())
                        <div class="small text-muted mt-3 mb-1">Sub-items of “{{ $item->label }}”</div>
                        <ul class="section-list ms-4" data-sortable="menu_items">
                            @foreach($item->children as $c)@include('admin.menus._item', ['item' => $c, 'child' => true])@endforeach
                        </ul>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-xl-5">
        <form action="{{ route('admin.menus.items.store', $menu) }}" method="POST" class="a-card">
            @csrf
            <div class="a-card-head"><h2><i class="fa-solid fa-plus text-primary me-2"></i>Add Menu Item</h2></div>
            <div class="a-card-body">
                @include('admin.menus._fields', ['item' => new \App\Models\NavigationItem(['is_active' => true, 'type' => 'custom']), 'menu' => $menu])
                <button class="btn btn-primary w-100"><i class="fa-solid fa-plus me-1"></i> Add Item</button>
            </div>
        </form>
        <div class="alert alert-light border small"><i class="fa-solid fa-circle-info me-1"></i> “Services / Industries dropdown” items automatically list all published services or industries. Header CTA button is managed in <a href="{{ route('admin.settings', 'header') }}">Header settings</a>.</div>
    </div>
</div>

@foreach($menu->items->merge($menu->items->flatMap->children) as $item)
<div class="modal fade" id="mi{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.menus.items.update', $item) }}" method="POST" class="modal-content border-0 rounded-4">
            @csrf @method('PUT')
            <div class="modal-header"><h5 class="modal-title">Edit “{{ $item->label }}”</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">@include('admin.menus._fields', ['item' => $item, 'menu' => $menu])</div>
            <div class="modal-footer"><button class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Save</button></div>
        </form>
    </div>
</div>
@endforeach
@endsection
