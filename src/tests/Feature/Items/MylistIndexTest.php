<?php

namespace Tests\Feature\Items;

use App\Models\Item;
use App\Models\Like;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MylistIndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログイン時はいいねした商品だけ表示される_未いいねは表示されない()
    {
        $user = User::factory()->create();

        $liked = Item::factory()->create(['title' => 'いいね済み']);
        $notLike = Item::factory()->create(['title' => '未いいね']);

        Like::create(['user_id' => $user->id, 'item_id' => $liked->id]);

        $res = $this->actingAs($user)->get(route('items.index', ['tab' => 'mylist']));

        $res->assertOk()
            ->assertSee('いいね済み')
            ->assertDontSee('未いいね');
    }

    /** @test */
    public function マイリストでも購入済み商品には_soldが表示される()
    {
        $user = User::factory()->create();
        $buyer = User::factory()->create();

        $item = Item::factory()->create(['title' => '売れたいいね商品']);
        Like::create(['user_id' => $user->id, 'item_id' => $item->id]);

        // 購入済みにする
        Purchase::create([
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'payment_method' => 'convenience',
            'shipping_postal_code' => '123-4567',
            'shipping_address' => '東京都千代田区1-1-1',
            'shipping_building' => 'テストビル',
        ]);

        $res = $this->actingAs($user)->get(route('items.index', ['tab' => 'mylist']));

        $res->assertOk()
            ->assertSee('売れたいいね商品')
            ->assertSee('Sold');
    }

    /** @test */
    public function ゲストはマイリストに何も表示されない()
    {
        $res = $this->get(route('items.index', ['tab' => 'mylist']));
        $res->assertOk()
            // 空表示の文言に合わせて確認（ビューの「商品がありません。」に合わせる）
            ->assertSee('商品がありません。');
    }
}
