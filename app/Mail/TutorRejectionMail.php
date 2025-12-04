<?php

namespace App\Mail;

use App\Models\Tutor;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TutorRejectionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $tutor;
    public $rejectionReason;

    public function __construct(Tutor $tutor, $rejectionReason)
    {
        $this->tutor = $tutor;
        $this->rejectionReason = $rejectionReason;
    }

    public function build()
    {
        return $this->view('emails.tutor-rejection')
            ->with([
                'tutorName' => $this->tutor->user->name,
                'rejectionReason' => $this->rejectionReason,
            ]);
    }
}
