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

Route::get('/authenticate', 'JwtAuthenticateController@index');
Route::post('/authenticate', 'JwtAuthenticateController@authenticate');

Route::get('/cdr/search', 'CdrController@search');

Route::get('/{vue?}', function () {
    return view('index');
})->where('vue', '[\/\w\.-]*');
