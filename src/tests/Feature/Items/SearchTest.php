<?php

namespace Tests\Feature\Items;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function タイトル部分一致で検索できる()
    {
        Item::factory()->create(['title' => 'Apple Watch SE']);
        Item::factory()->create(['title' => 'タンブラー']);
        Item::factory()->create(['title' => 'Watch バンド']);

        $res = $this->get(route('items.index', ['q' => 'Watch']));
        $res->assertOk()
            ->assertSee('Apple Watch SE')
            ->assertSee('Watch バンド')
            ->assertDontSee('タンブラー');
    }

    /** @test */
    public function 検索状態はマイリストでも保持される()
    {
        $user = User::factory()->create();

        $target1 = Item::factory()->create(['title' => 'Rolax 腕時計']);
        $target2 = Item::factory()->create(['title' => '腕時計 ベルト']);
        $other = Item::factory()->create(['title' => 'コーヒーミル']);

        // ユーザーが target1, target2 にいいね
        $this->actingAs($user)->post(route('likes.store', $target1));
        $this->actingAs($user)->post(route('likes.store', $target2));

        // 検索キーワードを保持したままマイリストへ
        $res = $this->actingAs($user)->get(route('items.index', ['q' => '腕時計', 'tab' => 'mylist']));
        $res->assertOk()
            ->assertSee('Rolax 腕時計')
            ->assertSee('腕時計 ベルト')
            ->assertDontSee('コーヒーミル');
    }
}
