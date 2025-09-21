<?php

namespace Tests\Feature\Mypage;

use App\Models\Item;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MypagePurchasesIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_mypage_purchases_shows_only_bought_items(): void
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();

        // 購入済み商品（buyerが購入）
        $bought = Item::factory()->create([
            'user_id' => $seller->id,
            'status' => 'sold',
        ]);

        Purchase::factory()->create([
            'item_id' => $bought->id,
            'buyer_id' => $buyer->id,
            'price' => $bought->price,
            'payment_method' => 'card',         // enum: convenience_store|card
            'status' => 'completed',    // enum: trading|completed
            'shipping_postal_code' => '123-4567',
            'shipping_address' => '東京都渋谷区1-1-1',
            'shipping_building' => 'テストビル',
        ]);

        // 未購入の商品（一覧に出ないこと）
        $notBought = Item::factory()->create([
            'user_id' => $seller->id,
        ]);

        $this->actingAs($buyer)
            ->get('/mypage?page=buy')
            ->assertOk()
            ->assertSee($bought->title)        // 購入した商品は表示される
            ->assertDontSee($notBought->title); // 未購入は表示されない
    }
}
