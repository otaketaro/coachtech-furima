<?php

namespace Tests\Feature\Purchase;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressStoredWithPurchaseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 購入時にセッションの住所が購入レコードに保存される()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->for($seller, 'seller')->create(['price' => 3000]);

        // テストで使う住所（PUTとPOSTで同じ値を送る）
        $postal = '150-0001';
        $address = '東京都渋谷区神宮前1-1-1';
        $building = 'テストビル101';

        // まずPG07: 送付先住所を更新（セッションへ保存＆郵便番号正規化）
        $this->actingAs($buyer)->put(route('purchase.address.update', $item), [
            'shipping_postal_code' => '1500001', // ハイフン無しでもOK、コントローラで 150-0001 に正規化
            'shipping_address' => $address,
            'shipping_building' => $building,
        ])->assertRedirect(route('purchase.create', $item));

        // 次にPG06: 購入確定（POST側にも同じ値を送る）
        $res = $this->actingAs($buyer)->post(route('purchase.store', $item), [
            'payment_method' => 'convenience_store', // フォーム値 → リクエストで 'convenience' に正規化される
            'shipping_postal_code' => $postal,
            'shipping_address' => $address,
            'shipping_building' => $building,
        ]);

        $res->assertRedirect(route('items.show', $item))
            ->assertSessionHas('status', '購入が完了しました！');

        $this->assertDatabaseHas('purchases', [
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'payment_method' => 'convenience', // DBは 'convenience'
            'shipping_postal_code' => $postal,       // 150-0001
            'shipping_address' => $address,
            'shipping_building' => $building,
        ]);
    }
}
