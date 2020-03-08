<?php

/**
 * OpnUC アプリケーション設定
 */

return [

    //// PBXとの連携
    // true: 有効, false: 無効
    'pbx_linker' => env('PBX_LINKER_ENABLE', false),

    //// Click to Callの利用
    // true: 有効, false: 無効
    'enable_c2c' => env('PBX_C2C_ENABLE', false),

    //// 電話 プレゼンス情報
    // true: 有効, false: 無効
    'enable_tel_presence' => env('PBX_PRESENCE_ENABLE', false),

    'presence_cache_key_prefix' => env('PBX_PRESENCE_CACHE_KEY_PREFIX', 'OpnUC_PresenceCache:'),

    //// 電話 不在転送設定
    // true: 有効, false: 無効
    'enable_set_callforward' => env('PBX_SET_CALLFORWARD_ENABLE', false),

];
