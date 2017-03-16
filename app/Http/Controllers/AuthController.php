<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{

    /**
     * constructor
     */
    public function __construct()
    {

        // ミドルウェアの指定と、例外の指定
        $this->middleware('jwt.auth', ['except' => ['login']]);

    }

    public function user()
    {

        return response()->json([
            'status' => 'success',
            'data' => Auth::User()
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

        // mode が restore の場合は、Laravel上でログイン済みとして、Tokenの取得を試みる
        if($request['mode'] === 'restore'){
            $logginUser = Auth::user();

            if($logginUser === null){
                return response([
                    'status' => 'error',
                    'error' => 'invalid.credentials',
                    'message' => '認証に失敗しました。'
                ], 401);
            }

            $token = JWTAuth::fromUser($logginUser);
        }else{
            $credentials = $request->only('username', 'password');

            try {
                // verify the credentials and create a token for the user
                if (!$token = JWTAuth::attempt($credentials)) {
                    return response([
                        'status' => 'error',
                        'error' => 'invalid.credentials',
                        'message' => 'ユーザ名もしくはパスワードが正しくありません。'
                    ], 400);
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

    public function logout()
    {
        JWTAuth::invalidate();
        Auth::logout();

        return response([
            'status' => 'success',
            'msg' => 'Logged out Successfully.'
        ], 200);
    }

    public function refresh()
    {
        return response([
            'status' => 'success'
        ]);
    }

}