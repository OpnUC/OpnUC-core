<?php

namespace App\Libs;

use PAMI\Message\Event\EventMessage;
use PAMI\Message\Event\UnknownEvent;
use PAMI\Message\Event\OriginateResponseEvent;

class AsteriskLinker
{

    // クライアント
    private $client = null;
    // 接続状態
    private $connect = false;

    /**
     * AsteriskLinker constructor.
     */
    public function __construct()
    {

        $options = array(
            'host' => env('ASTERISK_LINKER_HOST', ''),
            'scheme' => 'tcp://',
            'port' => env('ASTERISK_LINKER_PORT', 5038),
            'username' => env('ASTERISK_LINKER_USER', 'opnuc'),
            'secret' => env('ASTERISK_LINKER_PASSWORD', 'opnuc'),
            'connect_timeout' => 10,
            'read_timeout' => 10
        );

        $this->client = new \PAMI\Client\Impl\ClientImpl($options);

        $this->client->open();

        $this->connect = true;

    }

    /**
     * デストラクタ
     */
    public function __destruct()
    {

        // 接続されている場合は、切断する
        if ($this->connect) {
            $this->client->close();

            $this->connect = false;
        }

    }

    public function processPresence()
    {

        if (!$this->connect) {
            return;
        }

        // すべてのイベントを捕捉
        $this->client->registerEventListener(
            function (EventMessage $event) {
                //var_dump($event);
            }
        );

        // Click 2 Callの結果
//        $client->registerEventListener(
//            function (EventMessage $event) {
//                echo $event->getKey('actionid') . ':' . $event->getKey('response') . "\n";
//            },
//            function (EventMessage $event) {
//                return $event instanceof OriginateResponseEvent
//                    && $event->getKey('event') == 'OriginateResponse';
//            }
//        );

        // Device State
        $this->client->registerEventListener(
            function (EventMessage $event) {

                \Log::debug('AsteriskLinker:DeviceStateChange');
                \Log::debug('  Device:' . $event->getKey('device'));
                \Log::debug('  State:' . $event->getKey('state'));

                if (!preg_match('/(\d+)/', $event->getKey('device'), $matches)) {
                    return;
                }

                $ext = $matches[0];
                $state = 'unknown';

                // ToDo: AsteriskとOpnUCのマッチ必要
                switch ($event->getKey('state')) {
                    case 'INUSE':
                        $state = 'busy';
                        break;
                    case 'NOT_INUSE':
                        $state = 'idle';
                }

                event(new \App\Events\PresenceUpdated($ext, $state));
            },
            function (EventMessage $event) {
                return $event instanceof UnknownEvent
                    && $event->getKey('event') == 'DeviceStateChange';
            }
        );

        while (true) {
            $this->client->process();
            usleep(1000);
        }

    }

    public function originate($number)
    {

        $action = new \PAMI\Message\Action\OriginateAction('SIP/' . $number);

        $action->setContext('incoming');
        $action->setPriority('1');
        $action->setExtension('99999');
        $action->setAsync(true);

        $this->client->send($action);

    }

}