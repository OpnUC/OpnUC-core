<?php

namespace App;

use App\Facades\PbxLinker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Predis\CommunicationException;
use Predis\Connection\ConnectionException;

/**
 * App\AddressBook
 */
class AddressBook extends Model
{
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
     * @todo 情報の取得がパッケージに依存するため見直す
     * @return string
     */
    private function _getTelStatus($value)
    {

        // プレゼンスが有効で、
        // 値があり、0で始まらない場合のみステータスを取得
        if (config('opnuc.enable_tel_presence') && $value && !starts_with($value, '0')) {
            return PbxLinker::getPresence($value);
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