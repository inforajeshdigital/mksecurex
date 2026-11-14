<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Mail\NewEnquiryMail;
use App\Models\Enquiry;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EnquiryController extends Controller
{
    public function store(Request $request)
    {
        // Honeypot: bots fill the hidden "website" field. Pretend success.
        if (filled($request->input('website'))) {
            return $this->respond($request, true, setting('enquiry_success'));
        }

        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'company_name' => ['nullable', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s()]{7,20}$/'],
            'email' => ['required', 'email:rfc', 'max:150'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
            'service_other' => ['nullable', 'string', 'max:100'],
            'workforce_size' => ['nullable', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:150'],
            'subject' => ['nullable', 'string', 'max:200'],
            'message' => ['nullable', 'string', 'max:3000'],
            'consent' => ['accepted'],
            'form_type' => ['nullable', 'in:enquiry,contact,quote'],
        ], [
            'consent.accepted' => 'Please accept the Privacy Policy to continue.',
            'phone.regex' => 'Please enter a valid phone number.',
        ]);

        $serviceName = $data['service_id'] ? Service::find($data['service_id'])?->title : ($data['service_other'] ?? null);

        $enquiry = Enquiry::create([
            'full_name' => strip_tags($data['full_name']),
            'company_name' => isset($data['company_name']) ? strip_tags($data['company_name']) : null,
            'phone' => $data['phone'],
            'email' => $data['email'],
            'service_id' => $data['service_id'] ?? null,
            'service_name' => $serviceName,
            'workforce_size' => $data['workforce_size'] ?? null,
            'location' => isset($data['location']) ? strip_tags($data['location']) : null,
            'subject' => isset($data['subject']) ? strip_tags($data['subject']) : null,
            'message' => isset($data['message']) ? strip_tags($data['message']) : null,
            'consent' => true,
            'form_type' => $data['form_type'] ?? 'enquiry',
            'source_url' => mb_substr((string) url()->previous(), 0, 250),
            'ip_address' => $request->ip(),
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 250),
        ]);

        try {
            $to = setting('admin_email');
            if ($to && filter_var($to, FILTER_VALIDATE_EMAIL)) {
                Mail::to($to)->send(new NewEnquiryMail($enquiry));
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return $this->respond($request, true, setting('enquiry_success'));
    }

    protected function respond(Request $request, bool $ok, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json(['success' => $ok, 'message' => $message]);
        }

        return back()->with('enquiry_success', $message)->withFragment('enquiry');
    }
}
