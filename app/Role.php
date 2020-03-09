<?php namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;

class Role extends \Spatie\Permission\Models\Role
{
    use UsesTenantConnection;

    protected $fillable = [
        'name', 'display_name', 'description',
    ];

    protected $hidden = [
        'guard_name',
        'permissions',
        'users',
    ];

    // JSONに追加する属性
    protected $appends = array(
        'users_count',
        'perms_name',
    );

    /**
     * Roleを割り当てられているユーザ数
     * @return int
     */
    public function getUsersCountAttribute()
    {
        // Todo: $this->usersを使うと getModelForGuard エラーになるため
        $item = User::role($this->name)->count();

        return $item;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPermsNameAttribute()
    {
        return $this->getPermissionNames();
    }

}