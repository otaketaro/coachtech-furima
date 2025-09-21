<?php

namespace Tests\Feature\Items;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategorySelectionValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_is_required_on_item_store()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // カテゴリ未選択で保存を試みる
        $resp = $this->post('/items', [
            'title' => 'テスト商品',
            'brand' => 'ブランド',
            'description' => '説明',
            'price' => 1000,
            // 'categories' => [1,2]  ← わざと未指定
            'condition' => '良好',
            // 画像は既存実装に合わせる（ファイル必須なら fake を入れる）
        ]);

        $resp->assertSessionHasErrors([
            'categories', // ExhibitionRequest.php で required|min:1|array 等を想定
        ]);
    }
}
