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

Route::get('/cdr/search', 'CdrController@search');

Route::get('/{vue?}', function () {
    return view('index');
})->where('vue', '[\/\w\.-]*');
