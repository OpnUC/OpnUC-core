<?php

/**
 * OpnUC アプリケーション設定
 */

return [

    //// PBXとの連携
    'pbx_linker' => [
        // true: 有効, false: 無効
        'enable' => env('PBX_LINKER_ENABLE', false),
        //// PBX種類
        // Asterisk: Asterisk
        'type' => env('PBX_LINKER_TYPE'),
    ],

    //// Click to Callの利用
    // true: 有効, false: 無効
    'enable_c2c' => env('PBX_C2C_ENABLE', false),

    //// 電話 プレゼンス情報
    // true: 有効, false: 無効
    'enable_tel_presence' => env('PBX_PRESENCE_ENABLE', false),

];
