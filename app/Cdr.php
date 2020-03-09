<?php

namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

class Cdr extends Model
{
    use UsesTenantConnection;

    // for Carbon
    protected $dates = ['created_at', 'updated_at', 'start_datetime', 'end_datetime'];
    // JSONに追加する属性
    protected $appends = array(
        'sender_name',
        'destination_name',
    );

    /**
     * 発信者の名称
     * @return null|string
     */
    public function getSenderNameAttribute()
    {
        return $this->_getNumber2Name($this->sender);
    }

    /**
     * 着信者の名称
     * @return null|string
     */
    public function getDestinationNameAttribute()
    {
        return $this->_getNumber2Name($this->destination);
    }

    /**
     * アドレス帳から名称を取得
     * @param string $number
     * @return null|string
     */
    private function _getNumber2Name($number)
    {

        // ToDo: データベースへの検索が重たいため、機能ON/OFFが選べるようにする

        // 番号情報に付加情報がある場合があるため、「先頭の数字 # *」のみとする
        $number = preg_replace('/^([0-9\#\*]+).*$/', '$1', $number);

        $redisItem = Redis::get('numberList-' . $number);

        if (!$redisItem) {
            // 種別が、内線電話帳/共通電話帳
            // ToDo: 個人電話帳も使いたいが、キャッシュが共有なので、いまいち
            $dbItem = AddressBook::whereIn('type', [1, 2])
                ->where(function ($query) use ($number) {
                    $query
                        ->where('tel1', $number)
                        ->orWhere('tel2', $number)
                        ->orWhere('tel3', $number);
                })
                ->first();
        } else {
            return $redisItem;
        }

        if ($dbItem) {
            Redis::set('numberList-' . $number, $dbItem->name);
            Redis::expire('numberList-' . $number, 60 * 60); // 60 min

            return $dbItem->name;
        }

        return null;

    }

}
