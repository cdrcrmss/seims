<?php

namespace App\Mail;

use App\Models\Borrowing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BorrowingRejected extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Borrowing $borrowing
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '❌ Borrow Request Rejected — ' . $this->borrowing->item->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.borrowing-rejected',
        );
    }
}
