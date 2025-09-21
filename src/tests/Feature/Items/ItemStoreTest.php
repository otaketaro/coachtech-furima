<?php

namespace Tests\Feature\Items;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemStoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 出品時に必須項目が保存され画像も保存されカテゴリが中間テーブルに紐づく()
    {
        // 認証
        $user = User::factory()->create();
        $this->actingAs($user);

        // 画像ストレージの偽装
        Storage::fake('public');

        // カテゴリを2件用意
        $c1 = Category::factory()->create();
        $c2 = Category::factory()->create();

        // 送信ペイロード
        $payload = [
            'title' => '出品テスト商品',
            'brand' => 'TEST BRAND',
            'description' => '説明テキスト',
            'price' => 1234,
            'condition' => 'good',
            'image' => UploadedFile::fake()->create('photo.jpg', 10, 'image/jpeg'),

            'categories' => [$c1->id, $c2->id],
        ];

        // 送信
        $res = $this->post(route('items.store'), $payload);

        // 詳細へリダイレクト
        $item = Item::first();
        $res->assertRedirect(route('items.show', $item));

        // DB: items
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'user_id' => $user->id,
            'title' => '出品テスト商品',
            'brand' => 'TEST BRAND',
            'description' => '説明テキスト',
            'price' => 1234,
            'condition' => 'good',
            'status' => Item::STATUS_SELLING,
        ]);

        // 画像が保存されている
        Storage::disk('public')->assertExists($item->image_path);

        // 中間テーブル: category_item
        $this->assertTrue($item->categories()->whereKey([$c1->id, $c2->id])->count() === 2);
    }
}
