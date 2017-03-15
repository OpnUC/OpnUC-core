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

Route::get('/auth/user', 'AuthController@user');
Route::get('/auth/refresh', 'AuthController@refresh');
Route::post('/auth/login', 'AuthController@login');
Route::post('/auth/logout', 'AuthController@logout');

Route::post('/auth/resetPasswordEmail', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::post('/auth/resetPassword', 'Auth\ResetPasswordController@reset');

Route::get('/cdr/search', 'CdrController@search');
Route::get('/addressbook/search', 'AddressBookController@search');
Route::get('/addressbook/groups', 'AddressBookController@groups');

// Vue向けのルート
// saml2/　で始まるリクエストは処理しない
Route::get('/{vue?}', function () {
    return view('index');
})->where('vue', '(?!saml2/)[\/\w\.-]*');
