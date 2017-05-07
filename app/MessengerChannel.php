<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MessengerChannel extends Model
{
    // 追加する属性
    protected $appends = array(
        'member_count',
    );

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * 参加者数
     */
    public function getMemberCountAttribute()
    {

        return $this->users()->count();

    }

}
