<?php

namespace Tests;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $user;

    /**
     * Set the currently logged in user for the application.
     *
     * @param  Authenticatable $user
     * @param  string|null $driver
     * @return $this
     */
    public function actingAs(Authenticatable $user, $driver = null)
    {
        $this->user = $user;
        return $this;
    }

    public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
    {
        if ($this->user) {
            $server['HTTP_AUTHORIZATION'] = 'Bearer ' . auth('api')->login($this->user);
        }

        $server['HTTP_ACCEPT'] = 'application/json';

        return parent::call($method, $uri, $parameters, $cookies, $files, $server, $content);
    }


    public function setUp()
    {
        parent::setUp();
        Artisan::call('migrate:reset');

        Artisan::call('migrate');
        Artisan::call('db:seed');
        Artisan::call('db:seed', ['--class' => 'DemoSeeder']);
    }

    public function tearDown()
    {
        Artisan::call('migrate:reset');
        parent::tearDown();
    }
}
