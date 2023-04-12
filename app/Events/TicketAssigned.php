<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketAssigned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $ticket;
    public $users;
    public $relation;

    public function __construct($ticket, $users, $relation = "users")
    {
        $this->ticket   = $ticket;
        $this->users    = $users;
        $this->relation = $relation;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
