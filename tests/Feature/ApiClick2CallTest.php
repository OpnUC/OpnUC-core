<?php

namespace Tests\Feature;

use App\Facades\PbxLinker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Click 2 Callのテスト
 * Class ApiClick2CallTest
 * @package Tests\Feature
 */
class ApiClick2CallTest extends TestCase
{
    /**
     * テスト：発信
     */
    public function testOriginate()
    {
        // ユーザーを作成
        $user = factory(\App\User::class)->create();

        // アドレス帳グループを作成
        $addressbookGroup = factory(\App\AddressBookGroup::class)->create();

        // アドレス帳を作成
        $addressbook = factory(\App\AddressBook::class)->create([
            'groupid' => $addressbookGroup->id,
            'owner_userid' => $user->id,
        ]);

        $this->actingAs($user);

        // 発信先の番号を指定
        $telNumber = '117';

        // PbxLinkerの発信処理をモック化
        PbxLinker::shouldReceive('originate')
            ->once()
            ->with($addressbook->tel1, $telNumber)
            ->andReturn(true);

        $this->post('/api/v1/pbxlinker/originate', [
            'number' => $telNumber,
        ])
            ->assertStatus(200);

    }

    /**
     * テスト：検索 失敗1
     */
    public function testOriginateFail1()
    {
        $user = factory(\App\User::class)->create();

        $this->actingAs($user);

        // ユーザに関連付いたアドレス帳がないため、失敗
        $this->post('/api/v1/pbxlinker/originate', [
            'number' => '117',
        ])
            ->assertStatus(403);

    }

    /**
     * テスト：発信 失敗2
     */
    public function testOriginateFail2()
    {
        // ユーザーを作成
        $user = factory(\App\User::class)->create();

        // アドレス帳グループを作成
        $addressbookGroup = factory(\App\AddressBookGroup::class)->create();

        // アドレス帳を作成
        factory(\App\AddressBook::class)->create([
            'groupid' => $addressbookGroup->id,
            'owner_userid' => $user->id,
        ]);

        $this->actingAs($user);

        // 発信先が数値ではないため、失敗
        $this->post('/api/v1/pbxlinker/originate', [
            'number' => 'abc',
        ])
            ->assertStatus(422);

    }

}
