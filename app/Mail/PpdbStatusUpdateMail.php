<?php

namespace App\Mail;

use App\Models\PpdbRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PpdbStatusUpdateMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public PpdbRegistration $registration;
    public string $previousStatus;

    /**
     * Create a new message instance.
     */
    public function __construct(PpdbRegistration $registration, string $previousStatus)
    {
        $this->registration = $registration;
        $this->previousStatus = $previousStatus;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Update Status PPDB - ' . $this->registration->registration_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.ppdb.status-update',
            with: [
                'registration' => $this->registration,
                'previousStatus' => $this->previousStatus,
                'url' => config('app.url') . '/ppdb/status',
            ],
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
