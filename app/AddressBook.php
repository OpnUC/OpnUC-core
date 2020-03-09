<?php

namespace App;

use App\Facades\PbxLinker;
use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * App\AddressBook
 */
class AddressBook extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    // JSONに追加する属性
    protected $appends = array(
        'group_name',
        'tel1_status',
        'tel2_status',
        'tel3_status',
        'avatar_path',
    );

    /**
     * 番号変換
     * @param $value
     * @return string
     */
    private function rewriteTelNumber($value)
    {

        // 番号変換パターンを適用
        $patterns = \App\SettingNumberRewrite::where('display_replacement', true)
            ->get();

        foreach ($patterns as $pattern) {
            $value = preg_replace('/' . $pattern['pattern'] . '/i', $pattern['replacement'], $value);
        }

        return $value;
    }

    /**
     * Tel1
     * @return string
     */
    public function getTel1Attribute()
    {
        return $this->rewriteTelNumber($this->attributes['tel1']);
    }

    /**
     * Tel2
     * @return string
     */
    public function getTel2Attribute()
    {
        return $this->rewriteTelNumber($this->attributes['tel2']);
    }

    /**
     * Tel3
     * @return string
     */
    public function getTel3Attribute()
    {
        return $this->rewriteTelNumber($this->attributes['tel3']);
    }

    /**
     * グループ名
     * @return string
     */
    public function getGroupNameAttribute()
    {
        $group = \App\AddressBookGroup::find($this->groupid);

        if (!$group) {
            return '';
        }

        $groupName = $group->group_name;

        while ($group->parent_groupid != 0) {
            $group = \App\AddressBookGroup::find($group->parent_groupid);
            $groupName = $group->group_name . ' > ' . $groupName;
        }

        return $groupName;
    }

    /**
     * TEL1のステータス
     * @return string
     */
    public function getTel1StatusAttribute()
    {
        return $this->_getTelStatus($this->tel1);
    }

    /**
     * TEL2のステータス
     * @return string
     */
    public function getTel2StatusAttribute()
    {
        return $this->_getTelStatus($this->tel2);
    }

    /**
     * TEL3のステータス
     * @return string
     */
    public function getTel3StatusAttribute()
    {
        return $this->_getTelStatus($this->tel3);
    }

    /**
     * 内線番号のステータスを返す
     * @param $extNumber string 内線番号
     * @return string
     */
    private function _getTelStatus($extNumber)
    {

        // プレゼンスが有効、かつ内線番号が空で無く、0で始まっていない場合にキャッシュからプレゼンスを取得
        if (config('opnuc.enable_tel_presence') && $extNumber != '' && !Str::startsWith($extNumber, '0')) {
            $cacheKey = config('opnuc.presence_cache_key_prefix') . $extNumber;

            return Cache::get($cacheKey, 'unknown');
        }

        return 'unknown';

    }

    /**
     * アバター
     * @return string
     */
    public function getAvatarPathAttribute()
    {

        // 初期値
        $path = 'https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mm';

        // 所有者がいる場合はアバターを表示
        if ($this->owner_userid != 0) {
            $user = \App\User::find($this->owner_userid);

            // ユーザが存在しない場合は処理しない
            if (!is_null($user)) {
                $path = $user->getAvatarPathAttribute();
            }
        }

        return $path;

    }

    /**
     * 該当ユーザが所有している内線番号か確認
     * @param string $value 内線番号
     * @return bool
     */
    public function checkExtNumber($value)
    {

        if ($value == $this->tel1 || $value == $this->tel2 || $value == $this->tel3) {
            return true;
        } else {
            return false;
        }

    }

}