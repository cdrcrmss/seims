<?php

namespace App\Support;

class MailConfig
{
    /**
     * Pick the best default mailer from environment variables.
     */
    public static function resolveDefaultMailer(): string
    {
        $configured = env('MAIL_MAILER');

        if ($configured && ! in_array($configured, ['log', 'array'], true)) {
            return $configured;
        }

        if (filled(env('RESEND_KEY'))) {
            return 'resend';
        }

        if (filled(env('POSTMARK_TOKEN'))) {
            return 'postmark';
        }

        if (self::smtpCredentialsPresent()) {
            return 'smtp';
        }

        return $configured ?: 'log';
    }

    /**
     * Whether outbound email can reach a real inbox (not log/array only).
     */
    public static function canDeliver(): bool
    {
        return ! in_array(config('mail.default'), ['log', 'array'], true);
    }

    /**
     * SMTP is configured via MAIL_URL or host + credentials.
     */
    public static function smtpCredentialsPresent(): bool
    {
        if (filled(env('MAIL_URL'))) {
            return true;
        }

        $host = env('MAIL_HOST');
        if (! filled($host) || in_array($host, ['127.0.0.1', 'localhost'], true)) {
            return false;
        }

        return filled(env('MAIL_USERNAME')) && filled(env('MAIL_PASSWORD'));
    }
}
