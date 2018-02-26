<?php

namespace App\Http\Controllers\Api\v1;

use App\Facades\PbxLinker;
use Illuminate\Http\Request;
use App\Http\Requests;

/**
 * PBX Linker Controller
 */
class PbxLinkerController extends Controller
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
     * @todo 外線発信特番を設定で変更出来るようにする
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function originate(Request $request)
    {

        $addressbook = \App\AddressBook::where('type', 1)
            ->where('owner_userid', \Auth::user()->id)
            ->get()
            ->first();

        // ユーザがアドレス帳を所有しているか、また内線番号の設定があるかチェック
        if (!$addressbook || !$addressbook->tel1) {
            return response([
                'message' => 'あなたはPBX連携が設定されていないため、発信できません。',
                'status' => 'error',
            ], 403);
        }

        // 相手先番号が数値かどうかチェック
        if (!is_numeric($request['number'])) {
            return response([
                'message' => '発信先が電話番号ではないため、発信できません。',
                'status' => 'error',
            ], 422);
        }

        $number = $request['number'];

        // 先頭が0の場合は0を付加する
        if (starts_with($number, '0')) {
            $number = sprintf('0%s', $number);
        }

        // 発信
        $result = PbxLinker::originate($addressbook->tel1, $number);

        return response([
            'status' => $result ? 'success' : 'error',
        ]);

    }

    /**
     * 不在転送設定
     */
    public function forward(Request $request)
    {

        // PBX側の機能有無を確認
        if(!PbxLinker::isEnabledSetCallForward()){
            return response([
                'message' => 'PBX連携が設定されていないため、設定できません。',
                'status' => 'error',
            ], 403);
        }

        $addressbook = \App\AddressBook::where('type', 1)
            ->where('owner_userid', \Auth::user()->id)
            ->get()
            ->first();

        $extNumber = $request['ExtNumber'];
        $number = $request['Number'];

        // ユーザがアドレス帳を所有しているか、また内線番号の設定があるかチェック
        if (!$addressbook || !$addressbook->checkExtNumber($extNumber)) {
            return response([
                'message' => 'あなたはPBX連携が設定されていないため、設定できません。',
                'status' => 'error',
            ], 403);
        }

        // 相手先番号が数値かどうかチェック
        if ($number != '' && !is_numeric($number)) {
            return response([
                'message' => '転送先が電話番号ではないため、設定できません。',
                'status' => 'error',
            ], 422);
        }

        // 設定
        $result = PbxLinker::setCallForward($extNumber, $number);

        return response([
            'status' => $result ? 'success' : 'error',
        ]);

    }

}
