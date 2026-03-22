<?php

namespace App\Mail;

use App\Models\Evaluation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class EvaluationReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $evaluation;
    public $organization;

    public function __construct(Evaluation $evaluation, User $organization)
    {
        $this->evaluation = $evaluation;
        $this->organization = $organization;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Evaluation Report - ' . $this->evaluation->event->event_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.evaluation-report',
            with: [
                'evaluation' => $this->evaluation,
                'organization' => $this->organization,
                'event' => $this->evaluation->event,
                'report_date' => now()->format('F d, Y'),
            ]
        );
    }

    public function attachments(): array
    {
        $attachments = [];
        
        if ($this->evaluation->report_path && \Storage::disk('public')->exists($this->evaluation->report_path)) {
            $attachments[] = Attachment::fromPath(storage_path('app/public/' . $this->evaluation->report_path))
                ->as('evaluation_report_' . $this->evaluation->id . '.pdf')
                ->withMime('application/pdf');
        }

        return $attachments;
    }
}