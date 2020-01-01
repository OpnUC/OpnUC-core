<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminRoleRequest extends FormRequest
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

    public function rules()
    {

        $rules = [
            'id' => 'numeric',
            'name' => 'required|unique:roles,name,' . $this['id'],
            'display_name' => 'required',
            'perms_name' => 'required|array|min:1',
        ];

        return $rules;
    }

    // カスタムメッセージを設定
    public function messages()
    {
        return [
            'name.required' => 'ロール名は必ず入力してください。',
            'name.unique' => '入力されたロール名はすでに登録されています。別のロール名を入力してください。',
            'display_name.required' => '表示名は必ず入力してください。',
            'perms_name.required' => '付与パーミッションは必ず選択して下さい。',
        ];
    }

}
