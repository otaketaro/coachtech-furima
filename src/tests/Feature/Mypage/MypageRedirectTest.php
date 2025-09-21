<?php

namespace Tests\Feature\Mypage;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MypageRedirectTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログイン後にマイページへ行くとsellへ正規化される()
    {
        $user = User::factory()->create();

        $res = $this->actingAs($user)->get(route('mypage.index'));

        $res->assertRedirect(route('mypage.index', ['page' => 'sell']));
    }
}
