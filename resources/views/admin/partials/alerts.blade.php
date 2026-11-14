@foreach(['success' => 'circle-check', 'error' => 'circle-exclamation', 'warning' => 'triangle-exclamation'] as $type => $icon)
    @if(session($type))
        <div class="alert alert-{{ $type === 'error' ? 'danger' : $type }} alert-dismissible fade show alert-auto border-0 shadow-sm rounded-3" role="alert">
            <i class="fa-solid fa-{{ $icon }} me-2"></i>{{ session($type) }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
@endforeach
@if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm rounded-3">
        <strong><i class="fa-solid fa-circle-exclamation me-1"></i> Please fix the following:</strong>
        <ul class="mb-0 mt-1 small">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif
