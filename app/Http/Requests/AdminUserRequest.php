<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminUserRequest extends FormRequest
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
            'username' => 'required|regex:/^[a-zA-Z0-9\-_]+$/|unique:users,username,' . $this['id'],
            'display_name' => 'required',
            'email' => 'required|email|unique:users,email,' . $this['id'],
            'roles_name' => 'required|array|min:1',
        ];

        if (!isset($this['id']) || $this['id'] === '') {
            // 新規なのでパスワード必須
            $rules += [
                'password' => 'required|min:6|confirmed',
                'password_confirmation' => 'required|min:6',
            ];
        } elseif (strlen($this['password']) > 0 || strlen($this['password_confirmation']) > 0) {
            // それ以外の場合は必須では無い
            $rules += [
                'password' => 'min:6|confirmed',
                'password_confirmation' => 'min:6',
            ];
        }

        return $rules;
    }

    // カスタムメッセージを設定
    public function messages()
    {
        return [
            'username.required' => 'ユーザ名は必ず入力してください。',
            'username.regex' => 'ユーザ名は半角英数字/-/_で入力してください。',
            'username.unique' => '入力されたユーザ名はすでに登録されています。別のユーザ名を入力してください。',
            'display_name.required' => '表示名は必ず入力してください。',
            'email.required' => 'メールアドレスは必ず入力して下さい。',
            'email.unique' => '入力されたメールアドレスはすでに登録されています。別のメールアドレスを入力してください。',
            'email.email' => 'メールアドレスはメールアドレスの形式で入力して下さい。',
            'roles_name.required' => '所属ロールは必ず選択して下さい。',
            'password.required' => 'パスワードは必ず入力して下さい。',
            'password.min' => 'パスワードは6文字以上入力して下さい。',
            'password.confirmed' => 'パスワードと新しいパスワード(確認)が一致しません。',
            'password_confirmation.required' => '新しいパスワード(確認)は必ず入力して下さい。',
            'password_confirmation.min' => '新しいパスワード(確認)は6文字以上入力して下さい',
        ];
    }

}
