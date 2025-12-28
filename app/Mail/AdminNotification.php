<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $emailMessage;
    public $userName;
    private $emailSubject;

    /**
     * Create a new message instance.
     *
     * @param string $subject
     * @param string $message
     * @param string $userName
     */
    public function __construct($subject, $message, $userName)
    {
        $this->emailSubject = $subject;
        $this->emailMessage = $message;
        $this->userName = $userName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->emailSubject)
                    ->view('emails.admin-notification')
                    ->with([
                        'emailSubject' => $this->emailSubject,
                        'emailMessage' => $this->emailMessage,
                        'userName' => $this->userName,
                    ]);
    }
}

