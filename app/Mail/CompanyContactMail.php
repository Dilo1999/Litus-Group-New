<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompanyContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $senderName,
        public string $senderEmail,
        public ?string $senderPhone,
        public string $messageBody,
        public ?string $companyName = null,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->companyName
            ? '[Company Enquiry] '.$this->companyName
            : '[Contact Form] New enquiry';

        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            replyTo: [new Address($this->senderEmail, $this->senderName)],
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.company-contact',
        );
    }
}

