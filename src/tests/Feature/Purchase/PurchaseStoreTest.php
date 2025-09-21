<?php

namespace Tests\Feature\Purchase;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseStoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 支払い方法が必須かつ許可値のみ()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        $item = Item::factory()->create(['user_id' => $seller->id, 'price' => 1000]);

        // 未入力
        $this->actingAs($buyer)
            ->post(route('purchase.store', $item), [
                'payment_method' => '',
                'shipping_postal_code' => '123-4567',
                'shipping_address' => '東京都千代田区1-1-1',
                'shipping_building' => '',
            ])
            ->assertSessionHasErrors(['payment_method']);

        // 許可外の値
        $this->actingAs($buyer)
            ->post(route('purchase.store', $item), [
                'payment_method' => 'bitcoin',
                'shipping_postal_code' => '123-4567',
                'shipping_address' => '東京都千代田区1-1-1',
                'shipping_building' => '',
            ])
            ->assertSessionHasErrors(['payment_method']);
    }
}
