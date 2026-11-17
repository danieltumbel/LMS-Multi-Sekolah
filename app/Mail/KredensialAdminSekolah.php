<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class KredensialAdminSekolah extends Mailable
{


    /**
     * Create a new message instance.
     */
    public function __construct(public string $sekolah, public string $email, public string $password)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Akun Admin Sekolah'. $this->sekolah,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.kredensial_admin_sekolah',
        );
    }
}
