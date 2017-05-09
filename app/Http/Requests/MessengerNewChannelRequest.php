<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessengerNewChannelRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|regex:/^[a-zA-Z0-9]+$/|unique:messenger_channels,name'
        ];
    }

    // カスタムメッセージを設定
    public function messages() {
        return [
            'name.required' => 'チャンネル名は必ず入力して下さい。',
            'name.regex' => 'チャンネル名は半角英数字で入力してください。',
            'name.unique' => 'すでにチャンネルが存在しています。',
        ];
    }
}
