<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUploadAvatarRequest extends FormRequest
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
            'avatar_file' => 'required|file|mimes:jpeg,gif,png|dimensions:max_width=640,max_height=480',
        ];
    }

    // カスタムメッセージを設定
    public function messages() {
        return [
            'avatar_file.required' => 'ファイルが選択されていません。',
            'avatar_file.mimes' => '画像ファイル以外がアップロードされました。',
            'avatar_file.dimensions' => '画像ファイルは、横 640ピクセル 縦 480ピクセル以下のファイルとしてください。',
        ];
    }

}
