<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

/**
 * アドレス帳
 */
class AddressBookController extends Controller
{

    /**
     * アドレス帳の検索
     * @param Request $req
     * @return type
     */
    public function search(Request $req)
    {

        $column = ['id', 'type', 'position', 'name_kana', 'name', 'tel1', 'tel2', 'tel3', 'email', 'comment', 'avatar_type', 'avatar_filename'];
        $typeId = intval($req['typeId']) ? intval($req['typeId']) : -1;

        $items = \App\AddressBook::select($column)
            ->where('type', $typeId);

        // 種別が個人の場合：ログイン中 ユーザの物のみを対象とする
        if ($typeId == 9) {
            $user = \Auth::user();
            $items = $items
                ->where('owner_userid', $user['id']);
        }

        // グループで絞り込み
        if (intval($req['groupId'])) {
            $items = $items
                ->where('groupid', $req['groupId']);
        }

        // キーワードで絞り込み
        if (strlen($req['keyword']) != 0) {
            $items = $items
                ->where(function ($query) use ($req) {
                    $query
                        ->orWhere('position', 'like', '%' . $req['keyword'] . '%')
                        ->orWhere('name', 'like', '%' . $req['keyword'] . '%')
                        ->orWhere('name_kana', 'like', '%' . $req['keyword'] . '%')
                        ->orWhere('tel1', 'like', '%' . $req['keyword'] . '%')
                        ->orWhere('tel2', 'like', '%' . $req['keyword'] . '%')
                        ->orWhere('tel3', 'like', '%' . $req['keyword'] . '%')
                        ->orWhere('email', 'like', '%' . $req['keyword'] . '%')
                        ->orWhere('comment', 'like', '%' . $req['keyword'] . '%');
                });
        }

        $sort = explode('|', $req['sort']);
        // Sort
        if (is_array($sort) && in_array($sort[0], $column) && in_array($sort[1], array('desc', 'asc'))) {
            $items = $items
                ->orderBy($sort[0], $sort[1]);
        }

        $per_page = intval($req['per_page']) ? $req['per_page'] : 10;

        $items = $items->paginate($per_page);

        // プレゼンス取得
//        foreach ($data as $key => &$value) {
//            foreach (['tel1', 'tel2', 'tel3'] as $item) {
//                // 内線と思われる物だけ取得
//                if (substr($value[$item], 0, 1) != 0) {
//                    $status = \Redis::GET('extStatus:' . $value[$item]);
//                    $value[$item . '_status'] = $status == null ? 'unknown' : $status;
//                }
//            }
//        }

        return \Response::json($items);

    }
}