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

        // 相手先番号が数値かどうか、チェック
        if(!is_numeric($request['number'])){
            abort(400);
        }

        // イベントを発火させる
        \Event::fire(new \App\Events\Click2Call($request['number']));

        return response([
            'status' => 'success',
        ]);

    }

}
