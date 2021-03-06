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
     * プレゼンス情報の取得
     * @param $ExtNumber
     * @return mixed
     */
    public function getPresence($ExtNumber);

    /**
     * 不在転送設定が利用可能か
     * @return bool
     */
    public function isEnabledSetCallForward();

    /**
     * 不在転送設定
     * @param $ExtNumber string 内線番号(SYSG付き)
     * @param $number string 転送先番号
     * @return bool
     */
    public function setCallForward($ExtNumber, $number = '');

    /**
     * 発信が利用可能か
     * @return bool
     */
    public function isEnabledOriginate();

    /**
     * 発信
     * @param $ExtNumber string
     * @param $number string
     * @return bool
     */
    public function originate($ExtNumber, $number);

    public function parseCdr();

}