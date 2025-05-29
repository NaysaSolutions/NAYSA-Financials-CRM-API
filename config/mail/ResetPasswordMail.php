<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResetPasswordMail extends Mailable
{
    use SerializesModels;

    public $user;
    public $resetLink;

    public function __construct($user, $resetLink)
    {
        $this->user = $user;
        $this->resetLink = $resetLink;
    }

    public function build()
    {
        return $this->view('reset_password')
                    ->with([
                        'username' => $this->user->username,
                        'email' => $this->user->email,
                        'resetLink' => $this->resetLink,
                    ]);
    }
}
