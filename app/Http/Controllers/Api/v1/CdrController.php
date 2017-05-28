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
     * constructor
     */
    public function __construct()
    {

        // ミドルウェアの指定
        $this->middleware('jwt.auth');

    }

    /**
     * 発着信履歴をJSONで返す
     * @param Request $req
     * @return type
     */
    public function search(Request $request)
    {

        // 権限チェック
        if (!\Entrust::can('cdr-user')) {
            abort(403);
        }

        $per_page = intval($request['per_page']) ? $request['per_page'] : 10;

        $items = $this->_getItems($request)->paginate($per_page);

        return \Response::json($items);
    }

    /**
     * 発着信履歴をCSVでダウンロードさせる
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function download(Request $request)
    {

        // 権限チェック
        if (!\Entrust::can('cdr-user')) {
            abort(403);
        }

        $items = $this->_getItems($request)->get()->toArray();

        $csvHeader = ['id', 'start_datetime', 'duration', 'sender', 'destination'];
        array_unshift($items, $csvHeader);

        $stream = fopen('php://temp', 'r+b');

        foreach ($items as $user) {
            fputcsv($stream, $user);
        }

        rewind($stream);

        $csv = str_replace(PHP_EOL, "\r\n", stream_get_contents($stream));

        $headers = array(
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="cdr.csv"',
        );

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

        $column = ['id', 'start_datetime', 'duration', 'sender', 'destination'];

        $items = \App\Cdr::select($column);

        if (strlen($request['sender'])) {
            $items = $items
                ->where('sender', 'LIKE', '%' . $request['sender'] . '%');
        }

        if (strlen($request['destination'])) {
            $items = $items
                ->where('destination', 'LIKE', '%' . $request['destination'] . '%');
        }

        $startDt = str_replace('"', '', $request['datetime'][0]);
        $endDt = str_replace('"', '', $request['datetime'][1]);

        if (is_array($request['datetime']) && strtotime($startDt) && strtotime($endDt)) {
            $startDt = date('Y-m-d' . ' 00:00:00', strtotime($startDt));
            $endDt = date('Y-m-d' . ' 23:59:59', strtotime($endDt));

            $items = $items
                ->whereBetween('start_datetime', array($startDt, $endDt));
        }

        $sort = explode('|', $request['sort']);

        // Sort
        if (is_array($sort) && in_array($sort[0], $column) && in_array($sort[1], array('desc', 'asc'))) {
            $items = $items
                ->orderBy($sort[0], $sort[1]);
        }

        return $items;

    }

}
