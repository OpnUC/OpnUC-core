<?php

namespace Tests;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class TestCase extends BaseTestCase
{
    use InteractsWithTenancy;

    protected $user;
    protected $tenantUrl;

    public function createApplication()
    {
        $app = require __DIR__ . '/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        $this->setUpTenancy();

        return $app;
    }

    /**
     * Set the currently logged in user for the application.
     *
     * @param Authenticatable $user
     * @param string|null $driver
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

        return parent::call($method, 'http://' . $this->tenantUrl . '/' . $uri, $parameters, $cookies, $files, $server, $content);
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->tenantUrl = 'testing.' . env('TENANT_URL_BASE');

        Artisan::call('migrate:reset');

        Artisan::call('migrate');

        $this->setUpHostnames(true);
        $this->setUpWebsites(true, true);
        $this->activateTenant();

        // テナントデータベースのシーダーはTenancyで自動的に実行される
//        Artisan::call('tenancy:db:seed');

        // デモデータのシーダー
        Artisan::call('tenancy:db:seed', [
            '--class' => 'DemoSeeder',
            '--website_id' => 1
        ]);
    }

    public function tearDown(): void
    {
        Artisan::call('migrate:reset');

        $this->cleanupTenancy();

        parent::tearDown();
    }
}
