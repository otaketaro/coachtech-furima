<?php

namespace Tests\Feature\Purchase;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressPrefillOnCreateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 購入画面でユーザープロフィール住所が初期表示される()
    {
        $user = User::factory()->create([
            'postal_code' => '123-4567',
            'address' => '東京都港区テスト町1-2-3',
            'building' => 'テストビル101',
        ]);
        $item = Item::factory()->create();

        $this->actingAs($user)
            ->get(route('purchase.create', $item))
            ->assertOk()
            ->assertSee('123-4567')
            ->assertSee('東京都港区テスト町1-2-3')
            ->assertSee('テストビル101');
    }
}
