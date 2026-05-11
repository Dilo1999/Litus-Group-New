<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    |
    | This option controls the default mailer that is used to send any email
    | messages sent by your application. Alternative mailers may be setup
    | and used as needed; however, this mailer will be used by default.
    |
    */

    'default' => env('MAIL_MAILER', 'smtp'),

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    |
    | Here you may configure all of the mailers used by your application plus
    | their respective settings. Several examples have been configured for
    | you and you are free to add your own as your application requires.
    |
    | Laravel supports a variety of mail "transport" drivers to be used while
    | sending an e-mail. You will specify which one you are using for your
    | mailers below. You are free to add additional mailers as required.
    |
    | Supported: "smtp", "sendmail", "mailgun", "ses", "ses-v2",
    |            "postmark", "log", "array", "failover", "roundrobin"
    |
    */

    /*
    |--------------------------------------------------------------------------
    | cPanel / shared hosting notes (SMTP)
    |--------------------------------------------------------------------------
    |
    | - Set APP_URL to your real site URL on the server (used as EHLO fallback).
    | - Optionally set MAIL_EHLO_DOMAIN to your domain if mail still fails.
    | - If port 587 + TLS fails, try MAIL_PORT=465 with MAIL_ENCRYPTION=ssl.
    | - If SSL certificate verification fails on the host, set MAIL_VERIFY_PEER=false
    |   (less secure; prefer fixing PHP's CA bundle / openssl.cafile in php.ini).
    | - If the host blocks outbound SMTP to Google, create a mailbox in cPanel and use
    |   MAIL_MAILER=cpanel_smtp with MAIL_CPANEL_* (localhost relay).
    |
    */

    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'url' => env('MAIL_URL'),
            'host' => env('MAIL_HOST', 'smtp.mailgun.org'),
            'port' => (int) env('MAIL_PORT', 587),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => (int) env('MAIL_TIMEOUT', 60),
            'local_domain' => env('MAIL_EHLO_DOMAIN')
                ?: (parse_url((string) env('APP_URL', 'http://localhost'), PHP_URL_HOST) ?: 'localhost'),
            // Symfony Mailer reads this from the DSN options array (see EsmtpTransportFactory).
            'verify_peer' => filter_var(env('MAIL_VERIFY_PEER', true), FILTER_VALIDATE_BOOLEAN),
        ],

        /*
        |--------------------------------------------------------------------------
        | cPanel localhost SMTP relay
        |--------------------------------------------------------------------------
        |
        | Use when outbound connections to external SMTP (e.g. smtp.gmail.com) are blocked.
        | Create an email account in cPanel, then set MAIL_MAILER=cpanel_smtp and the vars below.
        |
        */

        'cpanel_smtp' => [
            'transport' => 'smtp',
            'host' => env('MAIL_CPANEL_HOST', 'localhost'),
            'port' => (int) env('MAIL_CPANEL_PORT', 587),
            'encryption' => env('MAIL_CPANEL_ENCRYPTION', 'tls'),
            'username' => env('MAIL_CPANEL_USERNAME'),
            'password' => env('MAIL_CPANEL_PASSWORD'),
            'timeout' => (int) env('MAIL_TIMEOUT', 60),
            'local_domain' => env('MAIL_EHLO_DOMAIN')
                ?: (parse_url((string) env('APP_URL', 'http://localhost'), PHP_URL_HOST) ?: 'localhost'),
            'verify_peer' => filter_var(env('MAIL_VERIFY_PEER', true), FILTER_VALIDATE_BOOLEAN),
        ],

        'ses' => [
            'transport' => 'ses',
        ],

        'postmark' => [
            'transport' => 'postmark',
            // 'message_stream_id' => null,
            // 'client' => [
            //     'timeout' => 5,
            // ],
        ],

        'mailgun' => [
            'transport' => 'mailgun',
            // 'client' => [
            //     'timeout' => 5,
            // ],
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers' => [
                'smtp',
                'log',
            ],
        ],

        'roundrobin' => [
            'transport' => 'roundrobin',
            'mailers' => [
                'ses',
                'postmark',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    |
    | You may wish for all e-mails sent by your application to be sent from
    | the same address. Here, you may specify a name and address that is
    | used globally for all e-mails that are sent by your application.
    |
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Example'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Contact Form Recipient
    |--------------------------------------------------------------------------
    |
    | Emails sent from the contact form will be delivered to this address.
    | Defaults to MAIL_FROM_ADDRESS if not set.
    |
    */

    'contact_to' => env('MAIL_CONTACT_RECIPIENT') ?: env('MAIL_FROM_ADDRESS', 'hello@example.com'),

    /*
    |--------------------------------------------------------------------------
    | Careers / Job Application Recipient
    |--------------------------------------------------------------------------
    */

    'careers_to' => env('MAIL_CAREERS_RECIPIENT') ?: 'hr@litusgroup.mv',

    /*
    |--------------------------------------------------------------------------
    | Fallback mailer (optional)
    |--------------------------------------------------------------------------
    |
    | If the primary MAIL_MAILER fails (e.g. outbound Gmail blocked on cPanel),
    | set MAIL_FALLBACK_MAILER=cpanel_smtp and configure MAIL_CPANEL_* so a second
    | attempt uses the localhost relay.
    |
    */

    'fallback_mailer' => env('MAIL_FALLBACK_MAILER'),

    /*
    |--------------------------------------------------------------------------
    | Markdown Mail Settings
    |--------------------------------------------------------------------------
    |
    | If you are using Markdown based email rendering, you may configure your
    | theme and component paths here, allowing you to customize the design
    | of the emails. Or, you may simply stick with the Laravel defaults!
    |
    */

    'markdown' => [
        'theme' => 'default',

        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],

];
