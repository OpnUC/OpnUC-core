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

        // mode が restore の場合は、ログイン済みとして、Tokenで認証を試みる
        if ($request['mode'] === 'restore' && $request['token']) {
            JWTAuth::setToken($request['token']);

            $logginUser = JWTAuth::parseToken()->authenticate();

            if ($logginUser === null) {
                return response([
                    'status' => 'error',
                    'error' => 'invalid.credentials',
                    'message' => '認証に失敗しました。'
                ], 401);
            }

            $token = JWTAuth::fromUser($logginUser);
        } else {
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