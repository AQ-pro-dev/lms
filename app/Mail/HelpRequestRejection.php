<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HelpRequestRejection extends Mailable
{
    use Queueable, SerializesModels;

    public $response;

    public function __construct($response)
    {
        $this->response = $response;
    }

    public function build()
    {
        return $this->view('emails.help-request-rejection')
            ->subject('Help Request Rejected')
            ->with([
                'response' => $this->response,
            ]);
    }
}
