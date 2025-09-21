<?php

namespace Tests\Feature\Items;

use App\Models\Item;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemIndexSoldAndHideOwnTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 購入済みは_sold表示され自分の出品は一覧に出ない()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        // 自分の出品（一覧には出ない想定）
        $ownItem = Item::factory()->for($seller, 'seller')->create(['title' => '自分の出品']);

        // 他人の出品で「購入済み」にする
        $soldItem = Item::factory()->create(['title' => '売れた商品']);
        Purchase::create([
            'item_id' => $soldItem->id,
            'buyer_id' => $buyer->id,
            'payment_method' => 'convenience',   // DBは enum('convenience','card')
            'shipping_postal_code' => '123-4567',
            'shipping_address' => '東京都千代田区1-1-1',
            'shipping_building' => 'テストビル',
        ]);

        // ログインした状態で一覧を確認
        $res = $this->actingAs($seller)->get(route('items.index'));

        $res->assertOk()
            // 自分の出品は非表示
            ->assertDontSee('自分の出品')
            // 売れた商品は表示され、SOLD バッジも見える
            ->assertSee('売れた商品')
            ->assertSee('Sold');
    }
}
