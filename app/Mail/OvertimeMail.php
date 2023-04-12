<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OvertimeMail extends Mailable
{
    use Queueable, SerializesModels;
    public $slots;

    public function __construct($slots)
    {
        $this->slots = $slots;
    }

    public function build()
    {
        return $this->subject("Resumen Horas Extra")->markdown('emails.OvertimeTemplate');
    }
}
