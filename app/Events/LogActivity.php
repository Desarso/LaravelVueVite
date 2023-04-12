<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LogActivity
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $action;
    public $ticket;
    public $iduser;
    public $data;

    public function __construct($action, $ticket, $iduser, $data = null)
    {
        $this->action = $action;
        $this->ticket = $ticket;
        $this->iduser = $iduser;
        $this->data   = $data;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
