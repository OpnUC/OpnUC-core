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

    public $callerid_num;
    public $callerid_name;
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
    public function __construct($ext, $state, $callerid_num = null, $callerid_name = '')
    {

        $this->state = $state;
        $this->callerid_num = $callerid_num;
        $this->callerid_name = $callerid_name;

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

        // Caller ID Nameが無い場合は、電話帳から検索する
        if(!$this->callerid_name && $this->callerid_num){
            $record = AddressBook::select('name')
                ->orWhere('tel1', $this->callerid_num)
                ->orWhere('tel2', $this->callerid_num)
                ->orWhere('tel3', $this->callerid_num)
                ->get()
                ->first();

            if(!$record){
                // レコードが無い場合は、セットしない
                $this->callerid_name = $record->name;
            }
        }

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
