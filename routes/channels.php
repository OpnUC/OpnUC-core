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

/**
 * PBX Linker Channel
 * @todo テナント
 */
Broadcast::channel('PbxLinkerChannel', function ($user) {
    // Check PBX Linker Permission
    return $user->can('pbxlinker');
});