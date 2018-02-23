<?php

namespace App\Console\Commands;

use App\Cdr;
use Illuminate\Console\Command;

/**
 * 発着信履歴のパース
 * Class CdrProcess
 * @package App\Console\Commands
 */
class CdrProcess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:cdrProcess';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'CDR Process Command';

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

        // 実際の処理はベンダーにより異なるため、パッケージへ投げる
        $items = \App\Facades\PbxLinker::parseCdr();

        // データベースへ登録
        Cdr::insert($items);

    }

}
