<?php

namespace App\Http\Controllers\Api\v1;

use http\Client\Response;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

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

        $column = ['id', 'username', 'display_name', 'email', 'created_at'];

        $items = \App\User::select($column);

        // キーワードで絞り込み
        if (strlen($request['keyword']) != 0) {
            $items = $items
                ->where(function ($query) use ($request) {
                    $query
                        ->orWhere('username', 'like', '%' . $request['keyword'] . '%')
                        ->orWhere('display_name', 'like', '%' . $request['keyword'] . '%')
                        ->orWhere('email', 'like', '%' . $request['keyword'] . '%');
                });
        }

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

        $record = \App\User::find($id);
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

        $user = \App\User::firstOrNew(['id' => $id]);
        $user->username = $request['username'];
        $user->display_name = $request['display_name'];
        $user->email = $request['email'];

        if (strlen($request['password']) != 0) {
            // パスワードが入力されている場合、パスワードをセット
            $user->password = bcrypt($request['password']);
        }

        $user->save();

        // ロールを割り当てる
        $user->syncRoles($request['roles_name']);

        return response([
            'status' => 'success',
            'message' => 'ユーザの追加・編集が完了しました。'
        ]);

    }

    /**
     * ロール一覧
     */
    public function roles(Request $request)
    {

        $column = ['id', 'name', 'display_name', 'description'];

        $items = \App\Role::select($column);

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
     * ロール
     */
    public function role(Request $request)
    {

        $id = intval($request['id']);

        $user = \App\Role::find($id);

        return \Response::json($user);

    }

    /**
     * ロールの削除
     */
    public function roleDelete(Request $request)
    {

        $id = intval($request['id']);

        $role = \App\Role::find($id);

        if ($role->users()->get()->count() != 0) {
            return response([
                'status' => 'error',
                'message' => 'このロールに所属するユーザがいるため、削除出来ません。'
            ], 422);
        }

        $role->delete();

        return response([
            'status' => 'success',
            'message' => 'ロールの削除が完了しました。'
        ]);

    }

    /**
     * ロールの追加・編集
     * @param Requests\AdminRoleRequest $request
     * @return \Illuminate\Http\Response
     */
    public function roleEdit(\App\Http\Requests\AdminRoleRequest $request) : \Illuminate\Http\Response
    {

        $role = Role::findOrCreate($request['name']);
        $role->update($request->except(['perms_name', 'users_count']));

        $role->save();

        $role->syncPermissions($request['perms_name']);

        return response([
            'status' => 'success',
            'message' => 'ロールの追加・編集が完了しました。'
        ]);

    }

    /**
     * パーミッション一覧
     */
    public function permissions(Request $request)
    {

        $column = ['id', 'name', 'display_name', 'description'];

        $items = \App\Permission::select($column);

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
     * 番号変換
     */
    public function settingNumberRewrite(Request $request)
    {

        $id = intval($request['id']);

        $user = \App\SettingNumberRewrite::find($id);

        return \Response::json($user);

    }


    /**
     * 番号変換一覧
     */
    public function settingNumberRewrites(Request $request)
    {

        $column = ['id', 'pattern', 'replacement', 'description', 'display_replacement'];

        $items = \App\SettingNumberRewrite::select($column);

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
     * 番号変換の削除
     */
    public function settingNumberRewriteDelete(Request $request)
    {

        $id = intval($request['id']);

        $record = \App\SettingNumberRewrite::find($id);

        $record->delete();

        return response([
            'status' => 'success',
            'message' => '番号変換の削除が完了しました。'
        ]);

    }

    /**
     * 番号変換の追加・編集
     * @param Requests\AdminSettingNumberRewriteRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function settingNumberRewriteEdit(\App\Http\Requests\AdminSettingNumberRewriteRequest $request)
    {

        $id = intval($request['id']);

        $record = \App\SettingNumberRewrite::firstOrNew(['id' => $id]);
        $record->pattern = $request['pattern'];
        $record->replacement = $request['replacement'];
        $record->description = $request['description'];
        $record->display_replacement = $request['display_replacement'];

        $record->save();

        return response([
            'status' => 'success',
            'message' => '番号変換の追加・編集が完了しました。'
        ]);

    }

}