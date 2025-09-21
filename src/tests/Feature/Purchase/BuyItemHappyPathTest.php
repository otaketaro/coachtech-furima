<?php

namespace Tests\Feature\Purchase;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuyItemHappyPathTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 正常購入でレコード作成とメッセージ()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->for($seller, 'seller')->create(['price' => 4000]);

        $payload = [
            'payment_method' => 'convenience_store',
            'shipping_postal_code' => '123-4567',
            'shipping_address' => '栃木県小山市…',
            'shipping_building' => 'A-1',
        ];

        $res = $this->actingAs($buyer)->post(route('purchase.store', $item), $payload);

        $res->assertRedirect(route('items.show', $item))
            ->assertSessionHas('status', '購入が完了しました！');

        $this->assertDatabaseHas('purchases', [
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'payment_method' => 'convenience',
        ]);
    }
}
