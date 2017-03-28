<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;

use App\Notifications\CustomPasswordReset;

class User extends Authenticatable
{
    use Notifiable;
    use EntrustUserTrait;

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
        'password', 'remember_token',
    ];

    // JSONに追加する属性
    protected $appends = array(
        'address_book',
    );

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

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomPasswordReset($token));
    }

}
