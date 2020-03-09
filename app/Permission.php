<?php namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;

class Permission extends \Spatie\Permission\Models\Permission
{
    use UsesTenantConnection;

    protected $hidden = [
        'guard_name',
    ];
}