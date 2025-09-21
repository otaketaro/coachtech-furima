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
    // - 基本要件：登録・パスワードリセットをON
    // - 応用要件：メール認証はコメントアウトしておく（実装時に外す）
    // - プロフィール／パスワード更新は後工程で使うためONのままでOK
    'features' => [
        Features::registration(),        // 会員登録（必須）
        Features::resetPasswords(),      // パスワード再設定（必須）
        // Features::emailVerification(), // 応用：メール認証を実装するときにON

        // 以降はプロフィール編集やパスワード変更のAPI（後半要件で活用）
        Features::updateProfileInformation(),
        Features::updatePasswords(),

        // 2要素認証は要件外なのでOFF
        // Features::twoFactorAuthentication([
        //     'confirm' => true,
        //     'confirmPassword' => true,
        // ]),
    ],
];
