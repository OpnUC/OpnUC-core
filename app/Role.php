<?php namespace App;

use Illuminate\Support\Facades\Config;
use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{
    protected $fillable = [
        'name', 'display_name', 'description',
    ];

    // JSONに追加する属性
    protected $appends = array(
        'users_count',
        'perms',
    );

    /**
     * @return int
     */
    public function getUsersCountAttribute()
    {
        $item = $this->users()->get()->count();

        return $item;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPermsAttribute()
    {
        return $this->perms()->get(['id'])->pluck('id');
    }

    // fix
    public function users()
    {

        return $this->belongsToMany(Config::get('auth.providers.users.model'), Config::get('entrust.role_user_table'), Config::get('entrust.role_foreign_key'), Config::get('entrust.user_foreign_key'));

    }

}