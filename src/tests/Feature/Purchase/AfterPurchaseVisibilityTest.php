<?php

namespace Tests\Feature\Purchase;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AfterPurchaseVisibilityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 購入後は一覧で_sold表示されマイページ購入履歴にも出る()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->for($seller, 'seller')->create(['title' => '購入される商品', 'price' => 1200]);

        // 購入実行（★ 必要な入力を送る）
        $payload = [
            'payment_method' => 'convenience_store',
            'shipping_postal_code' => '123-4567',
            'shipping_address' => '東京都千代田区1-1-1',
            'shipping_building' => 'テストビル',
        ];
        $this->actingAs($buyer)->post(route('purchase.store', $item), $payload)
            ->assertRedirect();

        // 一覧で Sold 表示
        $resIndex = $this->get(route('items.index'));
        $resIndex->assertOk()
            ->assertSee('購入される商品')
            ->assertSee('Sold');

        // マイページ購入履歴（?page=buy）に表示される
        $resBuy = $this->actingAs($buyer)->get(route('mypage.index', ['page' => 'buy']));
        $resBuy->assertOk()
            ->assertSee('購入される商品');
    }
}
