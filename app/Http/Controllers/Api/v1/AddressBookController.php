<?php

namespace App\Http\Controllers\Api\v1;

use App\AddressBook;
use Illuminate\Http\Request;
use App\Http\Requests;
use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\Interpreter;
use Goodby\CSV\Import\Standard\LexerConfig;

/**
 * アドレス帳
 */
class AddressBookController extends Controller
{

    // ToDo: グループ取得処理が多すぎるため、見直し必要

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
                'message' => '404 Not Found'
            ], 404);
        }

    }

    /**
     * アドレス帳をCSVでダウンロードさせる
     * @param Request $request
     * @return \Illuminate\Http\Response
     * ToDo: タイプ別の処理に分ける
     */
    public function download(Request $request)
    {

        switch ($request['downloadType']) {
            case 'standard':
                $items = $this->_downloadStandard($request);
                break;
            case 'hitachi-phs':
                $items = $this->_downloadHitachiPhs($request);
                break;
            default:
                return response([
                    'status' => 'error',
                    'message' => '404 Not Found'
                ], 400);
                break;
        }

        // 一時的にストリームを作成
        $stream = fopen('php://temp', 'w');

        // CSVで書き出し
        foreach ($items as $item) {
            fputcsv($stream, $item);
        }

        // ファイルポインタを千頭に戻す
        rewind($stream);

        // 改行コードを \r\n に置き換える
        $csv = str_replace(PHP_EOL, "\r\n", stream_get_contents($stream));

        // HTTPヘッダー
        // PHPではUTF-8で出力し、JavaScript側でShiftJISに変換する
        $headers = array(
            'Content-Type' => 'text/csv;',
            'Content-Disposition' => 'attachment; filename="addressbook.csv"',
        );

        // レスポンスを返す
        return \Response::make($csv, 200, $headers);

    }

    /**
     * 内部処理：標準形式でダウンロード用 配列を生成
     * @param Request $request
     * @return array
     */
    private function _downloadStandard(Request $request)
    {

        $items = $this
            ->_getItems($request)
            ->get()
            // 不要な項目を排除 / ModelのAppends
            ->makeHidden(['group_name', 'tel1_status', 'tel2_status', 'tel3_status', 'avatar_path'])
            ->toArray();

        // CSVファイルの先頭に付けるヘッダー
        $csvHeader = ['アドレス帳ID', 'アドレス帳種別ID', '所有者ユーザID', '所属グループID', '役職', 'フリガナ', '名前', '電話番号1', '電話番号2', '電話番号3', 'メールアドレス', '備考'];
        // 配列に追加する
        array_unshift($items, $csvHeader);

        return $items;

    }

    /**
     * 内部処理：日立PHS形式でダウンロード用 配列を生成
     * @param Request $request
     * @return array
     */
    private function _downloadHitachiPhs(Request $request)
    {

        $items = $this
            ->_getItems($request)
            // 必要な項目を選択
            ->select('name', 'name_kana', 'tel1', 'tel2', 'tel3')
            ->get()
            // 不要な項目を排除 / ModelのAppends
            ->makeHidden(['group_name', 'tel1_status', 'tel2_status', 'tel3_status', 'avatar_path'])
            ->toArray();

        $telItemCount = 0;

        for ($i = 0; $i < count($items); $i++) {
            // 読み仮名を半角カナにする
            $items[$i]['name_kana'] = mb_convert_kana($items[$i]['name_kana'], 'rnhks');

            // 先頭が0の場合は、外線と見なし0を付加
            // ToDo: システム側の判定と合わせた方が良い
            if (substr($items[$i]['tel1'], 0, 1) === '0') {
                $items[$i]['tel1'] = '0' . $items[$i]['tel1'];
            }
            if (substr($items[$i]['tel2'], 0, 1) === '0') {
                $items[$i]['tel2'] = '0' . $items[$i]['tel2'];
            }
            if (substr($items[$i]['tel3'], 0, 1) === '0') {
                $items[$i]['tel3'] = '0' . $items[$i]['tel3'];
            }

            // 電話番号数のカウント
            if ($items[$i]['tel1']) {
                $telItemCount++;
            }
            if ($items[$i]['tel2']) {
                $telItemCount++;
            }
            if ($items[$i]['tel3']) {
                $telItemCount++;
            }

            // 登録可能件数のチェック
            if ($telItemCount >= 1000) {
                return response([
                    'status' => 'error',
                    'message' => '電話番号の登録数が1000件を超えたため、ダウンロード出来ません。'
                ], 400);
            }

            // 文字数制限

            // 名前はSJIS換算で16バイトにしたいため、いったんSJISに変換
            $name = mb_strcut(mb_convert_encoding($items[$i]['name'], 'SJIS'), 0, 16, 'SJIS');
            $items[$i]['name'] = mb_convert_encoding($name, mb_internal_encoding(), 'SJIS');

            $items[$i]['name_kana'] = mb_substr($items[$i]['name_kana'], 0, 16);
            $items[$i]['tel1'] = substr($items[$i]['tel1'], 0, 24);
            $items[$i]['tel2'] = substr($items[$i]['tel2'], 0, 24);
            $items[$i]['tel3'] = substr($items[$i]['tel3'], 0, 24);

            // メモリ番号追加
            array_unshift($items[$i], $i);
            // グループ追加
            $items[$i][] = '0';
        }

        // CSVファイルの先頭に付けるヘッダー
        $csvHeader = ['ﾒﾓﾘ番号', '名前', '読み仮名', '電話番号１', '電話番号２', '電話番号３', 'ｸﾞﾙｰﾌﾟ'];
        // 配列に追加する
        array_unshift($items, $csvHeader);

        return $items;

    }

    /**
     * データベースからアイテムを取得
     * @param Request $req
     * @return mixed
     */
    private function _getItems(Request $req)
    {

        $column = ['id', 'type', 'owner_userid', 'groupid', 'position', 'name_kana', 'name', 'tel1', 'tel2', 'tel3', 'email', 'comment'];
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

        return $items;

    }

    /**
     * アドレス帳の検索
     * @param Request $req
     * @return type
     */
    public function search(Request $req)
    {

        $items = $this->_getItems($req);

        $per_page = intval($req['per_page']) ? $req['per_page'] : 10;

        $items = $items->paginate($per_page);

        return \Response::json($items);

    }

    /**
     * アドレス帳のインポート
     * @param Requests\AddressBookImportRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function import(\App\Http\Requests\AddressBookImportRequest $request)
    {

        // 権限チェック
        if (!\Entrust::can('system-admin')) {
            abort(403);
        }

        if ($request->file('import_file')->isValid([])) {
            $filename = $request->import_file->store('tmp/');

            $config = new LexerConfig();
            $config
                ->setDelimiter(',')
                // 1行目はヘッダー行のため、スキップ
                ->setIgnoreHeaderLine(true);

            $interpreter = new Interpreter();
            $interpreter->addObserver(function (array $columns) {
                // アップロードされたファイルの文字コードはS-JISと想定
                // ToDo: アップロードされたファイルの文字コードを確認した方が良い
                mb_convert_variables('UTF-8', 'SJIS', $columns);

                // ToDo: Validation
                $record = \App\AddressBook::firstOrNew(['id' => $columns[0]]);
                $record->type = $columns[1];
                $record->owner_userid = $columns[2];
                $record->groupid = $columns[3];
                $record->position = $columns[4];
                $record->name_kana = $columns[5];
                $record->name = $columns[6];
                $record->tel1 = $columns[7];
                $record->tel2 = $columns[8];
                $record->tel3 = $columns[9];
                $record->email = $columns[10];
                $record->comment = $columns[11];

                $record->save();
            });

            $lexer = new Lexer($config);
            $lexer->parse(storage_path('app/' . $filename), $interpreter);

            return response([
                'status' => 'success',
                'message' => 'アドレス帳のインポートが完了しました。'
            ]);
        } else {
            return response([
                'status' => 'error',
                'message' => 'アドレス帳のインポートに失敗しました。'
            ], 400);
        }

    }

    /**
     * グループ情報を出力
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function groupList(Request $request)
    {

        // 末端のグループのみを表示するか
        $isAll = $request['isAll'] ? true : false;

        $dbGroups = \App\AddressBookGroup::all();

        $result = [];

        foreach ($dbGroups as $group) {
            // 末端のグループのみを表示する場合、子供が有るグループは処理しない
            if ($isAll == false && count($group->childs)) {
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
     * グループを出力
     * @param Request $req
     * @return \Illuminate\Http\JsonResponse
     */
    public function group(Request $request)
    {
        $groupId = intval($request['groupId']);
        $dbGroup = \App\AddressBookGroup::find($groupId);

        return \Response::json($dbGroup);
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
        // 権限チェック
        if (!\Entrust::can('addressbook-admin')) {
            abort(403);
        }

        $id = intval($request['id']);

        $address = \App\AddressBook::find($id);

        // 権限が無い場合は、個人電話帳のみとする
        // ToDo 所有者チェック
        if (!\Entrust::can('addressbook-admin') && $address['type'] != 9) {
            return response([
                'status' => 'error',
                'message' => '選択された連絡先を削除する権限がありません。'
            ], 403);
        }

        $address->delete();

        return response([
            'status' => 'success',
            'message' => '選択された連絡先を削除しました。'
        ]);

    }

    /**
     * グループの削除
     * @param $inputId int
     * @return type
     */
    public function groupDelete(Request $request)
    {
        // 権限チェック
        if (!\Entrust::can('addressbook-admin')) {
            abort(403);
        }

        $id = intval($request['groupId']);

        $group = \App\AddressBookGroup::find($id);

        $ItemCount = \App\AddressBook::where('type', $group->type)
            ->where('groupid', $group->id)
            ->count();

        // 権限が無い場合は、個人電話帳のみとする
        // ToDo 所有者チェック
        if (!\Entrust::can('addressbook-admin') && $group['type'] != 9) {
            abort(403);
        }

        if ($ItemCount == 0 && count($group->childs) == 0) {
            $group->delete();
            return response([
                'type' => 'success',
                'message' => '選択されたグループを削除しました。'
            ]);
        } else {
            return response([
                'type' => 'error',
                'message' => '該当グループに所属する電話帳があるか、子グループが存在するため、削除出来ません。'
            ]);
        }

    }

    /**
     * グループ 編集
     * @param AddressBookGroupRequest|Requests\AddressBookGroupRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function groupEdit(\App\Http\Requests\AddressBookGroupRequest $request)
    {
        // 権限チェック
        // 権限が無い場合は、個人電話帳のみとする
        if (!\Entrust::can('addressbook-admin') && $request['type'] != 9) {
            abort(403);
        }

        $id = intval($request['id']);

        $record = \App\AddressBookGroup::firstOrNew(['id' => $id]);
        $record->type = $request['type'];
        $record->parent_groupid = $request['parent_groupid'];
        $record->group_name = $request['group_name'];
        $record->save();

        return response([
            'status' => 'success',
            'message' => 'グループの追加・編集が完了しました。'
        ]);

    }

    /**
     * アドレス帳 編集
     * @param Request $req
     */
    public function edit(\App\Http\Requests\AddressBookRequest $request)
    {

        // 権限が無い場合は、個人電話帳のみとする
        if (!\Entrust::can('addressbook-admin') && $request['type'] != 9) {
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
        // 数値で無い場合は0とする
        $record->owner_userid = intval($request['owner_userid']) ? intval($request['owner_userid']) : 0;
        $record->save();

        return response([
            'status' => 'success',
            'message' => '連絡先の追加・編集が完了しました。'
        ]);

    }
}