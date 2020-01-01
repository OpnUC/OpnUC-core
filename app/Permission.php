<?php namespace App;

class Permission extends \Spatie\Permission\Models\Permission
{
    protected $hidden = [
        'guard_name',
    ];
}