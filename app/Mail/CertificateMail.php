<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CertificateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $certificatePath;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $certificatePath)
    {
        $this->user = $user;
        $this->certificatePath = $certificatePath;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your Certificate of Completion')
            ->view('emails.certificate')
            ->attach($this->certificatePath, [
                'as' => 'Certificate.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}
