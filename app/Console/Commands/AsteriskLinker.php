<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PAMI\Message\Event\EventMessage;
use PAMI\Listener\IEventListener;
use PAMI\Message\Event\UnknownEvent;

class AsteriskLinker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:asteriskLinker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asterisk Linker';

    private $client;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
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

        $this->client = new \PAMI\Client\Impl\ClientImpl(options);

        $this->client->open();

        $this->client->registerEventListener(
            function (EventMessage $event) {

                //event(new \App\Events\PresenceUpdated($this->argument('ext'), $this->argument('status')));

                echo $event->getKey('device') . ':';
                echo $event->getKey('state') . "\n";
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

        $this->client->close();

    }
}
