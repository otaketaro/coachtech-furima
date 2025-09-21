<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            // 画像：任意（ある場合のみ拡張子チェック）
            'avatar' => ['nullable', 'file', 'mimes:jpeg,png'],

            // ユーザー名：必須・20文字以内
            'name' => ['required', 'string', 'max:20'],

            // 郵便番号：必須・ハイフンあり8文字（例: 123-4567）
            'postal_code' => ['required', 'string', 'size:8', 'regex:/^\d{3}-\d{4}$/'],

            // 住所：必須
            'address' => ['required', 'string', 'max:255'],

            // 建物名：任意
            'building' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'avatar.file' => 'プロフィール画像はファイルを選択してください',
            'avatar.mimes' => 'プロフィール画像は.jpegもしくは.pngを選択してください',

            'name.required' => 'お名前を入力してください',
            'name.max' => 'お名前は20文字以内で入力してください',

            'postal_code.required' => '郵便番号を入力してください',
            'postal_code.size' => '郵便番号はハイフンありの8文字で入力してください',
            'postal_code.regex' => '郵便番号は「123-4567」の形式で入力してください',

            'address.required' => '住所を入力してください',
        ];
    }
}
