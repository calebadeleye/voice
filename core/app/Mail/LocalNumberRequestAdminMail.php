<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LocalNumberRequestAdminMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $company;
    public $type;
    public $price;
    public $vat;
    public $total;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $company, $type, $price, $vat, $total)
    {
        $this->user = $user;
        $this->company = $company;
        $this->type = $type;
        $this->price = $price;
        $this->vat = $vat;
        $this->total = $total;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Local Number Request Received',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.local_number_request_admin',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
