<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompanyStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $status;

    public function __construct(User $user, $status)
    {
        $this->user = $user;
        $this->status = $status;
    }

    public function envelope(): Envelope
    {
        $subject = $this->status === 'approved' 
            ? 'Wazafni - Account Approved! 🎉' 
            : 'Wazafni - Account Status Update';

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
      
        return new Content(
            view: 'emails.company_status',
        );
    }
}