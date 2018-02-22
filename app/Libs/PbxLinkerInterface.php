<?php

namespace App\Libs;

use Closure;

interface PbxLinkerInterface
{

    /**
     * プレゼンス情報の更新
     * @return void
     */
    public function processPresence();

    /**
     * 不在転送設定
     * @param $ExtNumber string 内線番号(SYSG付き)
     * @param $number string 転送先番号
     * @return bool
     */
    public function setCallForward($ExtNumber, $number = '');

    /**
     * 発信
     * @param $ExtNumber string
     * @param $number string
     * @return bool
     */
    public function originate($ExtNumber, $number);

}