<?php

namespace App\Http\Controllers\Api\v1;

use App\Events\MessengerNewMessage;
use App\Message;
use App\MessengerChannel;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use Storage;

/**
 * Messenger Controller
 */
class MessengerController extends Controller
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
     * メッセージをポストする
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function message(Request $request)
    {

        // 権限チェック
        if (!\Entrust::can('messenger-user')) {
            abort(403);
        }

        // リクエストからチャンネルIDを取得
        $channelId = intval($request['channelId']);

        // メッセージをDBに保存
        $message = new Message();
        $message->from_user_id = \Auth::user()->id;
        $message->channel_id = $channelId;
        $message->message = $request['message'];
        $message->save();

        // WebSocketのイベント発火
        broadcast(new MessengerNewMessage($message))
            ->toOthers();

        return response([
            'status' => 'success'
        ]);

    }

    /**
     * チャンネルの作成
     * @param Requests\MessengerNewChannelRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function newChannel(Requests\MessengerNewChannelRequest $request)
    {

        // 権限チェック
        if (!\Entrust::can('messenger-user')) {
            abort(403);
        }

        // チャンネルを作成
        $channel = new MessengerChannel();
        $channel->name = $request['name'];
        $channel->topic = $request['topic'];
        $channel->save();

        return response([
            'status' => 'success'
        ]);

    }

    /**
     * チャンネルに参加する
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function joinChannel(Request $request)
    {

        // 権限チェック
        if (!\Entrust::can('messenger-user')) {
            abort(403);
        }

        // リクエストからチャンネルIDを取得
        $channelId = intval($request['channelId']);

        // DB上でチャンネルに参加する
        $result = User::find(\Auth::user()->id)
            ->messengerChannels()
            ->sync([$channelId], false);

        // メッセージをDBに保存
        $message = new Message();
        $message->from_user_id = \Auth::user()->id;
        $message->channel_id = $channelId;
        $message->message = 'チャンネルに参加しました。(システム)';
        $message->save();

        // WebSocketのイベント発火
        broadcast(new MessengerNewMessage($message))
            ->toOthers();

        return response([
            'status' => 'success',
            'channels' => $result
        ]);

    }

    /**
     * チャンネルから退室する
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function leaveChannel(Request $request)
    {

        // 権限チェック
        if (!\Entrust::can('messenger-user')) {
            abort(403);
        }

        // リクエストからチャンネルIDを取得
        $channelId = intval($request['channelId']);

        $result = User::find(\Auth::user()->id)
            ->messengerChannels()
            ->detach($channelId);

        // メッセージをDBに保存
        $message = new Message();
        $message->from_user_id = \Auth::user()->id;
        $message->channel_id = $channelId;
        $message->message = 'チャンネルから退室しました。(システム)';
        $message->save();

        // WebSocketのイベント発火
        broadcast(new MessengerNewMessage($message))
            ->toOthers();

        return response([
            'status' => 'success',
            'channels' => $result
        ]);

    }

    /**
     * 参加しているチャンネルリストを取得する
     * @return mixed
     */
    public function joinedChannles()
    {

        // 権限チェック
        if (!\Entrust::can('messenger-user')) {
            abort(403);
        }

        $channels = User::find(\Auth::user()->id)
            ->messengerChannels;

        return \Response::json($channels);

    }

    /**
     * チャンネル情報取得
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function channel(Request $request)
    {

        // 権限チェック
        if (!\Entrust::can('messenger-user')) {
            abort(403);
        }

        // リクエストからチャンネルIDを取得
        $channelId = intval($request['channelId']);

        $channel = MessengerChannel::find($channelId);

        return \Response::json($channel);

    }

    /**
     * チャンネル一覧を取得する
     * @return \Illuminate\Http\JsonResponse
     */
    public function channels(Request $request)
    {

        // 権限チェック
        if (!\Entrust::can('messenger-user')) {
            abort(403);
        }

        $column = ['id', 'name', 'topic'];

        $items = \App\MessengerChannel::select($column);

        // キーワードで絞り込み
        if (strlen($request['keyword']) != 0) {
            $items = $items
                ->where(function ($query) use ($request) {
                    $query
                        ->orWhere('name', 'like', '%' . $request['keyword'] . '%')
                        ->orWhere('topic', 'like', '%' . $request['keyword'] . '%');
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
     * ファイルのアップロード
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function upload(Request $request)
    {

        // 権限チェック
        if (!\Entrust::can('messenger-user')) {
            abort(403);
        }

        if ($request->file('file')->isValid([])) {
            // 一時ファイル名を生成
            $uploadFileName = hash('sha256', time() . $request->file->getClientOriginalName());
            $request->file->storeAs('messengerAttaches', $uploadFileName);

            // オリジナルファイル名を取得
            $origFileName = $request->file->getClientOriginalName();

            $channelId = intval($request['channelId']);

            // DBに保存
            $message = new Message();
            $message->from_user_id = \Auth::user()->id;
            $message->channel_id = $channelId;
            $message->message = 'ファイルがアップロードされました。';
            $message->attach_file = array(
                $uploadFileName => $origFileName
            );
            $message->save();

            // WebSocketのイベント発火
            broadcast(new MessengerNewMessage($message));

            return response([
                'status' => 'success'
            ]);
        } else {
            return response([
                'status' => 'error',
                'message' => 'アップロードに失敗しました。'
            ], 400);
        }

    }

    /**
     * ダウンロードさせる
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function download(Request $request)
    {
        // binaryとして扱うので、Responseのメッセージは使えない

        // 権限チェック
        if (!\Entrust::can('messenger-user')) {
            abort(403);
        }

        $id = intval($request['id']);

        // メッセージを取得
        $item = Message::find($id);

        // メッセージが存在しない場合は、404で返す
        if (!$item) {
            abort(404);
        }

        // 添付ファイルが存在しない場合は、400で返す
        if (!$item->attach_file) {
            abort(400);
        }

        $attachFile = (array)$item->attach_file;

        $storageFile = array_keys($attachFile)[0];
        $storagePath = 'messengerAttaches/' . $storageFile;
        $filename = $attachFile[$storageFile];

        // ファイルが存在しない場合は、404で返す
        if (!Storage::exists($storagePath)) {
            abort(404);
        }

        $headers = array(
            'Content-Type' => Storage::mimeType($storagePath),
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length' => filesize(storage_path('app/' . $storagePath))
        );

        return response()->download(storage_path('app/' . $storagePath), $filename, $headers);

    }

}
