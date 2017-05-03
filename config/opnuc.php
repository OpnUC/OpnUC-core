<?php

/**
 * OpnUC アプリケーション設定
 */

return [

    //// PBXとの連携
    'pbx_linker' => [
        // true: 有効, false: 無効
        'enable' => true,
        //// PBX種類
        // Asterisk: Asterisk
        'type' => 'Asterisk',
        // typeがAstersikの場合の設定
        'asterisk' => [
            'host' => '',
            'port' => 5038,
            'username' => 'opnuc',
            'password' => 'opnuc',
            // Click to Callで使用するコンテキスト
            'originate_context' => 'opnucdemo',
            // Click to Callで通知するCallerID
            'originate_callerid' => 'Click 2 Call',
            // Click to Callで使用するチャンネルのPrefix
            'originate_channel_prefix' => 'SIP/opnucdemo_',
            // DeviceNameのPrefix(DeviceStateにて使用)
            //  [Prefix]内線番号 の形式
            'device_name_prefix' => 'SIP/opnucdemo_',
        ],
    ],

    //// Click to Callの利用
    // true: 有効, false: 無効
    'enable_c2c' => true,

    //// 電話 プレゼンス情報
    // true: 有効, false: 無効
    'enable_tel_presence' => true,

];
