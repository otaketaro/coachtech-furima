<?php

namespace Tests\Feature\Purchase;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressValidationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 郵便番号が不正ならエラーになる()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        $item = \App\Models\Item::factory()->create(['user_id' => $seller->id]);

        $this->actingAs($buyer)
            ->put(route('purchase.address.update', $item), [
                'shipping_postal_code' => 'ABCDEF', // 不正
                'shipping_address' => '東京都港区1-2-3',
                'shipping_building' => 'テストビル101',
            ])
            ->assertSessionHasErrors(['shipping_postal_code']);
    }
}
