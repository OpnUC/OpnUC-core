<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessengerNewMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $channelId;
    public $ownerUserId;
    public $ownerUserName;
    public $ownerAvatarUrl;
    public $datetime;
    public $message;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($channelId, $ownerUserId, $ownerUserName, $ownerAvatarUrl, $datetime, $message)
    {
        $this->channelId = $channelId;
        $this->ownerUserId = $ownerUserId;
        $this->ownerUserName = $ownerUserName;
        $this->ownerAvatarUrl = $ownerAvatarUrl;
        $this->datetime = $datetime;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel('MessengerChannel.' . $this->channelId);
    }
}
