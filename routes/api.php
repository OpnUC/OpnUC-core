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

Route::group([
    'namespace' => 'Api\v1',
    'prefix' => 'v1'
], function () {

    // Guest
    Route::group([
        'middleware' => ['api'],
    ], function () {
        // Guest Access
        Route::post('/auth/resetPasswordEmail', 'Auth\ForgotPasswordController@sendResetLinkEmail');
        Route::post('/auth/resetPassword', 'Auth\ResetPasswordController@reset');
        Route::post('/auth/login', 'AuthController@login');

        // FrontEnd Error Reporting
        Route::post('/error/report', 'ErrorController@report');

        // Application Config
        Route::get('/config', 'ConfigController@index');
    });

    // Login Check
    Route::group([
        'middleware' => ['auth:api'],
    ], function () {
        // Token Refresh
        Route::get('/auth/refresh', 'AuthController@refresh');
        Route::get('/auth/user', 'AuthController@user');
        Route::post('/auth/logout', 'AuthController@logout');

        // システム管理
        Route::group([
            'middleware' => 'permission:system-admin',
            'prefix' => 'admin'
        ], function () {
            Route::get('permissions', 'AdminController@permissions');
            Route::get('roles', 'AdminController@roles');
            Route::get('role', 'AdminController@role');
            Route::post('roleEdit', 'AdminController@roleEdit');
            Route::post('roleDelete', 'AdminController@roleDelete');
            Route::get('users', 'AdminController@users');
            Route::get('user', 'AdminController@user');
            Route::post('userEdit', 'AdminController@userEdit');
            Route::post('userDelete', 'AdminController@userDelete');
            Route::get('settingNumberRewrites', 'AdminController@settingNumberRewrites');
            Route::get('settingNumberRewrite', 'AdminController@settingNumberRewrite');
            Route::post('settingNumberRewriteDelete', 'AdminController@settingNumberRewriteDelete');
            Route::post('settingNumberRewriteEdit', 'AdminController@settingNumberRewriteEdit');
        });

        Route::get('/user/users', 'UserController@users');
        Route::post('/user/edit', 'UserController@edit');
        Route::post('/user/passwordChange', 'UserController@passwordChange');
        Route::post('/user/uploadAvatar', 'UserController@uploadAvatar');
        Route::post('/user/deleteAvatar', 'UserController@deleteAvatar');

        Route::post('/pbxlinker/originate', 'PbxLinkerController@originate');
        Route::post('/pbxlinker/forward', 'PbxLinkerController@forward');

        // 発着信履歴
        Route::group([
            'middleware' => 'permission:cdr-user|cdr-superuser',
            'prefix' => 'cdr'
        ], function () {
            Route::get('search', 'CdrController@search');
            Route::get('download', 'CdrController@download');
        });

        // アドレス帳
        Route::group([
            'middleware' => 'permission:addressbook-user|addressbook-admin',
            'prefix' => 'addressbook'
        ], function () {
            Route::get('search', 'AddressBookController@search');
            Route::get('download', 'AddressBookController@download');
            Route::get('detail', 'AddressBookController@detail');
            Route::get('groupList', 'AddressBookController@groupList');
            Route::get('groups', 'AddressBookController@groups');
            Route::get('group', 'AddressBookController@group');

            // アドレス帳 管理者
            Route::group([
                'middleware' => 'permission:addressbook-admin',
            ], function () {
                // admin
                Route::post('import', 'AddressBookController@import');
                Route::post('delete', 'AddressBookController@delete');
                Route::post('edit', 'AddressBookController@edit');
                Route::post('groupEdit', 'AddressBookController@groupEdit');
                Route::post('groupDelete', 'AddressBookController@groupDelete');
            });
        });
    });
});
