# coachtech-furima
フリマアプリ

## 環境構築
**Dockerビルド**
1. `git clone https://github.com/otaketaro/coachtech-furima`
2. Docker Desktop を起動
3. `docker-compose up -d --build`

**Laravel環境構築**
1. `docker-compose exec php bash`
2. `composer install`
3. 「.env.example」ファイルを コピーして「.env」を作成
    cp .env.example .env

4. .env の DB 設定を以下に変更
``` text
DB_HOST=mysql
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```
5. アプリケーションキーの作成
``` bash
php artisan key:generate
```

6. マイグレーションの実行
``` bash
php artisan migrate
```

7. シーディングの実行
``` bash
php artisan db:seed
```

## 使用技術
1. Laravel Framework 8.83.8
2. PHP 8.1.33
3. nginx 1.21.1
4. MySQL 8.0.26
5. Docker 28.0.1


## ER図

![ER図](./docs/er.png)





## 管理者ログイン情報
- メールアドレス: admin@example.com
- パスワード: password123

## 一般ユーザーログイン情報
- メールアドレス: buyer1@example.com
- パスワード: password123


## URL
- 開発環境：http://localhost
- phpMyAdmin：http://localhost:8080


## テストについて

本アプリケーションは PHPUnit による自動テストを実装済みです。以下の手順でテストを実行できます。

コンテナに入る
docker-compose exec php bash

テスト環境用にマイグレーション・シーディングを実行

php artisan migrate --env=testing

php artisan db:seed --env=testing

テストを実行
php artisan test

主なテスト内容
ユーザー登録／ログイン機能
出品機能（商品登録・一覧・詳細表示）
購入機能（住所入力・支払い方法選択・購入確定処理）
バリデーション（必須項目未入力時のエラーメッセージ表示など）購入後の一覧での「SOLD」表示確認
マイページ購入履歴の表示確認、全てのテストがグリーンになることを確認済みです。

## 補足
商品詳細ページのエラーメッセージの指定がなかったため、「リスト内の項目を選択してください」というエラーメッセージが出るようにしてあります。