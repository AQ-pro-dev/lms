<?php

namespace App\Mail;

use App\Models\Tutor;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TutorApprovalMail extends Mailable
{
    use Queueable, SerializesModels;

    public $tutor;
    public $approvalMessage;

    public function __construct(Tutor $tutor, $approvalMessage)
    {
        $this->tutor = $tutor;
        $this->approvalMessage = $approvalMessage;
    }

    public function build()
    {
        return $this->view('emails.tutor-approval')
            ->with([
                'tutorName' => $this->tutor->user->name,
                'approvalMessage' => $this->approvalMessage,
            ]);
    }
}
