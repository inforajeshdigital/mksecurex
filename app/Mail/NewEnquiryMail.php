<?php

namespace App\Mail;

use App\Models\Enquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewEnquiryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Enquiry $enquiry)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Enquiry: '.$this->enquiry->full_name.($this->enquiry->service_name ? ' – '.$this->enquiry->service_name : ''),
            replyTo: [new Address($this->enquiry->email, $this->enquiry->full_name)],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.enquiry');
    }
}
