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

// Vue向けのルート
// saml2/　で始まるリクエストは処理しない
Route::get('/{vue?}', function () {
    return view('index');
})->where('vue', '(?!saml2/|broadcasting/)[\/\w\.-]*');
