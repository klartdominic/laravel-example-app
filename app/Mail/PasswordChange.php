<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class PasswordChange extends Mailable
{
    use Queueable, SerializesModels;

     /** @var string*/
    public $view;

    /** @var App\Models\User */
    protected $user;

    /** @var string */
    public $subject;

    /** @var string */
    protected $url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->view = 'mail.password.resetPassword';
        $this->subject = 'Password Changed';
        $this->user = $user;
        $this->url = env('APP_URL') . 'login';
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