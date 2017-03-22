<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressBookGroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function rules() {
        return [
            'id' => 'numeric',
            'type' => 'required',
            'parent_groupid' => 'required',
            'group_name' => 'required',
        ];
    }

    // カスタムメッセージを設定
    public function messages() {
        return [
            'type.required' => '種別は必ず選択して下さい。',
            'parent_groupid.required' => '親グループは必ず選択してください。',
            'group_name.required' => 'グループ名は必ず入力して下さい。',
        ];
    }

}
