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

}
