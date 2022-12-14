<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\PasswordReset;

class ForgotPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    /** @var App\Models\PasswordReset */
    protected $passwordReset;

    /** @var string*/
    public $view;

    /** @var string */
    protected $url;

    /** @var App\Models\User */
    protected $user;

    /** @var string */
    public $subject;
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(PasswordReset $passwordReset)
    {
        $this->view = 'mail.password.forgotPassword';
        $this->subject = 'Forgot Password Reset';
        $this->user = $passwordReset->user;
        $this->url = env('APP_URL') . '/password/reset?token=' . $passwordReset->token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)
            ->markdown($this->view)
            ->with([
                'user' => $this->user,
                'url' => $this->url,
            ]);
    }
}