<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class CreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
                return [
            'username' => ['required', 'string'],
            'email' => ['required', 'email:refc,dns', 'unique:users,email'], // refc: RFC 準拠, dns: 存在するドメイン, usersemail: users テーブルの email カラムとユニーク
            'password' => ['required', Password::defaults()], // デフォルトは8文字以上
            'password_confirmed' => ['required', 'same:password'],
        ];

    }


    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'username.required' => 'ユーザー名は必ず入力してください',
            'email.required' => 'メールアドレスは必ず入力してください',
            'email.email' => '不正なフォーマットです',
            'password.required' => 'パスワードは必ず入力してください',
            'password.password' => '8文字以上使って作成してください',
            'password_confirmed' => 'パスワードが一致しません',
        ];
    }
}
