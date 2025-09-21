<?php

namespace Tests\Feature\Items;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Item;
use App\Models\Like;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 商品詳細に必要な情報が表示される()
    {
        $seller = User::factory()->create();
        $item = Item::factory()->for($seller, 'seller')->create([
            'title' => 'テスト商品',
            'brand' => 'テストブランド',
            'description' => 'これは説明文です',
            'condition' => 'good',
            'price' => 1234,
        ]);

        // カテゴリを2つ付与
        $categories = Category::factory()->count(2)->create();
        $item->categories()->attach($categories->pluck('id'));

        // いいねとコメントを追加
        Like::factory()->for($item)->create();
        Comment::factory()->for($item)->create(['content' => 'コメントテスト']);

        $res = $this->get(route('items.show', $item));

        $res->assertOk()
            ->assertSee('テスト商品')
            ->assertSee('テストブランド')
            ->assertSee('これは説明文です')
            ->assertSee('目立った傷や汚れなし') // condition=good の日本語ラベル
            ->assertSee('¥1,234') // 値段（ビューが通貨フォーマット表示）
            ->assertSee('1')    // いいね数 or コメント数が反映されている
            ->assertSee($categories[0]->name)
            ->assertSee($categories[1]->name)
            ->assertSee('コメントテスト');
    }
}
