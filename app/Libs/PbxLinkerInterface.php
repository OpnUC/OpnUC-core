<?php

namespace App\Libs;

use Closure;

interface PbxLinkerInterface
{

    /**
     * プレゼンス情報の更新
     */
    public function processPresence();

    /**
     * 発信
     * @param $ext string
     * @param $number string
     * @return bool
     */
    public function originate($ext, $number);

}