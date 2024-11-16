<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendResetPasswordOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user,$token, $subject , $expiretime;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user,$token, $subject , $expiretime)
    {
        $this->user = $user;
        $this->token = $token;
        $this->subject = $subject;
        $this->expiretime = $expiretime;
    }

   /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.auth.forgot_password_otp', [
            'user' => $this->user ,
            'token' => $this->token ,
            'expiretime' => $this->expiretime])->subject($this->subject);
    }
}
