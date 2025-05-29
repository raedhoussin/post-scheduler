<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserActionOccurred implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $activity;

    public function __construct($activity)
    {
        $this->activity = $activity;
    }

    public function broadcastOn()
    {
        return new Channel('user-activities');
    }

    public function broadcastWith()
    {
        return [
            'activity' => $this->activity,
        ];
    }
}