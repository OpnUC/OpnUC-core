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
     * constructor
     */
    public function __construct()
    {
        // ミドルウェアの指定
        $this->middleware('jwt.auth');
    }

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

    /**
     * グループ一覧を出力
     * @param Request $req
     * @return \Illuminate\Http\JsonResponse
     */
    public function groups(Request $req)
    {

        $typeId = intval($req['typeId']);

        $dbGroups = \App\AddressBookGroup::where('parent_groupid', 0)
            ->where('type', $typeId)
            ->get();

        $groups = $this->_buildGroups($dbGroups);

        return \Response::json($groups);
    }

    /**
     * グループ一覧を取得する処理の再帰部分
     * @param $groups
     * @return array|null
     */
    private function _buildGroups($groups)
    {
        $result = null;

        // 親グループ
        foreach ($groups as $Group) {
            $result_child = null;

            // 子グループ
            foreach ($Group->childs as $item) {
                // ToDo: 個人電話帳の考慮してない
                $ItemCount = \App\AddressBook::where('type', $item->type)
                    ->where('groupid', $item->id)
                    ->count();
                $result_child[] = array(
                    'Id' => $item->id,
                    'Name' => $item->group_name,
                    'ItemCount' => $ItemCount,
                    // 孫グループがある場合、再帰処理
                    'Child' => $item->childs->count() ? $this->_buildGroups($item->childs) : null,
                );
            }

            // ToDo: 個人電話帳の考慮してない
            $ItemCount = \App\AddressBook::where('type', $Group->type)
                ->where('groupid', $Group->id)
                ->count();
            $result[] = array(
                'Id' => $Group->id,
                'Name' => $Group->group_name,
                'ItemCount' => $ItemCount,
                'Child' => $result_child,
            );
        }

        return $result;
    }
}