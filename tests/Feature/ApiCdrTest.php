<?php

namespace Tests\Feature;

use App\Role;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * 発着信履歴のテスト
 * @package Tests\Feature
 */
class ApiCdrTest extends TestCase
{
    /**
     * テスト：検索
     */
    public function testSearch()
    {
        $user = factory(\App\User::class)->create();

        $adminRole = Role::where('name', 'admin')->first();
        $user->attachRole($adminRole);

        $this->actingAs($user);

        $this->get('/api/v1/cdr/search', [
            'sort' => 'start_datetime|desc',
            'sender' => '3',
            'destination' => '3',
            'datetime' => [
                '2000-01-01T00:00:00.000Z',
                '2999-12-31T23:59:59.999Z',
            ]
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

        $this->get('/api/v1/cdr/search')
            ->assertStatus(403);

    }

    /**
     * テスト：ダウンロード
     */
    public function testDownload()
    {
        $user = factory(\App\User::class)->create();

        $adminRole = Role::where('name', 'admin')->first();
        $user->attachRole($adminRole);

        $this->actingAs($user);

        $this->get('/api/v1/cdr/download')
            ->assertStatus(200);

    }
}
