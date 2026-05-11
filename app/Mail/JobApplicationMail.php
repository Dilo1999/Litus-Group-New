<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Http\UploadedFile;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JobApplicationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $position,
        public string $name,
        public string $email,
        public ?string $phone,
        public UploadedFile $cv,
    ) {}

    public function envelope(): Envelope
    {
        $to = config('mail.careers_to');

        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            replyTo: [new Address($this->email, $this->name)],
            to: [new Address($to)],
            subject: '[Job Application] '.$this->position,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.job-application',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->cv->getRealPath())
                ->as($this->cv->getClientOriginalName())
                ->withMime($this->cv->getMimeType() ?: 'application/octet-stream'),
        ];
    }
}
