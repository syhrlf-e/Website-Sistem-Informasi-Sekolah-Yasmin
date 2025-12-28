<?php

namespace App\Mail;

use App\Models\Ekstrakurikuler;
use App\Models\EkstrakurikulerRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EkskulApprovalMail extends Mailable
{
    use Queueable, SerializesModels;

    public EkstrakurikulerRegistration $registration;
    public Ekstrakurikuler $ekskul;
    public string $whatsappUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(EkstrakurikulerRegistration $registration, Ekstrakurikuler $ekskul)
    {
        $this->registration = $registration;
        $this->ekskul = $ekskul;

        $message = "Assalamu'alaikum Admin {$ekskul->nama},\n\n"
            . "Saya {$registration->nama_lengkap}, siswa kelas {$registration->kelas}, "
            . "telah diterima di ekskul {$ekskul->nama}.\n\n"
            . "Mohon untuk ditambahkan ke grup.\nTerima kasih.";

        $whatsappNumber = preg_replace('/[^0-9]/', '', $ekskul->whatsapp_admin ?? '');
        $this->whatsappUrl = "https://wa.me/{$whatsappNumber}?text=" . urlencode($message);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Selamat! Pendaftaran Ekskul {$this->ekskul->nama} Diterima",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.ekskul-approval',
            with: [
                'registration' => $this->registration,
                'ekskul' => $this->ekskul,
                'whatsappUrl' => $this->whatsappUrl,
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
