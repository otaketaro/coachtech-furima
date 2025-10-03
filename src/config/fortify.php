<?php

use Laravel\Fortify\Features;

return [

    // どのガードで認証するか
    'guard' => 'web',

    // パスワードブローカー
    'passwords' => 'users',

    // ログインIDはメールアドレス
    'username' => 'email',
    'email' => 'email',

    // ユーザー名を小文字化（メール想定なので true でOK）
    'lowercase_usernames' => true,

    // 認証成功時の遷移先（ログイン・パスワードリセットなど）
    // 一覧トップへ返す要件に合わせて '/' を指定
    'home' => '/',

    // Fortify が登録するルートのプレフィックス／サブドメイン
    'prefix' => '',
    'domain' => null,

    // Fortify が付与するミドルウェア
    'middleware' => ['web'],

    // レートリミッター（FortifyServiceProvider で 'login', 'two-factor' を定義済み）
    'limiters' => [
        'login' => 'login',
        'two-factor' => 'two-factor',
    ],

    // Fortify の“ビューを返す”ルートを使うか（Bladeを使うので true）
    'views' => true,

    // ===== 有効化する機能 =====
    'features' => [
        Features::registration(),
        Features::resetPasswords(),
        Features::updateProfileInformation(),
        Features::updatePasswords(),

    ],
];
