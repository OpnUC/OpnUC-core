<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

/**
 * 発着信履歴
 */
class CdrController extends Controller {

    /**
     * constructor
     */
    public function __construct(){

        // ミドルウェアの指定
        $this->middleware('jwt.auth');

    }

    /**
     * 発着信履歴をJSONで返す
     * @param Request $req
     * @return type
     */
    public function search(Request $req) {

        $column = ['id', 'start_datetime', 'duration', 'type', 'sender', 'destination'];

        $items = \App\Cdr::select($column);

        if (strlen($req['sender'])) {
            $items = $items
                    ->where('sender', 'LIKE', '%' . $req['sender'] . '%');
        }

        if (strlen($req['destination'])) {
            $items = $items
                    ->where('destination', 'LIKE', '%' . $req['destination'] . '%');
        }

        if (strlen($req['start_datetime']) && date_parse($req['start_datetime']) && strlen($req['end_datetime']) && date_parse($req['end_datetime'])) {
            $items = $items
                    ->whereBetween('start_datetime', array($req['start_datetime'], $req['end_datetime']));
        }

        $type = is_numeric($req['type']) ? intval($req['type']) : 0;

        if ($type !== 0) {
            $items = $items
                    ->where('type', $req['type']);
        }

        $sort = explode('|', $req['sort']);
        // Sort
        if (is_array($sort) && in_array($sort[0], $column) && in_array($sort[1], array('desc', 'asc'))) {
                $items = $items
                        ->orderBy($sort[0], $sort[1]);
        }

        $per_page = intval($req['per_page']) ? $req['per_page'] : 10;

        $items = $items->paginate($per_page);

        return \Response::json($items);
    }

}
