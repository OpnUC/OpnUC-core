<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\User;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\SomeEvent' => [
            'App\Listeners\EventListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        // SAMLで認証できた場合
        Event::listen('Aacotroneo\Saml2\Events\Saml2LoginEvent', function ($event) {
            // SAMLのユーザ情報を取得
            $user = $event->getSaml2User();

            // ユーザテーブルからユーザを検索
            $laravelUser = User::where('email', $user->getUserId())
                ->first();

            // ログインしたことにする
            Auth::login($laravelUser);
        });

        // SAMLでログアウトした場合
        Event::listen('Aacotroneo\Saml2\Events\Saml2LogoutEvent', function ($event) {
            //Auth::logout();
            //Session::save();
        });

    }
}
