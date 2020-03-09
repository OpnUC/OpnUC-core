<?php

namespace App\Console\Commands;

use App\Libs\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * テナントを作成するコマンド
 * @package App\Console\Commands
 * @see https://qiita.com/hitotch/items/8162d5a0594c72db6fed from Qiita @hitotch san.
 */
class CreateTenant extends Command
{

    protected $signature = 'tenant:create {name} {email} {tenantName}';
    protected $description = 'Creates a tenant with the provided name and email address e.g. php artisan tenant:create boise boise@example.com';

    /**
     * 実際の処理
     */
    public function handle()
    {

        $name = $this->argument('name');
        $email = $this->argument('email');
        $tenantName = $this->argument('tenantName');

        // テナントの存在を確認
        if (Tenant::retrieveBy($tenantName)) {
            $this->error("A tenant with name '{$tenantName}' already exists.");
            return;
        }

        $password = Str::random();
        $tenant = Tenant::createFrom($name, $email, $password, $tenantName);

        $this->info("Tenant '{$tenantName}' is created and is now accessible at {$tenant->hostname->fqdn}");
        $this->info("Password is {$password}");

        // invite admin
        //$tenant->admin->notify(new TenantCreated($tenant->hostname));
        $this->info("Admin {$email} has been invited!");

    }

}
