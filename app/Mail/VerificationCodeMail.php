<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $code;
    public $userLogin;

    public function __construct($code, $login)
    {
        $this->code = $code;
        $this->userLogin = $login;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Подтверждение регистрации на Фотосфера',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.verification-code',
            with: [
                'code' => $this->code,
                'login' => $this->userLogin,
            ]
        );
    }
}