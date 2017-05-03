<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Requests;

/**
 * Click to Call Controller
 */
class Click2CallController extends Controller
{

    /**
     * constructor
     */
    public function __construct()
    {

        // ミドルウェアの指定
        $this->middleware('jwt.auth');

    }

    /**
     * 発信処理
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function originate(Request $request)
    {

        $addressbook = \App\AddressBook::where('type', 1)
            ->where('owner_userid', \Auth::user()->id)
            ->get()
            ->first();

        // ユーザに内線番号の設定があるかチェック
        if(!$addressbook->tel1){
            return response([
                'message' => 'あなたはPBX連携が設定されていないため、発信できません。',
                'status' => 'error',
            ], 403);
        }

        // 相手先番号が数値かどうかチェック
        if(!is_numeric($request['number'])){
            return response([
                'message' => '発信先が電話番号ではないため、発信できません。',
                'status' => 'error',
            ], 422);
        }

        // 発信
        $result = \App\Facades\PbxLinker::originate($addressbook->tel1, $request['number']);

        return response([
            'status' => $result ? 'success' : 'error',
        ]);

    }

}
