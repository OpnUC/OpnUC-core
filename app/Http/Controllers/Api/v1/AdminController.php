<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Requests;

/**
 * Admin Controller
 */
class AdminController extends Controller
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
     * ユーザ一覧
     */
    public function users(Request $request)
    {

        // 権限チェック
        if (!\Entrust::can('system-admin')) {
            abort(403);
        }

        $column = ['id', 'username', 'display_name', 'email', 'created_at'];

        $items = \App\User::select($column);

        $sort = explode('|', $request['sort']);
        // Sort
        if (is_array($sort) && in_array($sort[0], $column) && in_array($sort[1], array('desc', 'asc'))) {
            $items = $items
                ->orderBy($sort[0], $sort[1]);
        }

        $per_page = intval($request['per_page']) ? $request['per_page'] : 10;

        $items = $items->paginate($per_page);

        return \Response::json($items);

    }

    /**
     * ユーザ
     */
    public function user(Request $request)
    {

        // 権限チェック
        if (!\Entrust::can('system-admin')) {
            abort(403);
        }

        $id = intval($request['id']);

        $user = \App\User::find($id);

        return \Response::json($user);

    }

    /**
     * ユーザの削除
     */
    public function userDelete(Request $request)
    {

        // ToDo：電話帳が紐付いているときの処理をどうするか

        $id = intval($request['id']);

        $record = \App\User::firstOrNew(['id' => $id]);
        $record->delete();

        return response([
            'status' => 'success',
            'message' => 'ユーザの削除が完了しました。'
        ]);

    }

    /**
     * ユーザの追加・編集
     */
    public function userEdit(\App\Http\Requests\AdminUserRequest $request)
    {

        $id = intval($request['id']);

        $record = \App\User::firstOrNew(['id' => $id]);
        $record->username = $request['username'];
        $record->display_name = $request['display_name'];
        $record->email = $request['email'];

        if(strlen($request['password']) != 0){
            // パスワードが入力されている場合、パスワードをセット
            $record->password = bcrypt($request['password']);
        }

        $record->save();

        return response([
            'status' => 'success',
            'message' => 'ユーザの追加・編集が完了しました。'
        ]);

    }

    /**
     * ロール一覧
     */
    public function roles()
    {

        // 権限チェック
        if (!\Entrust::can('system-admin')) {
            abort(403);
        }

        $column = ['id', 'name', 'display_name', 'description'];

        $items = \App\Role::All($column);

        return \Response::json($items);

    }

}
