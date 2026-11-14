<div class="row g-4">
    @foreach($industries as $in)
        <div class="col-sm-6 col-lg-3 reveal d{{ $loop->index % 4 }}">
            <a href="{{ $in->url }}" class="industry-card">
                @if($in->image)<img src="{{ img_url($in->image) }}" alt="{{ $in->image_alt ?: $in->title }}" loading="lazy" width="320" height="300">@endif
                <i class="fa-solid {{ $in->icon ?: 'fa-building' }} ph-icon" aria-hidden="true"></i>
                <div class="content">
                    <h3>{{ $in->title }}</h3>
                    <p>{{ $in->short_description }}</p>
                    <span class="go">Explore Industry <i class="fa-solid fa-arrow-right ms-1"></i></span>
                </div>
            </a>
        </div>
    @endforeach
</div>
