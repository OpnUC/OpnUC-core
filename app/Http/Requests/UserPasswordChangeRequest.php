<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserPasswordChangeRequest extends FormRequest
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
            'password' => 'required',
            'newPassword' => 'required|min:6|confirmed',
            'newPassword_confirmation' => 'required|min:6',
        ];
    }

    // カスタムメッセージを設定
    public function messages() {
        return [
            'password.required' => '現在のパスワードは必ず入力してください。',
            'newPassword.required' => '新しいパスワードは必ず入力して下さい。',
            'newPassword.min' => '新しいパスワードは6文字以上入力して下さい。',
            'newPassword.confirmed' => '新しいパスワードと新しいパスワード(確認)が一致しません。',
            'newPassword_confirmation.required' => '新しいパスワード(確認)は必ず入力して下さい。',
            'newPassword_confirmation.min' => '新しいパスワード(確認)は6文字以上入力して下さい',
        ];
    }

}
