<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

//Broadcast::channel('App.User.{id}', function ($user, $id) {
//    return (int)$user->id === (int)$id;
//});

Broadcast::channel('PrivateChannel.{userId}', function ($user, $userId) {
//    \Log::debug(print_r($user, true));
    return (int)$user->id === (int)$userId;
});

Broadcast::channel('MessengerChannel.{channelId}', function ($user, $channelId) {
    if (true) { // Replace with real ACL
        return [
            'id' => $user->id,
            'name' => $user->display_name,
            'avatar_path' => $user->avatar_path,
        ];
    }
});