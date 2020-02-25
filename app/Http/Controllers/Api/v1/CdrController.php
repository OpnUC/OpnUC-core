<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Requests;

/**
 * 発着信履歴
 */
class CdrController extends Controller
{

    /**
     * 発着信履歴をJSONで返す
     * @param Request $request
     * @return type
     * @internal param Request $req
     */
    public function search(Request $request)
    {

        // 1ページあたりの件数を取得
        // 無い場合は、10件とする
        $per_page = intval($request->input('per_page')) ? $request->input('per_page') : 10;

        // ページネーション
        $items = $this
            ->_getItems($request)
            ->paginate($per_page);

        // JSONでレスポンスを返す
        return \Response::json($items);

    }

    /**
     * 発着信履歴をCSVでダウンロードさせる
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function download(Request $request)
    {
        $items = $this
            ->_getItems($request)
            ->get()
            // 不要な項目を排除 / ModelのAppends
            ->makeHidden(['sender_name', 'destination_name'])
            ->toArray();

        // CSVファイルの先頭に付けるヘッダー
        $csvHeader = ['id', 'start_datetime', 'duration', 'sender', 'destination'];
        // 配列に追加する
        array_unshift($items, $csvHeader);

        // 一時的にストリームを作成
        $stream = fopen('php://temp', 'r+b');

        // CSVで書き出し
        foreach ($items as $user) {
            fputcsv($stream, $user);
        }

        // ファイルポインタを千頭に戻す
        rewind($stream);

        // 改行コードを \r\n に置き換える
        $csv = str_replace(PHP_EOL, "\r\n", stream_get_contents($stream));

        // HTTPヘッダー
        $headers = array(
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="cdr.csv"',
        );

        // レスポンスを返す
        return \Response::make($csv, 200, $headers);

    }

    /**
     * 発着信履歴のデータを取得する内部処理
     * @param Request $request
     * @return mixed
     * @internal param Request $req
     */
    private function _getItems(Request $request)
    {

        // 取得する列
        $column = ['id', 'start_datetime', 'duration', 'sender', 'sender_comment', 'destination', 'destination_comment'];

        $items = \App\Cdr::select($column);

        // 発信者が指定されている場合
        if ($request->has('sender')) {
            $items = $items
                ->where('sender', 'LIKE', '%' . $request->input('sender') . '%');
        }

        // 着信先が指定されている場合
        if ($request->has('destination')) {
            $items = $items
                ->where('destination', 'LIKE', '%' . $request->input('destination') . '%');
        }

        // 発着信履歴の特権ユーザか
        if (!\Auth::user()->can('cdr-superuser')) {
            $addressbook = \Auth::user()->address_book;

            // ToDo: アドレス帳情報が自分で変えられるため、連携用の列を分ける必要あり？
            // 特権で無い場合は、自分の履歴のみ表示出来る
            $items = $items
                ->where(function ($query) use ($addressbook) {
                    $query
                        ->where('sender', $addressbook->tel1)
                        ->orWhere('destination', $addressbook->tel1);
                });
        }

        // 期間
        if (is_array($request['datetime'])) {
            $startDt = strtotime(str_replace('"', '', $request['datetime'][0]));
            $endDt = strtotime(str_replace('"', '', $request['datetime'][1]));

            if ($startDt && $endDt) {
                $startDt = date('Y-m-d' . ' 00:00:00', $startDt);
                $endDt = date('Y-m-d' . ' 23:59:59', $endDt);

                $items = $items
                    ->whereBetween('start_datetime', array($startDt, $endDt));
            }
        }

        // Sort
        $sort = explode('|', $request['sort']);

        if (is_array($sort) && in_array($sort[0], $column) && in_array($sort[1], array('desc', 'asc'))) {
            $items = $items
                ->orderBy($sort[0], $sort[1]);
        }

        return $items;

    }

}
