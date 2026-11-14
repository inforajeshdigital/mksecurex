@extends('layouts.admin')
@section('title', $def['label'])
@section('crumbs', 'Settings › '.$def['label'])

@section('content')
<form action="{{ route('admin.settings.update', $group) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="a-card">
        <div class="a-card-head">
            <h2><i class="fa-solid {{ $def['icon'] }} text-primary me-2"></i>{{ $def['label'] }}</h2>
            @if($group === 'floating')<span class="help">Floating buttons appear on every public page.</span>@endif
            @if($group === 'header')<a href="{{ route('admin.menus.index') }}" class="btn btn-sm btn-soft"><i class="fa-solid fa-bars me-1"></i> Edit navigation links</a>@endif
            @if($group === 'footer')<a href="{{ route('admin.menus.index', ['menu' => 'footer_quick']) }}" class="btn btn-sm btn-soft"><i class="fa-solid fa-link me-1"></i> Edit footer links</a>@endif
        </div>
        <div class="a-card-body">
            <div class="row g-4">
                @foreach($def['fields'] as $key => $f)
                    @php($val = old($key, setting($key)))
                    @php($wide = in_array($f['type'], ['textarea', 'code', 'richtext']))
                    <div class="{{ $wide ? 'col-12' : ($f['type'] === 'image' ? 'col-md-6 col-xl-4' : 'col-md-6') }}">
                        @switch($f['type'])
                            @case('image')
                                @include('admin.partials.image-field', ['name' => $key, 'label' => $f['label'], 'value' => setting($key), 'help' => $f['help'] ?? null])
                                @break
                            @case('toggle')
                                <div class="form-check form-switch mt-md-4 pt-md-2">
                                    <input class="form-check-input" type="checkbox" role="switch" name="{{ $key }}" value="1" id="s_{{ $key }}" @checked($val === '1' || $val === 1 || $val === true)>
                                    <label class="form-check-label fw-semibold" for="s_{{ $key }}">{!! $f['label'] !!}</label>
                                </div>
                                @break
                            @case('textarea')
                                <label class="form-label" for="s_{{ $key }}">{!! $f['label'] !!}</label>
                                <textarea name="{{ $key }}" id="s_{{ $key }}" class="form-control" rows="3">{{ $val }}</textarea>
                                @break
                            @case('code')
                                <label class="form-label" for="s_{{ $key }}">{!! $f['label'] !!}</label>
                                <textarea name="{{ $key }}" id="s_{{ $key }}" class="form-control code-area" rows="4" spellcheck="false">{{ $val }}</textarea>
                                @break
                            @case('color')
                                <label class="form-label" for="s_{{ $key }}">{!! $f['label'] !!}</label>
                                <div class="input-group"><input type="color" class="form-control form-control-color" value="{{ $val }}" oninput="this.nextElementSibling.value=this.value"><input type="text" name="{{ $key }}" id="s_{{ $key }}" class="form-control" value="{{ $val }}" pattern="#[0-9a-fA-F]{6}" oninput="if(/^#[0-9a-f]{6}$/i.test(this.value))this.previousElementSibling.value=this.value"></div>
                                @break
                            @case('select')
                                <label class="form-label" for="s_{{ $key }}">{!! $f['label'] !!}</label>
                                <select name="{{ $key }}" id="s_{{ $key }}" class="form-select">@foreach($f['options'] as $ov => $ol)<option value="{{ $ov }}" @selected($val == $ov)>{{ $ol }}</option>@endforeach</select>
                                @break
                            @default
                                <label class="form-label" for="s_{{ $key }}">{!! $f['label'] !!}</label>
                                <input type="{{ in_array($f['type'], ['email', 'url', 'number']) ? $f['type'] : 'text' }}" name="{{ $key }}" id="s_{{ $key }}" class="form-control" value="{{ $val }}">
                        @endswitch
                        @if(!empty($f['help']) && $f['type'] !== 'image')<div class="form-text">{{ $f['help'] }}</div>@endif
                    </div>
                @endforeach
            </div>
            @if($group === 'floating' && wa_link())
                <div class="alert alert-light border mt-4 small mb-0"><i class="fa-brands fa-whatsapp text-success me-1"></i> Current WhatsApp link: <a href="{{ wa_link() }}" target="_blank" rel="noopener">{{ wa_link() }}</a></div>
            @endif
            <div class="sticky-actions mt-4">
                <button class="btn btn-primary px-4"><i class="fa-solid fa-floppy-disk me-1"></i> Save Changes</button>
            </div>
        </div>
    </div>
</form>
@endsection
