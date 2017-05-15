<?php

namespace App\Http\Controllers\Api\v1;

use App\Message;
use App\MessengerChannel;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;

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

        $channelId = intval($request['channelId']);

        $message = new Message();
        $message->from_user_id = \Auth::user()->id;
        $message->channel_id = $channelId;
        $message->message = $request['message'];

        $message->save();

        broadcast(new \App\Events\MessengerNewMessage(
                $channelId,
                \Auth::user()->id,
                \Auth::user()->display_name,
                \Auth::user()->getAvatarPathAttribute(),
                date('c'),
                $request['message']
            )
        )->toOthers();

        return response([
            'status' => 'success'
        ]);

    }

    /**
     * チャンネルの追加
     * @param Requests\MessengerNewChannelRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function newChannel(Requests\MessengerNewChannelRequest $request)
    {

        $channel = new MessengerChannel();
        $channel->name = $request['name'];
        $channel->topic = $request['topic'];
        $channel->save();

        return response([
            'status' => 'success'
        ]);

    }

    public function joinChannel(Request $request)
    {

        $channelId = intval($request['channelId']);

        $result = User::find(\Auth::user()->id)
            ->messengerChannels()
            ->sync([$channelId], false);

        broadcast(new \App\Events\MessengerNewMessage(
                $channelId,
                \Auth::user()->id,
                \Auth::user()->display_name,
                \Auth::user()->getAvatarPathAttribute(),
                date('c'),
                'チャンネルに参加しました。(システム)'
            )
        )->toOthers();

        return response([
            'status' => 'success',
            'channels' => $result
        ]);

    }

    public function leaveChannel(Request $request)
    {

        $channelId = intval($request['channelId']);

        $result = User::find(\Auth::user()->id)
            ->messengerChannels()
            ->detach($channelId);

        broadcast(new \App\Events\MessengerNewMessage(
                $channelId,
                \Auth::user()->id,
                \Auth::user()->display_name,
                \Auth::user()->getAvatarPathAttribute(),
                date('c'),
                'チャンネルから退室しました。(システム)'
            )
        )->toOthers();

        return response([
            'status' => 'success',
            'channels' => $result
        ]);

    }

    public function joinedChannles()
    {

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

        $channelId = intval($request['channelId']);

        $channel = MessengerChannel::find($channelId);

        return \Response::json($channel);

    }

    /**
     * チャンネル一覧取得
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

}
