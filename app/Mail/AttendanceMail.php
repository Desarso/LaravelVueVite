<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AttendanceMail extends Mailable
{
    use Queueable, SerializesModels;
    public $logs;

    public function __construct($logs)
    {
        $this->logs = $logs;
    }

    public function build()
    {
        return $this->subject("Tardias de Whagons")->markdown('emails.AttendanceTemplate');
    }
}
