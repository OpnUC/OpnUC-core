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

    public function detail(Request $request)
    {

        $id = intval($request['id']) ? intval($request['id']) : 0;

        $item = \App\AddressBook::find($id);

        if ($item) {
            return \Response::json($item);
        } else {
            return response([
                'status' => 'error',
                'message' => '404 Not Found。'
            ], 404);
        }

    }

    /**
     * アドレス帳の検索
     * @param Request $req
     * @return type
     */
    public function search(Request $req)
    {

        $column = ['id', 'type', 'groupid', 'position', 'name_kana', 'name', 'tel1', 'tel2', 'tel3', 'email', 'comment', 'avatar_type', 'avatar_filename'];
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
        foreach ($items as $key => &$value) {
            $value['group_name'] = $value->GroupName();
//            foreach (['tel1', 'tel2', 'tel3'] as $item) {
//                // 内線と思われる物だけ取得
//                if (substr($value[$item], 0, 1) != 0) {
//                    $status = \Redis::GET('extStatus:' . $value[$item]);
//                    $value[$item . '_status'] = $status == null ? 'unknown' : $status;
//                }
//            }
        }

        return \Response::json($items);

    }

    /**
     * グループ情報を出力
     * @param Request $req
     * @return \Illuminate\Http\JsonResponse
     */
    public function groupList()
    {

        $dbGroups = \App\AddressBookGroup::all();

        $result = [];

        foreach ($dbGroups as $group){
            if(count($group->childs)){
                continue;
            }

            $result[$group['type']][$group['id']] = array(
                'id' => $group['id'],
                'group_name' => $group['group_name'],
                'full_group_name' => $group->FullGroupName(),
            );

        }

        return \Response::json($result);

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

    /**
     * 連絡先の削除
     * @param $inputId int
     * @return type
     */
    public function delete(Request $request)
    {

        $id = intval($request['id']);

        $address = \App\AddressBook::find($id);

        // 権限が無い場合は、個人電話帳のみとする
        // ToDo 所有者チェック
        if (!\Entrust::can('edit-addressbook') && $address['type'] != 9) {
            return response([
                'status' => 'error',
                'message' => '選択された連絡先を削除する権限がありません。'
            ]);
        }

        $address->delete();

        return response([
            'status' => 'success',
            'message' => '選択された連絡先を削除しました。'
        ]);

    }

    /**
     * アドレス帳 編集
     * @param Request $req
     */
    public function edit(\App\Http\Requests\AddressBookRequest $request)
    {

        // 権限が無い場合は、個人電話帳のみとする
        if (!\Entrust::can('edit-addressbook') && $request['type'] != 9) {
            abort(403);
        }

        $id = intval($request['id']);

        $record = \App\AddressBook::firstOrNew(['id' => $id]);
        $record->position = $request['position'];
        $record->name_kana = $request['name_kana'];
        $record->name = $request['name'];
        $record->type = $request['type'];
        $record->groupid = $request['groupid'];
        $record->tel1 = $request['tel1'];
        $record->tel2 = $request['tel2'];
        $record->tel3 = $request['tel3'];
        $record->email = $request['email'];
        $record->comment = $request['comment'];
        $record->save();

        return response([
            'status' => 'success',
            'message' => '連絡先の追加・編集が完了しました。'
        ]);

    }
}