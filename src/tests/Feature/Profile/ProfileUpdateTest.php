<?php

namespace Tests\Feature\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 正常にプロフィールを更新できる()
    {
        $user = User::factory()->create([
            'name' => '旧名',
            'postal_code' => '111-1111',
            'address' => '旧住所',
            'building' => '旧建物',
        ]);

        $payload = [
            'name' => '新しい太郎',
            'postal_code' => '123-4567',          // 仕様：ハイフンあり8文字
            'address' => '新住所',
            'building' => '新建物',
        ];

        $res = $this->actingAs($user)->put(route('mypage.profile.update'), $payload);

        $res->assertRedirect(); // 実装の遷移先に合わせてOK（editやmypageなど）
        $user->refresh();

        $this->assertSame('新しい太郎', $user->name);
        $this->assertSame('123-4567', $user->postal_code);
        $this->assertSame('新住所', $user->address);
        $this->assertSame('新建物', $user->building);
    }

    /** @test */
    public function バリデーション_名前必須_20文字以内_郵便番号形式_住所必須()
    {
        $user = User::factory()->create();

        $payload = [
            'name' => str_repeat('あ', 21),  // 21文字 → NG
            'postal_code' => '1234567',            // ハイフンなし → NG（仕様に合わせる）
            'address' => '',                   // 必須違反
            'building' => '何か',
        ];

        $res = $this->actingAs($user)->put(route('mypage.profile.update'), $payload);

        $res->assertSessionHasErrors(['name', 'postal_code', 'address']);
    }
}
