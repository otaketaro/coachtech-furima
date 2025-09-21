<?php

namespace App\Actions\Fortify;

trait PasswordValidationRules
{
    /**
     * フリマアプリ要件のパスワードバリデーション
     *
     * - 必須
     * - 文字列
     * - 8文字以上
     * - password_confirmation と一致
     *
     * @return array<int, string>
     */
    protected function passwordRules(): array
    {
        return ['required', 'string', 'min:8', 'confirmed'];
    }
}
