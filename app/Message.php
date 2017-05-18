<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    // 隠す属性
    protected $hidden = array(
        'from_user',
        'to_user',
    );

    // 追加する属性
    protected $appends = array(
        'display_name',
        'avatar_url',
        'is_posted',
    );

    /**
     * attache_fileフィールドの取得
     * @param $value
     * @return mixed
     */
    public function getAttachFileAttribute($value)
    {
        // JSONで保存しているため、デコード
        return json_decode($value);
    }

    /**
     * attache_fileフィールドのセット
     * @param $value
     */
    public function setAttachFileAttribute($value)
    {
        // JSONで保存するため、エンコード
        $this->attributes['attach_file'] = json_encode($value);
    }

    /**
     * 表示名
     * @return mixed
     */
    public function getDisplayNameAttribute()
    {
        return $this->from_user->display_name;
    }

    /**
     * アバターパス
     * @return mixed
     */
    public function getAvatarUrlAttribute()
    {
        return $this->from_user->getAvatarPathAttribute();
    }

    /**
     * 配信済みフラグ
     * @return bool
     */
    public function getIsPostedAttribute()
    {
        // DBから取得する際はすべて配信済みなので、trueで返す
        return true;
    }

    /**
     * 送信元ユーザ
     */
    public function from_user()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    /**
     * 送信先ユーザ
     */
    public function to_user()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

}
