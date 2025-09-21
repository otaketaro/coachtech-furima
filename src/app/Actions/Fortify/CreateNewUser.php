<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    /**
     * 新規ユーザー登録処理
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make(
            $input,
            [
                // RegisterRequest 指示どおり
                'name' => ['required', 'string', 'max:20'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'same:password_confirmation'],
                'password_confirmation' => ['required', 'string', 'min:8', 'same:password'],
            ],
            [
                'name.required' => 'お名前を入力してください',
                'name.max' => 'お名前は20文字以内で入力してください',
                'email.required' => 'メールアドレスを入力してください',
                'email.email' => 'メールアドレスはメール形式で入力してください',
                'email.unique' => 'このメールアドレスは既に登録されています',
                'password.required' => 'パスワードを入力してください',
                'password.min' => 'パスワードは8文字以上で入力してください',
                'password.same' => 'パスワードと一致しません',
                'password_confirmation.required' => '確認用パスワードを入力してください',
                'password_confirmation.min' => '確認用パスワードは8文字以上で入力してください',
                'password_confirmation.same' => 'パスワードと一致しません',
            ]
        )->validate();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
