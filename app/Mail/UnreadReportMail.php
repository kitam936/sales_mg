<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UnreadReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('【Dijon_Sales2】未読の店舗Reportがあります')
            ->markdown('emails.unread_report', [
                'user' => $this->user,
            ]);
    }
}
