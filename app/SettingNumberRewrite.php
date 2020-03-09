<?php

namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

/**
 * App\SettingNumberRewrite
 */
class SettingNumberRewrite extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
}