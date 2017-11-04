<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Libs\PbxLinkerManager;

class PbxLinkerServiceProvider extends ServiceProvider
{

    // 利用するまでロードしない
    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

        // PBX Linkerをシングルトンで提供する
        $this->app->singleton('pbxlinker', function ($app) {
            return new PbxLinkerManager($app);
        });
    }
}
