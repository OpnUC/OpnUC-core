<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Requests;

/**
 * ユーザ情報
 */
class UserController extends Controller
{

    /**
     * constructor
     */
    public function __construct()
    {

        // ミドルウェアの指定
        $this->middleware('jwt.auth');

    }

    /**
     * ユーザ情報 編集
     * @param Requests\UserRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function edit(\App\Http\Requests\UserRequest $request)
    {

        $id = \Auth::User()->id;

        // ToDo: メールアドレスの変更は確認した方がいい

        $record = \App\User::firstOrNew(['id' => $id]);
        $record->display_name = $request['display_name'];
        $record->email = $request['email'];
        $record->avatar_type = $request['avatar_type'];
        $record->save();

        return response([
            'status' => 'success',
            'message' => 'ユーザ情報の編集が完了しました。'
        ]);

    }

    /**
     * パスワード変更
     */
    public function passwordChange(\App\Http\Requests\UserPasswordChangeRequest $request)
    {

        $user = \Auth::User();

        if (\Hash::check($request->password, $user->password)) {
            $user->fill([
                'password' => \Hash::make($request->newPassword)
            ])->save();

            return response([
                'status' => 'success',
                'message' => 'パスワードの変更が完了しました。'
            ]);
        } else {
            return response([
                'status' => 'error',
                'message' => '現在のパスワードが正しくありません。'
            ]);
        }

    }

    /**
     * アバター画像のアップロード
     * @param Requests\UserUploadAvatarRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function uploadAvatar(\App\Http\Requests\UserUploadAvatarRequest $request)
    {

        if ($request->file('avatar_file')->isValid([])) {
            $filename = $request->avatar_file->store('public/avatars');

            $user = \App\User::find(\Auth::user()->id);

            // 既存の画像ファイルを削除
            if ($user->avatar_filename != '' && \Storage::exists('public/avatars/' . $user->avatar_filename)) {
                \Storage::delete('public/avatars/' . $user->avatar_filename);
            }

            $user->avatar_filename = basename($filename);
            $user->save();

            return response([
                'status' => 'success',
                'path' => \Storage::url('public/avatars/' . $user->avatar_filename),
                'message' => 'アバター画像のアップロードが完了しました。'
            ]);
        } else {
            return response([
                'status' => 'error',
                'message' => 'アバター画像のアップロードに失敗しました。'
            ],400);
        }


    }

}
