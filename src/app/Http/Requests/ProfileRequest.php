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
            'avatar' => ['nullable', 'file', 'mimes:jpeg,png'],

            'name' => ['required', 'string', 'max:20'],

            'postal_code' => ['required', 'string', 'size:8', 'regex:/^\d{3}-\d{4}$/'],

            'address' => ['required', 'string', 'max:255'],

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
