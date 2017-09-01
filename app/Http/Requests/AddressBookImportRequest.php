<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressBookImportRequest extends FormRequest
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

        return [
            'import_file' => 'required|file|mimes:csv,txt',
        ];
    }

    // カスタムメッセージを設定
    public function messages()
    {
        return [
            'import_file.required' => 'ファイルが選択されていません。',
            'import_file.mimes' => 'CSVファイル以外がアップロードされました。',
        ];
    }

}
