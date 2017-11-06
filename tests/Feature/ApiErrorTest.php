<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * API ErrorContoller Test
 * Class ApiErrorTest
 * @package Tests\Feature
 */
class ApiErrorTest extends TestCase
{
    /**
     * テスト：Report(ログイン中)
     */
    public function testReport()
    {
        // ユーザーを作成
        $user = factory(\App\User::class)->create();

        $this->actingAs($user);
        $this->post('/api/v1/error/report', [
            'message' => 'error message logged in',
        ])
            ->assertStatus(200);

    }

    /**
     * テスト：Report(非ログイン中)
     */
    public function testReport2()
    {

        $this->post('/api/v1/error/report', [
            'message' => 'error message not logged in',
        ])
            ->assertStatus(200);

    }

}
