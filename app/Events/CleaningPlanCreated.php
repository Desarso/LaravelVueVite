<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CleaningPlanCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $cleaningPlan;

    public function __construct($cleaningPlan)
    {
        $this->cleaningPlan = $cleaningPlan;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
