<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterValidationTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(array $override = []): array
    {
        return array_merge([
            'name' => '大竹太郎',
            'email' => 'taro@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ], $override);
    }

    /** @test */
    public function 名前未入力でエラー文言()
    {
        $res = $this->post(route('register'), $this->validPayload(['name' => '']));
        $res->assertSessionHasErrors([
            'name' => 'お名前を入力してください',
        ]);
    }

    /** @test */
    public function メール未入力でエラー文言()
    {
        $res = $this->post(route('register'), $this->validPayload(['email' => '']));
        $res->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }

    /** @test */
    public function メール形式不正でエラー文言()
    {
        $res = $this->post(route('register'), $this->validPayload(['email' => 'not-an-email']));
        $res->assertSessionHasErrors([
            'email' => 'メールアドレスはメール形式で入力してください',
        ]);
    }

    /** @test */
    public function パスワード未入力でエラー文言()
    {
        $res = $this->post(route('register'), $this->validPayload(['password' => '', 'password_confirmation' => '']));
        $res->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);
    }

    /** @test */
    public function パスワード7文字以下でエラー文言()
    {
        $res = $this->post(route('register'), $this->validPayload([
            'password' => 'short7',             // 7文字
            'password_confirmation' => 'short7',
        ]));
        $res->assertSessionHasErrors([
            'password' => 'パスワードは8文字以上で入力してください',
        ]);
    }

    /** @test */
    public function 確認用パスワード不一致でエラー文言()
    {
        $res = $this->post(route('register'), $this->validPayload([
            'password_confirmation' => 'not-match',
        ]));
        $res->assertSessionHasErrors([
            'password' => 'パスワードと一致しません',
        ]);
    }

    /** @test */
    public function 正常登録でユーザーが作成されプロフィール設定画面へ遷移()
    {
        $payload = $this->validPayload();
        $res = $this->post(route('register'), $payload);

        // 成功時の遷移（仕様：初回はプロフィール設定画面へ）
        $res->assertRedirect(route('mypage.profile.edit'));

        $this->assertDatabaseHas('users', [
            'email' => 'taro@example.com',
            'name' => '大竹太郎',
        ]);

        // ログイン状態であること（セッションにユーザIDが入る想定）
        $this->assertAuthenticated();
    }
}
