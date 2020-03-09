<?php

namespace App\Console\Commands;

use App\Libs\Tenant;
use Hyn\Tenancy\Contracts\Repositories\HostnameRepository;
use Hyn\Tenancy\Contracts\Repositories\WebsiteRepository;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Models\Website;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

/**
 * テナントを削除するコマンド
 * @package App\Console\Commands
 * @see https://qiita.com/hitotch/items/8162d5a0594c72db6fed from Qiita @hitotch san.
 */
class DeleteTenant extends Command
{
    protected $signature = 'tenant:delete {tenantName}';

    protected $description = 'Deletes a tenant of the provided name. Only available on the local environment e.g. php artisan tenant:delete boise';

    public function handle()
    {

        // because this is a destructive command, we'll only allow to run this command
        // if you are on the local or testing
        if (!(app()->isLocal() || app()->runningUnitTests())) {
            $this->error('This command is only avilable on the local environment.');
            return;
        }

        $tenantName = $this->argument('tenantName');

        if ($tenant = Tenant::retrieveBy($tenantName)) {
            $tenant->delete();
            $this->info("Tenant {$tenantName} successfully deleted.");
        } else {
            $this->error("Couldn't find tenant {$tenantName}");
        }

    }

}