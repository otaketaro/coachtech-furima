<?php

namespace Tests\Feature\Purchase;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CannotBuyOwnItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 自分の出品は購入できない()
    {
        $seller = User::factory()->create();
        $item = Item::factory()->for($seller, 'seller')->create();

        $res = $this->actingAs($seller)->post(route('purchase.store', $item), [
            'payment_method' => 'convenience_store',
            'shipping_postal_code' => '123-4567',
            'shipping_address' => '栃木県小山市…',
        ]);

        $res->assertRedirect(route('items.show', $item))
            ->assertSessionHas('error', '自分の出品は購入できません。');
    }
}
