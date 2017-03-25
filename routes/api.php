<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['namespace' => 'Api\v1', 'prefix' => 'v1'], function() {
    Route::get('/auth/user', 'AuthController@user');
    Route::get('/auth/refresh', 'AuthController@refresh')
        ->middleware('jwt.refresh');
    Route::post('/auth/login', 'AuthController@login');
    Route::post('/auth/logout', 'AuthController@logout');

    Route::post('/auth/resetPasswordEmail', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('/auth/resetPassword', 'Auth\ResetPasswordController@reset');

    Route::get('/cdr/search', 'CdrController@search');
    Route::get('/addressbook/search', 'AddressBookController@search');
    Route::get('/addressbook/detail', 'AddressBookController@detail');
    Route::get('/addressbook/groupList', 'AddressBookController@groupList');
    Route::get('/addressbook/groups', 'AddressBookController@groups');
    Route::get('/addressbook/group', 'AddressBookController@group');
    Route::post('/addressbook/edit', 'AddressBookController@edit');
    Route::post('/addressbook/groupEdit', 'AddressBookController@groupEdit');
    Route::post('/addressbook/delete', 'AddressBookController@delete');
    Route::post('/addressbook/groupDelete', 'AddressBookController@groupDelete');
});