<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiAuthTest extends TestCase
{
    /**
     * Login Test
     */
    public function testLogin()
    {
        $user = factory(\App\User::class)->create();

        $this->call('POST', '/api/v1/auth/login', [
            'username' => $user->username,
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
     * Login Test on Fail
     */
    public function testLoginFail()
    {
        $this->call('POST', '/api/v1/auth/login', [
            'username' => 'user01_',
            'password' => 'password01',
        ], [
            'Accept' => 'application/json'
        ])
            ->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'error' => 'invalid.credentials',
            ]);
    }

    /**
     * Login Restore Test
     */
    public function testLoginRestore()
    {
        $user = factory(\App\User::class)->create();

        $this->call('POST', '/api/v1/auth/login', [
            'mode' => 'restore',
            'token' => auth('api')->login($user),
        ], [
            'Accept' => 'application/json'
        ])
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);
    }

    /**
     * Login Restore Test on Fail
     */
    public function testLoginRestoreFail()
    {
        $user = factory(\App\User::class)->create();

        $this->call('POST', '/api/v1/auth/login', [
            'mode' => 'restore',
            'token' => '',
        ], [
            'Accept' => 'application/json'
        ])
            ->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'error' => 'invalid.credentials',
            ]);
    }

    /**
     * Logout Test
     */
    public function testLogout()
    {
        $user = factory(\App\User::class)->create();

        $this->actingAs($user);

        $this->call('POST', '/api/v1/auth/logout')
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);
    }

    /**
     * Logout Test on Fail
     */
    public function testLogoutFail()
    {
        $this->call('POST', '/api/v1/auth/logout')
            ->assertStatus(401);
    }

    /**
     * Refresh Test
     */
    public function testRefresh()
    {
        $user = factory(\App\User::class)->create();

        $this->actingAs($user);

        $this->call('GET', '/api/v1/auth/refresh')
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);
    }

    /**
     * Refresh Test on Fail
     */
    public function testRefreshFail()
    {
        $this->call('GET', '/api/v1/auth/refresh')
            ->assertStatus(401);
    }

    /**
     * Get User Data Test
     */
    public function testUserData()
    {
        $user = factory(\App\User::class)->create();

        $this->actingAs($user);

        $this->call('GET', '/api/v1/auth/user')
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

    /**
     * Get User Data Test on Fail
     */
    public function testUserDataFail()
    {
        $this->call('GET', '/api/v1/auth/user')
            ->assertStatus(401);
    }

}
