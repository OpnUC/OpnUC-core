<?php

namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Notifications\CustomPasswordReset;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use HasRoles;
    use UsesTenantConnection;

    protected $guard_name = 'api';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'display_name', 'username', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'roles',
        'permissions',
    ];

    // JSONに追加する属性
    protected $appends = array(
        'address_book',
        'roles_name',
        'permissions_name',
        'avatar_path',
    );

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * アドレス帳情報を取得
     * @return AddressBook
     */
    public function getAddressBookAttribute()
    {
        // 内線電話帳で所有者が自分のIDは自分のアイテムとして処理する
        $item = \App\AddressBook::where('type', 1)
            ->where('owner_userid', $this->id)
            ->get()
            ->first();

        return $item;
    }

    /**
     * ロール情報を取得
     * @return array
     */
    public function getRolesNameAttribute()
    {

        return $this->getRoleNames();

    }

    /**
     * 権限情報を取得
     * @return array
     * @throws \Exception
     */
    public function getPermissionsNameAttribute()
    {
        $result = array();

        foreach ($this->getAllPermissions() as $permission) {
            $result[] = $permission->name;
        }

        return $result;

    }

    /**
     * アバター
     * @return string
     */
    public function getAvatarPathAttribute()
    {

        // 初期値
        $path = 'https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mm';

        switch ($this->avatar_type) {
            case 1:
                // 標準(アップロード優先)
                if ($this->avatar_filename != '' && \Storage::exists('public/avatars/' . $this->avatar_filename)) {
                    // ファイルが存在している事をチェック
                    $path = \Storage::url('public/avatars/' . $this->avatar_filename);
                }
                break;
            case 2:
                // Gravatar
                $path = 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($this->email))) . '?d=' . urlencode('mm') . '&s=80';
                break;
        }

        return $path;

    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomPasswordReset($token));
    }

}
