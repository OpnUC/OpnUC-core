<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtAuthenticateController extends Controller
{

    /**
     * constructor
     */
    public function __construct(){

        // ミドルウェアの指定と、例外の指定
        $this->middleware('jwt.auth', ['except' => ['authenticate']]);

    }

    public function index()
    {

        return response()->json([
            'status' => 'success',
            'data' => Auth::User()
        ]);

    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only('username', 'password');

        try {
            // verify the credentials and create a token for the user
            if ( ! $token = JWTAuth::attempt($credentials)) {
                return response([
                    'status' => 'error',
                    'error' => 'invalid.credentials',
                    'msg' => 'Invalid Credentials.'
                ], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        return response([
            'status' => 'success',
        ])
            ->header('Authorization', $token);
    }

}