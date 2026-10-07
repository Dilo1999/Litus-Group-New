<?php

namespace App\Mail\Transport;

use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\MessageConverter;

/**
 * Sends mail through the Brevo transactional email HTTP API (no SMTP needed).
 */
class BrevoTransport extends AbstractTransport
{
    public function __construct(
        protected string $apiKey,
        protected bool $verifySsl = true,
        protected string $endpoint = 'https://api.brevo.com/v3/smtp/email',
    ) {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $response = Http::withHeaders(['api-key' => $this->apiKey])
            ->withOptions(['verify' => $this->verifySsl])
            ->acceptJson()
            ->timeout(30)
            ->post($this->endpoint, $this->payload($email, $message->getEnvelope()->getSender()));

        if ($response->failed()) {
            throw new TransportException('Brevo API error ('.$response->status().'): '.$response->body());
        }

        if ($id = $response->json('messageId')) {
            $message->setMessageId($id);
        }
    }

    protected function payload(Email $email, Address $sender): array
    {
        $from = $email->getFrom()[0] ?? $sender;

        $payload = array_filter([
            'sender' => $this->address($from),
            'to' => $this->addresses($email->getTo()),
            'cc' => $this->addresses($email->getCc()),
            'bcc' => $this->addresses($email->getBcc()),
            'replyTo' => ($replyTo = $email->getReplyTo()[0] ?? null) ? $this->address($replyTo) : null,
            'subject' => $email->getSubject(),
            'htmlContent' => $email->getHtmlBody(),
            'textContent' => $email->getTextBody(),
        ]);

        foreach ($email->getAttachments() as $attachment) {
            $payload['attachment'][] = [
                'name' => $attachment->getFilename() ?? 'attachment',
                'content' => base64_encode($attachment->getBody()),
            ];
        }

        return $payload;
    }

    protected function address(Address $address): array
    {
        return array_filter(['email' => $address->getAddress(), 'name' => $address->getName()]);
    }

    protected function addresses(array $addresses): array
    {
        return array_map(fn (Address $a) => $this->address($a), $addresses);
    }

    public function __toString(): string
    {
        return 'brevo';
    }
}
