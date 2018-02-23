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

            $path = $user->getAvatarPathAttribute();
        }

        return $path;

    }

}