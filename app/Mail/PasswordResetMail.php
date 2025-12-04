<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $resetUrl;

    // Constructor to pass the reset link
    public function __construct($resetUrl)
    {
        $this->resetUrl = $resetUrl;
    }

    public function build()
    {
        return $this->subject('Reset Your Password')
            ->view('emails.reset-password-mail') // Use a custom view for the email content
            ->with(['resetUrl' => $this->resetUrl]);
    }
}
