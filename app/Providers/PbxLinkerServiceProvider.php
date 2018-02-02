<?php

namespace App\Providers;

use App\Facades\PbxLinker;
use Illuminate\Support\ServiceProvider;
use App\Libs\PbxLinkerManager;

class PbxLinkerServiceProvider extends ServiceProvider
{

    // 利用するまでロードしない
    protected $defer = true;

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

        // PBX Linkerをシングルトンで提供する
        $this->app->singleton(PbxLinker::class, function ($app) {
            return new PbxLinkerManager($app);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [PbxLinker::class];
    }

}
