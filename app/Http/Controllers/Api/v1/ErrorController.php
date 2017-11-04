<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Requests;

/**
 * Error Controller
 */
class ErrorController extends Controller
{

    /**
     * エラーリポート
     */
    public function report(Request $request)
    {

        $record = new \App\Errorlog();
        $record->user_id = \Auth::user() ? \Auth::user()->id : 0;
        $record->type = 'frontend';
        $record->message = json_encode($request['message']);
        $record->save();

        return response([
            'status' => 'success'
        ]);

    }

}
