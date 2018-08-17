<?php

namespace Tests\Feature;

use App\Facades\PbxLinker;
use Tests\TestCase;

/**
 * Pbx Linkerのテスト
 * Class ApiPbxLinkerTest
 * @package Tests\Feature
 */
class ApiPbxLinkerTest extends TestCase
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

        factory(\App\SettingNumberRewrite::class)->create();

        $this->actingAs($user);

        // 発信先の番号を指定
        $telNumber = '0120116116';

        // PbxLinkerの発信処理をモック化
        // 番号変換で0が付加されるため、決め打ちで0を追記
        PbxLinker::shouldReceive('originate')
            ->once()
            ->with($addressbook->tel1, '0' . $telNumber)
            ->andReturn(true);

        $this->post('/api/v1/pbxlinker/originate', [
            'number' => $telNumber,
        ])
            ->assertStatus(200);

    }

    /**
     * テスト：発信 失敗1
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

    /**
     * テスト：不在転送設定
     */
    public function testForward()
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

        // 転送先の番号を指定
        $telNumber = '117';

        // Pbx Linkerの不在転送機能をモックで有効にする
        PbxLinker::shouldReceive('isEnabledSetCallForward')
            ->once()
            ->andReturn(true);

        // PbxLinkerの不在転送設定処理をモック化
        PbxLinker::shouldReceive('setCallForward')
            ->once()
            ->with($addressbook->tel1, $telNumber)
            ->andReturn(true);

        $this->post('/api/v1/pbxlinker/forward', [
            'ExtNumber' => $addressbook->tel1,
            'Number' => $telNumber,
        ])
            ->assertStatus(200);

    }

    /**
     * テスト：不在転送設定 失敗1
     * ユーザに紐付いたアドレス帳がない
     */
    public function testForwardFail1()
    {
        $user = factory(\App\User::class)->create();

        $this->actingAs($user);

        // Pbx Linkerの不在転送機能をモックで有効にする
        PbxLinker::shouldReceive('isEnabledSetCallForward')
            ->once()
            ->andReturn(true);

        // ユーザに関連付いたアドレス帳がないため、失敗
        $this->post('/api/v1/pbxlinker/forward', [
            'ExtNumber' => '300',
            'Number' => '117',
        ])
            ->assertStatus(403);

    }

    /**
     * テスト：不在転送設定 失敗2
     * 転送先が電話番号でない
     */
    public function testFowardFail2()
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

        // 転送先の番号を指定
        $telNumber = 'abc';

        // Pbx Linkerの不在転送機能をモックで有効にする
        PbxLinker::shouldReceive('isEnabledSetCallForward')
            ->once()
            ->andReturn(true);

        // 転送先が数値ではないため、失敗
        $this->post('/api/v1/pbxlinker/forward', [
            'ExtNumber' => $addressbook->tel1,
            'Number' => $telNumber,
        ])
            ->assertStatus(422);

    }

    /**
     * テスト：不在転送設定 失敗3
     * 不在転送設定が無効
     */
    public function testForwardFail3()
    {
        $user = factory(\App\User::class)->create();

        $this->actingAs($user);

        // Pbx Linkerの不在転送機能をモックで有効にする
        PbxLinker::shouldReceive('isEnabledSetCallForward')
            ->once()
            ->andReturn(false);

        // 不在転送が無効なため、失敗
        $this->post('/api/v1/pbxlinker/forward', [
            'ExtNumber' => '300',
            'Number' => '117',
        ])
            ->assertStatus(403);

    }

}
