<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminSettingNumberRewriteRequest extends FormRequest
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
            'pattern' => 'required|unique:setting_number_rewrites,pattern,' . $this['id'],
            'replacement' => 'required',
            'display_replacement' => 'required|boolean',
        ];

        return $rules;
    }

    // カスタムメッセージを設定
    public function messages()
    {
        return [
            'pattern.required' => 'パターンは必ず入力してください。',
            'pattern.unique' => '入力されたパターンはすでに登録されています。別のパターンを入力してください。',
            'replacement.required' => '置換文字列は必ず入力して下さい。',
            'display_replacement.required' => '表示置換は必ず入力して下さい。',
            'display_replacement.boolean' => '表示置換は有効/無効から選択してください。',
        ];
    }

}
