<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログイン中にログアウトできる()
    {
        $user = User::factory()->create();

        // ログイン状態にする
        $this->actingAs($user);
        $this->assertAuthenticatedAs($user);

        // ログアウト実行（Fortifyのデフォは POST /logout）
        $res = $this->post(route('logout'));

        // どこかにリダイレクトされる（実装依存なので汎用に）
        $res->assertRedirect();

        // 未ログイン状態に戻ること
        $this->assertGuest();
    }
}
