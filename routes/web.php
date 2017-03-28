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

// ログイン情報を復元
Route::get('/loginRestore', function () {
    // ToDo エラー処理
    return redirect()->away('/login?mode=restore&token='. JWTAuth::fromUser(\Auth::user()));
});

// Vue向けのルート
// saml2/ broadcasting/ で始まるリクエストは処理しない
Route::get('/{vue?}', function () {
    return view('index');
})->where('vue', '(?!saml2/|broadcasting/)[\/\w\.-]*');

