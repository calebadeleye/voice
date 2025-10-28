<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class LocalNumberRequestMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $userName;

    public function __construct($userName)
    {
         $this->userName = $userName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Local Number Request is Being Processed',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.local_number_request',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

