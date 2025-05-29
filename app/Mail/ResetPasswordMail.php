<?php

namespace App\Mail;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Mail\Mailable;

class ResetPasswordMail extends Mailable
{
    public $user;
    public $resetLink;

    public function __construct($user, $resetLink)
    {
        $this->user = $user;
        $this->resetLink = $resetLink;
    }

    public function build()
    {
        return $this->subject('Password Reset Request')
                    ->view('reset_password'); 
    }
}
