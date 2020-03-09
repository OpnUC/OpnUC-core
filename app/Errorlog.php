<?php

namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Errorlog
 */
class Errorlog extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
}