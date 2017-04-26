<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'id' => 'required|numeric',
            'username' => 'required|unique:users,username,' . \Auth::user()->id,
            'display_name' => 'required',
            'email' => 'required|email',
            'avatar_type' => 'required|in:1,2',
        ];
    }

    // カスタムメッセージを設定
    public function messages() {
        return [
            'display_name.required' => '表示名は必ず入力してください。',
            'email.required' => 'メールアドレスは必ず入力して下さい。',
            'email.email' => 'メールアドレスはメールアドレスの形式で入力して下さい。',
            'avatar_type.required' => 'アバタータイプが必ず選択して下さい。',
        ];
    }

}
