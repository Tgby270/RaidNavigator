<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->subject('Invitation à rejoindre l\'équipe ' . ($this->data['teamName'] ?? 'RAID Navigator'))
                    ->view('mail.invitation')
                    ->with('data', $this->data);
    }
}