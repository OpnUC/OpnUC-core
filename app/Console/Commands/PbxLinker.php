<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class PbxLinker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:pbxLinker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pbx Linker';

    /**
     * Create a new command instance.
     *
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
        $this->info('Starting PbxLinker Process...');

        $this->line('Setting up default socket time out=-1');
        // ToDo
        // 環境によってうまく動作しない
        ini_set("default_socket_timeout", -1);

        $this->line('Subscribe Redis Channel:PresenceChannelWhisper');

        // ToDo:テナント
        Redis::subscribe(['PresenceChannelWhisper'], function ($event) {

            $obj = json_decode($event);
            $e = $obj->event;
            //{
            //   "event":{
            //      "event":"client-PresenceUpdated",
            //      "channel":"private-LinkerChannel",
            //      "data":{
            //         "extNumber":"89353",
            //         "status":"busy"
            //      },
            //      "socket":"xxxx"
            //   }
            //}

            // PbxLinkerChannelか確認
            if($e->channel != 'private-PbxLinkerChannel'){
                return;
            }

            // イベントで処理分け
            switch ($e->event){
                case 'client-PresenceUpdated':
                    Log::debug('Recieve PresenceUpdated Event: Ext=' . $e->data->extNumber . '/Status='. $e->data->status);
                    $cacheKey = config('opnuc.presence_cache_key_prefix') . $e->data->extNumber;

                    // 期限なくキャッシュに入れる
                    Cache::forever($cacheKey, $e->data->status);
                    break;
            }
        });

    }
}
