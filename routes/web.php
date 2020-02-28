<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 * ログイン情報を復元
 * - Session情報からJWT Tokenを生成し、ログイン情報を引き継ぐ
 * - この部分だけ、Sessionを利用するため、Middlewareにauth:webを設定
 */
Route::get('/loginRestore', function () {
    // ToDo エラー処理
    return redirect()->away('/login?mode=restore&token='. auth('api')->login(\Auth::user()));
})->middleware('auth:web');

// Vue向けのルート
// extensions/ broadcasting/ で始まるリクエストは処理しない
Route::get('/{vue?}', function () {
    return view('index');
})->where('vue', '(?!extensions/|broadcasting/)[\/\w\.-]*');

