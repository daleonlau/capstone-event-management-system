<?php

namespace App\Mail;

use App\Models\EventStudent;
use App\Models\Organization;
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
    public $organization;
    public $organizationName;

    public function __construct(EventStudent $payment)
{
    $this->payment = $payment;
    $this->student = $payment->student;
    $this->event = $payment->event;
    $this->treasurer = $payment->treasurer; // This now uses processed_by relationship
    
    // Get organization details
    $this->organization = Organization::find($payment->event->user_id);
    $this->organizationName = $this->organization ? $this->organization->name : 'Organization';
}

    public function envelope(): Envelope
    {
        // Set reply-to to the treasurer who processed the payment
        $replyToEmail = $this->treasurer?->email ?? config('mail.from.address');
        
        return new Envelope(
            subject: 'Payment Receipt #' . $this->payment->receipt_number . ' - ' . $this->event->event_name,
            replyTo: $replyToEmail
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
                'organization' => $this->organization,
                'organizationName' => $this->organizationName,
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