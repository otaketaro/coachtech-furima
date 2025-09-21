<?php

namespace Tests\Feature\Mypage;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MypageSellListShowsOwnItemsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 出品タブに自分の出品商品が表示される()
    {
        $me = User::factory()->create(['name' => '太郎']);
        $mine = Item::factory()->for($me, 'seller')->create(['title' => '自分の商品A']);
        $other = Item::factory()->create(['title' => '他人の商品B']);

        $this->actingAs($me)->get(route('mypage.index', ['page' => 'sell']))
            ->assertOk()
            ->assertSee('自分の商品A')
            ->assertDontSee('他人の商品B');
    }
}
