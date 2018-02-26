<?php

namespace App\Events;

use App\AddressBook;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

/**
 * 着信通知を行うイベント
 * Class IncomingCallEvent
 * @package App\Events
 */
class IncomingCallEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * 発信者番号
     * @var string
     */
    public $callerid_num;

    /**
     * 発信者名
     * @var string
     */
    public $callerid_name;

    /**
     * 状態
     * @var bool
     */
    public $state;

    /**
     * ユーザID
     * @var int
     */
    private $userid;

    /**
     * Create a new event instance.
     *
     * @param string $ext
     * @param bool $state
     * @param string $callerid_num
     * @param string $callerid_name
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
        if (!$this->callerid_name && $this->callerid_num) {
            $record = AddressBook::select('name')
                ->orWhere('tel1', $this->callerid_num)
                ->orWhere('tel2', $this->callerid_num)
                ->orWhere('tel3', $this->callerid_num)
                ->get()
                ->first();

            if ($record !== null) {
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
