<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressBookRequest extends FormRequest
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
    public function rules() {
        return [
            'id' => 'numeric',
            'name_kana' => 'required',
            'name' => 'required',
            'type' => 'required',
            'groupid' => 'required',
            'tel1' => 'nullable|numeric',
            'tel2' => 'nullable|numeric',
            'tel3' => 'nullable|numeric',
            'email' => 'nullable|email',
            'owner_userid' => 'nullable|numeric|unique:address_books,owner_userid,' . $this['id'],
        ];
    }

    // カスタムメッセージを設定
    public function messages() {
        return [
            'name_kana.required' => '名前(カナ)は必ず入力して下さい。',
            'name.required' => '名前は必ず入力して下さい。',
            'type.required' => '電話帳種別は必ず選択して下さい。',
            'groupid.required' => '所属グループは必ず選択して下さい。',
            'tel1.numeric' => '電話番号1は半角数字で入力してください。',
            'tel2.numeric' => '電話番号2は半角数字で入力してください。',
            'tel3.numeric' => '電話番号3は半角数字で入力してください。',
            'email.email' => 'メールアドレスはメールアドレスの形式で入力してください。',
            'owner_userid.unique' => '選択されたユーザはすでに別の電話帳を持っています。',
        ];
    }

}
