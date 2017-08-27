<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use \Carbon\Carbon;

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

        // CALL以外を削除
        \App\CdrBuffer::where('type', '!=', 'CALL')->delete();

        $cdrs = \App\CdrBuffer::all();

        foreach ($cdrs as $bufCdr) {
            Log::info("### Begen of Record");

            //// 生ログをパース
            /// CALL: 17/08/18 11:50:03 000702(001)0543452995 -> 299 11:49:09 - 11:50:03 (00:00:54)
            /// CALL: 17/08/19 10:24:47 410 -> 003311(001) 10:18:19 - 10:24:47 (00:06:28) 090xxxxxxxx
            /// CALL: 17/08/16 11:49:03 310 -> 481 11:48:40 - 11:49:03 (00:00:23)
            // 1:転送フラグ
            // 2:日時
            // 3:通話元
            // 4:通話先など
            if (!preg_match("/(TS|TE)?\s*(\d\d\/\d\d\/\d\d \d\d:\d\d:\d\d) (.+) \-> (.+) (\d\d:\d\d:\d\d) \- (\d\d:\d\d:\d\d) \((\d\d:\d\d:\d\d)\) *(.+)?/", $bufCdr->message, $parse)) {
                Log::error("Log Parse Error[1000]: $bufCdr->message");
                continue;
            }

            Log::debug("Parse Result[1000]");
            Log::debug(print_r($parse, true));

            // ログ生成日をパース(年月日はこの情報を利用する)
            $date = date_parse_from_format('y/m/d H:i:s', $parse[2]);

            $cdr = new \App\Cdr;
            $cdr->type = 0;
            $cdr->end_datetime = Carbon::create($date['year'], $date['month'], $date['day'], 0, 0, 0)->addSeconds(self::convTime($parse[6]));
            $cdr->duration = self::convTime($parse[7]);
            // 開始日は日付を跨ぐ可能性があるため、通話終了から通話時間を引く
            $cdr->start_datetime = $cdr->end_datetime->subSecond($cdr->duration);

            // 発信元が 000702(001)0543452995 の場合、ルート情報などをパース
            if (preg_match('/^(\d+)\((\d+)\)(.+)$/', $parse[3], $parseSender)) {
                $cdr->sender = sprintf('%s(RUT:%s/%s)', $parseSender[3], $parseSender[2], $parseSender[1]);
            } else {
                $cdr->sender = $parse[3];
            }

            // パースした結果が9個なら、8個目が通話先の番号
            if (count($parse) == 9 && preg_match('/^(\d+)\((\d+)\)$/', $parse[4], $parseDest)) {
                $cdr->destination = sprintf('%s(RUT:%s/%s)', $parse[8], $parseDest[2], $parseDest[1]);
            } else {
                $cdr->destination = $parse[4];
            }

            $cdr->save();

            // 処理が終了したバッファレコードは削除
            $bufCdr->delete();

            Log::info("### End of Record");
        }

    }

    /**
     * HH:MM:SSを秒数に変換
     * @param string $value
     * @return int
     */
    public function convTime($value)
    {
        $item = explode(':', $value);
        $secs = intval($item[0]) * 60 * 60;
        $secs += intval($item[1]) * 60;
        $secs += intval($item[2]);
        return $secs;
    }
}
