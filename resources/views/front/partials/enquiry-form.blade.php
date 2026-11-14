{{-- Reusable enquiry form. Vars: $formType (enquiry|contact), $selected (service id), $title --}}
@php
    $formType = $formType ?? 'enquiry';
    $formServices = \Illuminate\Support\Facades\Cache::rememberForever('nav.services', fn () => \App\Models\Service::published()->ordered()->get(['id', 'title', 'slug', 'icon']));
    $uid = 'f'.substr(md5(uniqid('', true)), 0, 6);
@endphp
<div class="form-card" id="enquiry">
    @if(!empty($title))<h3 class="h4 mb-1">{{ $title }}</h3>@endif
    @if(!empty($subtitle))<p class="text-muted small mb-4">{{ $subtitle }}</p>@endif
    <form action="{{ route('enquiry.store') }}" method="POST" data-ajax-enquiry novalidate>
        @csrf
        <input type="hidden" name="form_type" value="{{ $formType }}">
        <div class="hp-field" aria-hidden="true"><label for="{{ $uid }}w">Website</label><input type="text" id="{{ $uid }}w" name="website" tabindex="-1" autocomplete="off"></div>
        <div class="form-result">
            @if(session('enquiry_success'))<div class="alert alert-success form-alert mb-3"><i class="fa-solid fa-circle-check me-2"></i>{{ session('enquiry_success') }}</div>@endif
            @if($errors->any())<div class="alert alert-danger form-alert mb-3">{{ $errors->first() }}</div>@endif
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="{{ $uid }}n">Full Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="{{ $uid }}n" name="full_name" value="{{ old('full_name') }}" required maxlength="120" autocomplete="name">
            </div>
            <div class="col-md-6">
                <label class="form-label" for="{{ $uid }}c">Company Name</label>
                <input type="text" class="form-control" id="{{ $uid }}c" name="company_name" value="{{ old('company_name') }}" maxlength="150" autocomplete="organization">
            </div>
            <div class="col-md-6">
                <label class="form-label" for="{{ $uid }}p">Phone Number <span class="text-danger">*</span></label>
                <input type="tel" class="form-control" id="{{ $uid }}p" name="phone" value="{{ old('phone') }}" required pattern="[0-9+\-\s()]{7,20}" autocomplete="tel">
            </div>
            <div class="col-md-6">
                <label class="form-label" for="{{ $uid }}e">Email Address <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="{{ $uid }}e" name="email" value="{{ old('email') }}" required maxlength="150" autocomplete="email">
            </div>
            <div class="col-md-6">
                <label class="form-label" for="{{ $uid }}s">Service Required</label>
                <select class="form-select" id="{{ $uid }}s" name="service_id">
                    <option value="">Select a service</option>
                    @foreach($formServices as $s)
                        <option value="{{ $s->id }}" @selected(old('service_id', $selected ?? null) == $s->id)>{{ $s->title }}</option>
                    @endforeach
                </select>
            </div>
            @if($formType === 'contact')
                <div class="col-md-6">
                    <label class="form-label" for="{{ $uid }}l">Location</label>
                    <input type="text" class="form-control" id="{{ $uid }}l" name="location" value="{{ old('location') }}" maxlength="150">
                </div>
                <div class="col-12">
                    <label class="form-label" for="{{ $uid }}sub">Subject</label>
                    <input type="text" class="form-control" id="{{ $uid }}sub" name="subject" value="{{ old('subject') }}" maxlength="200">
                </div>
            @else
                <div class="col-md-6">
                    <label class="form-label" for="{{ $uid }}ws">Workforce Size</label>
                    <select class="form-select" id="{{ $uid }}ws" name="workforce_size">
                        <option value="">Select</option>
                        @foreach(lines(setting('workforce_sizes')) as $size)<option @selected(old('workforce_size') === $size)>{{ $size }}</option>@endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label" for="{{ $uid }}l">Work Location / City</label>
                    <input type="text" class="form-control" id="{{ $uid }}l" name="location" value="{{ old('location') }}" maxlength="150">
                </div>
            @endif
            <div class="col-12">
                <label class="form-label" for="{{ $uid }}m">Message</label>
                <textarea class="form-control" id="{{ $uid }}m" name="message" rows="4" maxlength="3000" placeholder="Tell us about your requirement">{{ old('message') }}</textarea>
            </div>
            <div class="col-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="{{ $uid }}cn" name="consent" required>
                    <label class="form-check-label small" for="{{ $uid }}cn">I agree to be contacted about my enquiry and accept the <a href="{{ url('privacy-policy') }}" target="_blank">Privacy Policy</a>.</label>
                </div>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-blue w-100 py-3"><i class="fa-solid fa-paper-plane"></i> Submit Your Requirement</button>
            </div>
        </div>
    </form>
</div>
