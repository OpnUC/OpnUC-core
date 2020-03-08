<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PbxLinkerOriginateEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * 内線番号
     * @var string
     */
    var $extNumber;

    /**
     * 発信先番号
     * @var string
     */
    var $number;

    /**
     * Create a new event instance.
     * @param $extNumber string 内線番号
     * @param $number string 発信先番号
     * @return void
     */
    public function __construct($extNumber, $number)
    {

        $this->extNumber = $extNumber;
        $this->number = $number;

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('PbxLinkerChannel');
    }
}
