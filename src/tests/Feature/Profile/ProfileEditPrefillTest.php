<?php

namespace Tests\Feature\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileEditPrefillTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 編集画面でユーザーの現在値が初期表示される()
    {
        $user = User::factory()->create([
            'name' => '太郎',
            'postal_code' => '123-4567',
            'address' => '東京都千代田区1-1-1',
            'building' => 'テストビル101',
        ]);

        $res = $this->actingAs($user)->get(route('mypage.profile.edit'));

        $res->assertOk()
            ->assertSee('太郎')
            ->assertSee('123-4567')
            ->assertSee('東京都千代田区1-1-1')
            ->assertSee('テストビル101');
    }
}
