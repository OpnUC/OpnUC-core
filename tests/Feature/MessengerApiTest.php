<?php

namespace Tests\Feature;

use App\Message;
use App\Role;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class MessengerApiTest extends TestCase
{

    /**
     * New Channel Test
     */
    public function testNewChannel()
    {
        $user = factory(\App\User::class)->create();

        $adminRole = Role::where('name', 'admin')->first();
        $user->attachRole($adminRole);

        $this->actingAs($user);

        $this->post('/api/v1/messenger/newChannel', [
            'name' => 'channel99',
            'topic' => 'channel99_topic',
        ], [
            'Accept' => 'application/json'
        ])
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);
    }

    /**
     * Join Channel Test
     */
    public function testJoinChannel()
    {
        $user = factory(\App\User::class)->create();

        $adminRole = Role::where('name', 'admin')->first();
        $user->attachRole($adminRole);

        $this->actingAs($user);

        $channel = factory(\App\MessengerChannel::class)->create();

        $this->call('POST', '/api/v1/messenger/joinChannel', [
            'channelId' => $channel->id
        ], [
            'Accept' => 'application/json'
        ])
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);
    }

    /**
     * Leave Channel Test
     */
    public function testLeaveChannel()
    {
        $user = factory(\App\User::class)->create();

        $adminRole = Role::where('name', 'admin')->first();
        $user->attachRole($adminRole);

        $this->actingAs($user);

        $channel = factory(\App\MessengerChannel::class)->create();

        $this->call('POST', '/api/v1/messenger/leaveChannel', [
            'channelId' => $channel->id
        ], [
            'Accept' => 'application/json'
        ])
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);
    }

    /**
     * Messege Post Test
     */
    public function testMesseage()
    {
        $user = factory(\App\User::class)->create();

        $adminRole = Role::where('name', 'admin')->first();
        $user->attachRole($adminRole);

        $this->actingAs($user);

        $channel = factory(\App\MessengerChannel::class)->create();

        $this->post('/api/v1/messenger/message', [
            'channelId' => $channel->id,
            'message' => 'テストメッセージ',
        ], [
            'Accept' => 'application/json'
        ])
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);
    }

    /**
     * Channel Test
     */
    public function testChannel()
    {
        $user = factory(\App\User::class)->create();

        $adminRole = Role::where('name', 'admin')->first();
        $user->attachRole($adminRole);

        $this->actingAs($user);

        $channel = factory(\App\MessengerChannel::class)->create();

        $this->call('GET', '/api/v1/messenger/channel', [
            'channelId' => $channel->id,
        ], [
            'Accept' => 'application/json'
        ])
            ->assertStatus(200)
            ->assertJson([
                'id' => $channel->id,
                'name' => $channel->name,
                'topic' => $channel->topic,
            ]);
    }

    /**
     * Channels Test
     */
    public function testChannels()
    {
        $user = factory(\App\User::class)->create();

        $adminRole = Role::where('name', 'admin')->first();
        $user->attachRole($adminRole);

        $this->actingAs($user);

        $channel = factory(\App\MessengerChannel::class)->create();

        $this->call('GET', '/api/v1/messenger/channels', [], [
            'Accept' => 'application/json'
        ])
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'id' => $channel->id,
                        'name' => $channel->name,
                        'topic' => $channel->topic,
                    ]
                ]
            ]);
    }

    /**
     * Upload Test
     */
//    public function testUpload()
//    {
//
//        $user = factory(\App\User::class)->create();
//
//        $adminRole = Role::where('name', 'admin')->first();
//        $user->attachRole($adminRole);
//
//        $this->actingAs($user);
//
//        $channel = factory(\App\MessengerChannel::class)->create();
//
//        Storage::fake();
//
//        $this->call('POST', '/api/v1/messenger/upload', [
//            'channelId' => $channel->id
//        ],
//            [
//            ],
//            [
//                'file' => UploadedFile::fake()->create('attache.pdf', 512)
//            ])
//            ->assertStatus(200)
//            ->assertJson([
//                'status' => 'success',
//            ]);
//
//        $message = Message::where('channel_id', $channel->id)
//            ->orderBy('id', 'desc')
//            ->first();
//
//        $file = key($message->attach_file);
//
//        // ToDo: ファイル名はハッシュ化しているため、見えない
//        Storage::disk()->assertExists($file);
//    }

}
