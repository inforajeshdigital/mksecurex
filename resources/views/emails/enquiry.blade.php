<!DOCTYPE html>
<html><body style="margin:0;background:#f3f7ff;font-family:Arial,sans-serif;color:#1d2a44">
<table width="100%" cellpadding="0" cellspacing="0" style="padding:24px 0"><tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:12px;overflow:hidden">
    <tr><td style="background:#06173a;padding:20px 28px;color:#fff;font-size:20px;font-weight:bold;border-bottom:4px solid #e8a317">New Website Enquiry</td></tr>
    <tr><td style="padding:24px 28px">
        <table width="100%" cellpadding="8" cellspacing="0" style="font-size:14px;border-collapse:collapse">
            @foreach([
                'Name' => $enquiry->full_name, 'Company' => $enquiry->company_name, 'Phone' => $enquiry->phone, 'Email' => $enquiry->email,
                'Service' => $enquiry->service_name, 'Workforce Size' => $enquiry->workforce_size, 'Location' => $enquiry->location,
                'Subject' => $enquiry->subject, 'Message' => $enquiry->message, 'Form' => ucfirst($enquiry->form_type), 'Page' => $enquiry->source_url,
                'Received' => $enquiry->created_at->format('d M Y, h:i A'),
            ] as $label => $value)
                @if($value)
                <tr><td style="width:150px;color:#6b7690;border-bottom:1px solid #eef2fa;vertical-align:top">{{ $label }}</td><td style="border-bottom:1px solid #eef2fa">{!! nl2br(e($value)) !!}</td></tr>
                @endif
            @endforeach
        </table>
        <p style="margin-top:24px"><a href="{{ route('admin.enquiries.show', $enquiry) }}" style="background:#0b3d91;color:#fff;padding:10px 18px;border-radius:8px;text-decoration:none">View in Admin Panel</a></p>
    </td></tr>
</table>
</td></tr></table>
</body></html>
