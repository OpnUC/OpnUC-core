<?php

namespace App\Libs;

use App\User;
use Hyn\Tenancy\Environment;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Models\Website;
use Illuminate\Support\Facades\Hash;
use Hyn\Tenancy\Contracts\Repositories\HostnameRepository;
use Hyn\Tenancy\Contracts\Repositories\WebsiteRepository;

/**
 * テナント
 * @property Website website
 * @property Hostname hostname
 * @see https://qiita.com/hitotch/items/8162d5a0594c72db6fed from Qiita @hitotch san.
 */
class Tenant
{
    /**
     * Tenant constructor.
     * @param $tenantName string テナント名
     */
    public function __construct($tenantName)
    {

        $baseUrl = config('app.url_base');

        $fqdn = "{$tenantName}.{$baseUrl}";

        if ($this->hostname = Hostname::where('fqdn', $fqdn)->firstOrFail()) {
            $this->website = Website::where('id', $this->hostname->website_id)->firstOrFail();
        }

    }

    /**
     * テナントの削除
     */
    public function delete()
    {
        app(HostnameRepository::class)->delete($this->hostname, true);
        app(WebsiteRepository::class)->delete($this->website, true);
    }

    /**
     * テナントの作成
     * @param $name string 管理者名
     * @param $email string 管理者メールアドレス
     * @param $password string 管理者パスワード
     * @param $tenantName string テナント名
     * @return Tenant
     */
    public static function createFrom($name, $email, $password, $tenantName): Tenant
    {

        // create a website
        $website = new Website;
        app(WebsiteRepository::class)->create($website);

        // associate the website with a hostname
        $hostname = new Hostname;
        $baseUrl = config('app.url_base');
        $hostname->fqdn = "{$tenantName}.{$baseUrl}";

        app(HostnameRepository::class)->attach($hostname, $website);

        // make hostname current
        app(Environment::class)->tenant($website);

        // 管理者の作成
        $admin = static::makeAdmin($name, $email, $password);

        // 新しいテナントを返す
        return new Tenant($tenantName, $admin);

    }

    /**
     * 管理者の作成
     * @param $username string ユーザ名
     * @param $email string メールアドレス
     * @param $password string パスワード
     * @return User
     */
    private static function makeAdmin($username, $email, $password): User
    {

        $admin = User::create([
            'username' => $username,
            'display_name' => $username,
            'email' => $email,
            'password' => Hash::make($password)
        ]);

        $admin->guard_name = config('auth.defaults.guard', 'web');

        // 管理者に管理者ロールを割り当てる
        $admin->assignRole('admin');

        return $admin;

    }

    /**
     * テナントを取得
     * @param $tenantName string テナント名
     * @return Tenant|null
     */
    public static function retrieveBy($tenantName): ?Tenant
    {
        $baseUrl = config('app.url_base');

        $fqdn = "{$tenantName}.{$baseUrl}";

        if (Hostname::where('fqdn', $fqdn)->exists()) {
            return new Tenant($tenantName);
        }

        return null;
    }
}