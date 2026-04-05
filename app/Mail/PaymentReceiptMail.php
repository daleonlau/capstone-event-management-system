<?php

namespace App\Mail;

use App\Models\EventStudent;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class PaymentReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public $payment;
    public $student;
    public $event;
    public $treasurer;

    public function __construct(EventStudent $payment)
    {
        $this->payment = $payment;
        $this->student = $payment->student;
        $this->event = $payment->event;
        $this->treasurer = $payment->treasurer;
    }

    public function envelope(): Envelope
    {
        // Set reply-to based on the treasurer who processed the payment
        $replyToEmail = $this->treasurer?->email ?? config('mail.from.address');
        $replyToName = $this->treasurer?->name ?? config('mail.from.name');
        
        return new Envelope(
            subject: 'Payment Receipt #' . $this->payment->receipt_number . ' - ' . $this->event->event_name,
            replyTo: [
                [
                    'address' => $replyToEmail,
                    'name' => $replyToName,
                ]
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-receipt',
            with: [
                'payment' => $this->payment,
                'student' => $this->student,
                'event' => $this->event,
                'treasurer' => $this->treasurer,
            ]
        );
    }

    public function attachments(): array
    {
        $attachments = [];
        
        if ($this->payment->receipt_pdf_path) {
            $fullPath = storage_path('app/public/' . $this->payment->receipt_pdf_path);
            
            if (file_exists($fullPath)) {
                $attachments[] = Attachment::fromPath($fullPath)
                    ->as('receipt-' . $this->payment->receipt_number . '.pdf')
                    ->withMime('application/pdf');
            }
        }

        return $attachments;
    }
}