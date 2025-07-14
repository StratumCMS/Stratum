<?php

namespace App\Support;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Mail\MailManager as LaravelMailManager;
class MailManager {

    public function sendTestEmail(string $to)
    {
        Mail::mailer($this->getMailer())->raw('Ceci est un e-mail de test.', function ($message) use ($to) {
            $message->to($to)->subject('Test de configuration mail');
        });
    }

    protected function getMailer(): string
    {
        $driver = setting('mail.driver', 'smtp');

        if (in_array($driver, ['smtp', 'sendmail'])) {
            return $driver;
        }

        if ($driver === 'mailgun') {
            Config::set('services.mailgun.domain', setting('mail.mailgun_domain'));
            Config::set('services.mailgun.secret', setting('mail.mailgun_secret'));
            Config::set('services.mailgun.endpoint', setting('mail.mailgun_endpoint', 'api.mailgun.net'));

            return 'mailgun';
        }

        if ($driver === 'sendgrid') {
            Config::set('mail.mailers.sendgrid.transport', 'smtp');
            Config::set('mail.mailers.sendgrid.host', 'smtp.sendgrid.net');
            Config::set('mail.mailers.sendgrid.port', 587);
            Config::set('mail.mailers.sendgrid.username', 'apikey');
            Config::set('mail.mailers.sendgrid.password', setting('mail.sendgrid_api_key'));

            return 'sendgrid';
        }

        return 'smtp';
    }

}
