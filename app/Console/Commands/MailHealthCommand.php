<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class MailHealthCommand extends Command
{
    protected $signature = 'mail:health {to? : Recipient email (defaults to MAIL_CONTACT_RECIPIENT)}';

    protected $description = 'Send a minimal test message using the configured mailer(s)';

    public function handle(): int
    {
        $to = $this->argument('to')
            ?: config('mail.contact_to', config('mail.from.address'));

        try {
            Mail::raw('Laravel mail health check — you can delete this message.', function ($message) use ($to) {
                $message->to($to)->subject('[Mail health] '.config('app.name'));
            });
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info('Sent test mail to '.$to.' using mailer: '.config('mail.default', 'smtp'));

        return self::SUCCESS;
    }
}
