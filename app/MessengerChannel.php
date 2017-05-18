<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MessengerChannel extends Model
{
    // 追加する属性
    protected $appends = array(
        'member_count',
        'recently_post',
    );

    /**
     * チャンネルに参加しているユーザ
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * このチャンネルのメッセージ
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'channel_id');
    }

    /**
     * 参加者数
     * @return integer
     */
    public function getMemberCountAttribute()
    {
        return $this->users()->count();
    }

    /**
     * 直近の投稿データ
     * @return mixed
     */
    public function getRecentlyPostAttribute()
    {

        return $this->messages()
            ->select(['id', 'from_user_id', 'message', 'attach_file', 'updated_at'])
            ->orderBy('updated_at', 'desc')
            ->take(100)
            ->get();

    }

}
