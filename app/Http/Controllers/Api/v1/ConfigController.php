<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;

/**
 * フロントエンド向けにアプリケーションの設定情報を提供
 */
class ConfigController extends Controller
{

    public function index(Request $request)
    {

        $value = [
            'enable_saml2_auth' => config('saml2_settings.useSaml2Auth'),
            'enable_c2c' => config('opnuc.enable_c2c'),
            'enable_tel_presence' => config('opnuc.enable_tel_presence'),
        ];

        // JSONでレスポンスを返す
        return \Response::json($value);

    }
}
