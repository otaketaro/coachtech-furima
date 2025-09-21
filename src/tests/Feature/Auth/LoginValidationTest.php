<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginValidationTest extends TestCase
{
    use RefreshDatabase;

    private function postLogin(array $override = [])
    {
        $base = [
            'email' => 'taro@example.com',
            'password' => 'password123',
        ];

        return $this->post(route('login'), array_merge($base, $override));
    }

    /** @test */
    public function メール未入力でエラー文言()
    {
        $res = $this->postLogin(['email' => '']);
        $res->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }

    /** @test */
    public function パスワード未入力でエラー文言()
    {
        $res = $this->postLogin(['password' => '']);
        $res->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);
    }

    /** @test */
    public function 入力情報が間違っている場合はエラー文言()
    {
        // ユーザー未作成、または存在してもパスワード不一致
        User::factory()->create([
            'email' => 'taro@example.com',
            'password' => Hash::make('correct-pass'),
        ]);

        $res = $this->postLogin(['password' => 'wrong-pass']);

        $res->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません',
        ]);
        $this->assertGuest();
    }

    /** @test */
    public function 正しい情報ならログイン成功()
    {
        User::factory()->create([
            'email' => 'taro@example.com',
            'password' => Hash::make('password123'),
        ]);

        $res = $this->postLogin();

        // 成功時の遷移先は実装に依存（/ や /mypage など）なので「何かにリダイレクト」だけを確認
        $res->assertRedirect();
        $this->assertAuthenticated();
    }

    /** @test */
    public function 未認証で保護ページへアクセスするとログイン画面へリダイレクトされる()
    {
        $this->get(route('mypage.index'))
            ->assertRedirect(route('login'));
    }
}
