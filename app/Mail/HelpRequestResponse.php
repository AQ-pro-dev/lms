<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HelpRequestResponse extends Mailable
{
    use Queueable, SerializesModels;

    public $response;

    public function __construct($response)
    {
        $this->response = $response;
    }

    public function build()
    {
        return $this->view('emails.help-request-response')
            ->subject('Help Request Response')
            ->with([
                'response' => $this->response,
            ]);
    }
}
