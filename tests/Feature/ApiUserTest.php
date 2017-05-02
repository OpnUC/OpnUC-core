<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ApiUserTest extends TestCase
{
    /**
     * Login Test
     */
    public function testLogin()
    {
        $user = factory(\App\User::class)->create();

        $this->actingAs($user);

        $this->post('/api/v1/auth/login', [
            'username' => 'user01',
            'password' => 'password01',
        ], [
            'Accept' => 'application/json'
        ])
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);
    }

    /**
     * Logout Test
     */
    public function testLogout()
    {
        $user = factory(\App\User::class)->create();

        $this->actingAs($user);

        $this->post('/api/v1/auth/logout')
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);
    }

    /**
     * Refresh Test
     */
    public function testRefresh()
    {
        $user = factory(\App\User::class)->create();

        $this->actingAs($user);

        $this->get('/api/v1/auth/refresh')
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);
    }

    /**
     * Get User Data Test
     */
    public function testUserData()
    {
        $user = factory(\App\User::class)->create();

        $this->actingAs($user);

        $this->get('/api/v1/auth/user')
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'username' => $user->username,
                    'display_name' => $user->display_name,
                    'email' => $user->email,
                ]
            ]);
    }


}
