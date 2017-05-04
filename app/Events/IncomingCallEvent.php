<?php

namespace App\Events;

use App\AddressBook;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class IncomingCallEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $caller_id;
    public $caller_name;
    public $state;
    private $userid;

    /**
     * Create a new event instance.
     *
     * @param $ext string
     * @param $state bool
     * @param $caller_id string
     * @param $caller_name string
     */
    public function __construct($ext, $state, $caller_id = null, $caller_name = null)
    {

        $this->state = $state;
        $this->caller_id = $caller_id;
        $this->caller_name = $caller_name;

        $record = AddressBook::select('owner_userid')
            // 内線電話帳
            ->where('type', 1)
            ->where('tel1', $ext)
            ->get()
            ->first();

        if (!$record) {
            // ユーザが存在しない場合は処理しない
            return;
        }

        $this->userid = $record->owner_userid;

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('PrivateChannel.' . $this->userid);
    }
}
