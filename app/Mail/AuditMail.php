<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AuditMail extends Mailable
{
    use Queueable, SerializesModels;
    public $options;
    public $checklist;
    public $url;

    public function __construct($checklist, $options, $url)
    {
        $this->checklist = $checklist;
        $this->options   = $options;
        $this->url   = $url;
    }

    public function build()
    {
        //return $this->view('emails.hello');
        return $this->subject("Auditoría de Whagons")->markdown('emails.AuditTemplate');
    }
}
