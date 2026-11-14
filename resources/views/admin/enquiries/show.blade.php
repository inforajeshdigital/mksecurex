@extends('layouts.admin')
@section('title', 'Enquiry #'.$enquiry->id)
@section('crumbs', 'Leads › Enquiries › '.$enquiry->full_name)

@section('content')
<div class="row g-4">
    <div class="col-xl-8">
        <div class="a-card">
            <div class="a-card-head">
                <h2>{{ $enquiry->full_name }} <span class="badge-soft {{ $enquiry->is_spam ? 'spam' : $enquiry->status }} ms-2">{{ $enquiry->is_spam ? 'Spam' : ucfirst($enquiry->status) }}</span></h2>
                <span class="small text-muted">{{ $enquiry->created_at->format('d M Y, h:i A') }} · {{ ucfirst($enquiry->form_type) }} form</span>
            </div>
            <div class="a-card-body">
                <div class="row g-3">
                    @foreach(['Company' => $enquiry->company_name, 'Phone' => $enquiry->phone, 'Email' => $enquiry->email, 'Service Required' => $enquiry->service_name, 'Workforce Size' => $enquiry->workforce_size, 'Location' => $enquiry->location, 'Subject' => $enquiry->subject] as $l => $v)
                        <div class="col-md-6"><div class="small text-muted">{{ $l }}</div><div class="fw-semibold">{{ $v ?: '—' }}</div></div>
                    @endforeach
                    <div class="col-12"><div class="small text-muted">Message</div><div class="p-3 rounded-3 mt-1" style="background:#f7f9fd;white-space:pre-line">{{ $enquiry->message ?: '—' }}</div></div>
                </div>
                <hr>
                <div class="small text-muted">
                    <div><i class="fa-solid fa-link me-1"></i> Source: {{ $enquiry->source_url ?: '—' }}</div>
                    <div><i class="fa-solid fa-network-wired me-1"></i> IP: {{ $enquiry->ip_address }} · Consent: {{ $enquiry->consent ? 'Yes' : 'No' }}</div>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-3">
                    <a href="{{ tel_link($enquiry->phone) }}" class="btn btn-soft"><i class="fa-solid fa-phone me-1"></i> Call</a>
                    <a href="mailto:{{ $enquiry->email }}?subject={{ rawurlencode('Re: Your enquiry with '.setting('site_name')) }}" class="btn btn-soft"><i class="fa-regular fa-envelope me-1"></i> Email</a>
                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $enquiry->phone) }}" target="_blank" rel="noopener" class="btn btn-soft"><i class="fa-brands fa-whatsapp me-1"></i> WhatsApp</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <form action="{{ route('admin.enquiries.update', $enquiry) }}" method="POST" class="a-card">
            @csrf @method('PUT')
            <div class="a-card-head"><h2>Manage Enquiry</h2></div>
            <div class="a-card-body">
                <div class="mb-3"><label class="form-label">Status</label><select name="status" class="form-select">@foreach(\App\Models\Enquiry::STATUSES as $k => $l)<option value="{{ $k }}" @selected($enquiry->status === $k)>{{ $l }}</option>@endforeach</select></div>
                <div class="mb-3"><label class="form-label">Internal Notes</label><textarea name="notes" rows="5" class="form-control" placeholder="Follow-up notes (not visible to the customer)">{{ $enquiry->notes }}</textarea></div>
                <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" name="is_spam" value="1" id="spam" @checked($enquiry->is_spam)><label class="form-check-label" for="spam">Mark as spam</label></div>
                <button class="btn btn-primary w-100"><i class="fa-solid fa-floppy-disk me-1"></i> Save</button>
            </div>
        </form>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.enquiries.index') }}" class="btn btn-light flex-fill">Back to list</a>
            <form action="{{ route('admin.enquiries.destroy', $enquiry) }}" method="POST" data-confirm="Delete this enquiry?">@csrf @method('DELETE')<button class="btn btn-soft-danger"><i class="fa-solid fa-trash"></i></button></form>
        </div>
    </div>
</div>
@endsection
