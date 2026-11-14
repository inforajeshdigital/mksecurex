<div class="row g-4">
    @foreach($services as $sv)
        <div class="col-md-6 col-lg-4 reveal d{{ $loop->index % 3 }}">
            <article class="card-mk service-card">
                <a href="{{ $sv->url }}" class="thumb d-block" tabindex="-1" aria-hidden="true">
                    @if($sv->image)
                        <img src="{{ img_url($sv->image) }}" alt="{{ $sv->image_alt ?: $sv->title }}" loading="lazy" width="400" height="250">
                    @else
                        <span class="ph"><i class="fa-solid {{ $sv->icon ?: 'fa-briefcase' }}"></i></span>
                    @endif
                    <span class="num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="float-icon"><i class="fa-solid {{ $sv->icon ?: 'fa-briefcase' }}"></i></span>
                </a>
                <div class="body">
                    <h3><a href="{{ $sv->url }}" class="text-reset">{{ $sv->title }}</a></h3>
                    <p>{{ $sv->short_description }}</p>
                    <div class="actions">
                        <a href="{{ $sv->url }}" class="btn-link-mk">Know More <i class="fa-solid fa-arrow-right"></i></a>
                        <a href="{{ url('contact-us') }}?service={{ $sv->id }}#enquiry" class="btn btn-gold">Enquire Now</a>
                    </div>
                </div>
            </article>
        </div>
    @endforeach
</div>
