<?php

namespace Tests\Feature\Items;

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemIndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ゲストでも一覧が見える()
    {
        Item::factory()->create(['title' => 'テスト商品']);

        $res = $this->get(route('items.index'));

        $res->assertOk()
            ->assertSee('テスト商品');
    }
}
