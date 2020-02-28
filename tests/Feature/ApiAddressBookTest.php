<?php

namespace Tests\Feature;

use App\Role;
use Tests\TestCase;

/**
 * 電話帳のテスト
 * @package Tests\Feature
 */
class ApiAddressBookTest extends TestCase
{
    /**
     * テスト：検索
     */
    public function testSearch()
    {
        $user = factory(\App\User::class)->create();

        $adminRole = Role::where('name', 'admin')->first();
        $user->assignRole($adminRole);

        $this->actingAs($user);

        $this->call('GET', '/api/v1/addressbook/search', [
            'sort' => 'name_kana|asc',
            'typeId' => 2,
            'groupId' => 0,
        ])
            ->assertStatus(200);

    }

    /**
     * テスト：検索 失敗
     */
    public function testSearchFail()
    {
        $user = factory(\App\User::class)->create();

        $this->actingAs($user);

        $this->call('GET', '/api/v1/addressbook/search')
            ->assertStatus(403);

    }

    /**
     * テスト：ダウンロード(標準)
     */
    public function testDownloadStandard()
    {
        $user = factory(\App\User::class)->create();

        $adminRole = Role::where('name', 'admin')->first();
        $user->assignRole($adminRole);

        $this->actingAs($user);

        $this->call('GET', '/api/v1/addressbook/download', [
            'downloadType' => 'standard',
            'typeId' => 2,
            'groupId' => 0,
        ])
            ->assertStatus(200);

    }

    /**
     * テスト：ダウンロード(日立PHS)
     */
    public function testDownloadHitachiPHS()
    {
        $user = factory(\App\User::class)->create();

        $adminRole = Role::where('name', 'admin')->first();
        $user->assignRole($adminRole);

        $this->actingAs($user);

        $this->call('GET', '/api/v1/addressbook/download', [
            'downloadType' => 'standard',
            'typeId' => 2,
            'groupId' => 0,
        ])
            ->assertStatus(200);

    }
}
