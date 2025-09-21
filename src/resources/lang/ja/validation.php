<?php

return [
    'required' => ':attributeを入力してください',
    'email' => 'メールアドレスはメール形式で入力してください',
    'min' => [
        'string' => ':attributeは:min文字以上で入力してください',
    ],
    'confirmed' => 'パスワードと一致しません',

    'attributes' => [
        'name' => 'お名前',
        'email' => 'メールアドレス',
        'password' => 'パスワード',
        'password_confirmation' => '確認用パスワード',
        'content' => 'コメント',

        // 送付先関連
        'shipping_postal_code' => '郵便番号',
        'shipping_address' => '住所',
        'shipping_building' => '建物名',
    ],
];
