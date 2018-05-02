<?php

namespace App\Http\Controllers\Api\v1;

use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{

    /**
     * ログイン中のユーザ情報を返す
     * @return \Illuminate\Http\JsonResponse
     */
    public function user()
    {

        return response()->json([
            'status' => 'success',
            'data' => auth()->user()
        ]);

    }

    /**
     * ログイン処理
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function login(Request $request)
    {

        $token = null;

        // mode が restore の場合は、ログイン済みとして、Tokenで認証を試みる
        if ($request['mode'] === 'restore' && $request['token']) {
            // Tokenからユーザを取得
            $token = auth()->tokenById(auth()->user()->id);

            if ($token === null) {
                return response([
                    'status' => 'error',
                    'error' => 'invalid.credentials',
                    'message' => '認証に失敗しました。'
                ], 401);
            }
        } else {
            $credentials = $request->only('username', 'password');

            try {
                // 認証情報をチェック
                if (!$token = auth('api')->attempt($credentials)) {
                    return response([
                        'status' => 'error',
                        'error' => 'invalid.credentials',
                        'message' => 'ユーザ名もしくはパスワードが正しくありません。'
                    ], 401);
                }
            } catch (JWTException $e) {
                return response()->json(['error' => 'could_not_create_token'], 500);
            }
        }

        return response([
            'status' => 'success',
        ])
            ->header('Authorization', $token);
    }

    /**
     * ログアウト
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function logout()
    {
        auth()->logout();

        return response([
            'status' => 'success',
            'msg' => 'Logged out Successfully.'
        ], 200);
    }

    /**
     * リフレッシュ
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function refresh()
    {
        auth()->refresh();

        return response([
            'status' => 'success'
        ]);
    }

}