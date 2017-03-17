<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
/**
 * App\AddressBookGroup
 */
class AddressBookGroup extends Model {

    protected $guarded = ['id'];

    public function childs() {
        return $this->hasMany('App\AddressBookGroup', 'parent_groupid', 'id');
    }

    /**
     * 所属グループ名を取得
     * @return string
     */
    public function FullGroupName()
    {
        $group = $this;

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

}