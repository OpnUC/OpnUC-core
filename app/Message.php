<?php

namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use UsesTenantConnection;

    public $fillable = ['from_user_id', 'to_user_id', 'message'];
}
