<?php

namespace Tests\Feature\Purchase;

use App\Models\Item;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CannotDoublePurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_purchase_item_already_sold_by_another_user(): void
    {
        $seller = User::factory()->create();
        $buyer1 = User::factory()->create(); // 先に買った人
        $buyer2 = User::factory()->create(); // 後から買おうとする人

        // 既に SOLD の商品を用意
        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'status' => 'sold',
        ]);

        // buyer1 が購入済みのレコード
        Purchase::factory()->create([
            'item_id' => $item->id,
            'buyer_id' => $buyer1->id,
            'price' => $item->price ?? 12345, // price列あり前提。無ければ固定値でもOK
            'payment_method' => 'card',
            'status' => 'completed',
            'shipping_postal_code' => '123-4567',
            'shipping_address' => '東京都渋谷区1-1-1',
            'shipping_building' => 'A',
        ]);

        // 別ユーザー(buyer2)が再購入を試みる → 失敗すべき
        $resp = $this->actingAs($buyer2)->post("/purchase/{$item->id}", [
            'payment_method' => 'card',
            'shipping_postal_code' => '987-6543',
            'shipping_address' => '東京都新宿区9-9-9',
            'shipping_building' => 'B',
        ]);

        // 実装に合わせていずれか一つでOK（おすすめは 302 + エラーメッセージ）
        $resp->assertStatus(302)->assertSessionHasErrors(); // 例：back()->withErrors(...)

        // 二重レコードが増えていない
        $this->assertDatabaseCount('purchases', 1);
    }
}
