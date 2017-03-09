<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
/**
 * App\AddressBook
 */
class AddressBook extends Model
{
    protected $guarded = ['id'];

    /**
     * 所属グループ名を取得
     * @return string
     */
    public function GroupName()
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
}