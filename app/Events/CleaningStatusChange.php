<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CleaningStatusChange
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $cleaningStatus;
    public $spot;

    public function __construct($spot, $cleaningStatus)
    {
        $this->spot = $spot;
        $this->cleaningStatus = $cleaningStatus;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
