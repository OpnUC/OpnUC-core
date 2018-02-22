<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PresenceUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ext;
    public $status;

    /**
     * Create a new event instance.
     *
     * @param $ext string 内線番号
     * @param $status string 状態
     */
    public function __construct($ext, $status)
    {
        $this->ext = $ext;
        $this->status = $status;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {

        return new Channel('BroadcastChannel');

    }
}
