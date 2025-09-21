<?php

namespace Tests\Feature\Comments;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentStoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 未ログインはコメント送信できずログインへリダイレクト()
    {
        $item = Item::factory()->create();

        $this->post(route('comments.store', $item), [
            'content' => 'ゲスト投稿',
        ])->assertRedirect(route('login'));

        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'content' => 'ゲスト投稿',
        ]);
    }

    /** @test */
    public function ログイン済みはコメントを送信でき_d_b保存される_詳細に反映される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $payload = ['content' => 'コメントテスト'];

        // 送信
        $this->actingAs($user)
            ->post(route('comments.store', $item), $payload)
            ->assertRedirect(); // 遷移先は実装依存（詳細など）

        // DB保存
        $this->assertDatabaseHas('comments', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'content' => 'コメントテスト',
        ]);

        // 詳細に反映（件数や本文）
        $this->get(route('items.show', $item))
            ->assertOk()
            // 件数表示が「コメント（1）」のように出る想定（HTMLに依存するので緩めに確認）
            ->assertSee('コメント（')
            ->assertSee('コメントテスト');
    }

    /** @test */
    public function バリデーション_必須と255文字上限()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 未入力
        $this->actingAs($user)
            ->post(route('comments.store', $item), ['content' => ''])
            ->assertSessionHasErrors(['content']);

        // 256文字でエラー
        $tooLong = str_repeat('あ', 256);
        $this->actingAs($user)
            ->post(route('comments.store', $item), ['content' => $tooLong])
            ->assertSessionHasErrors(['content']);

        // 255文字はOK
        $justOk = str_repeat('あ', 255);
        $this->actingAs($user)
            ->post(route('comments.store', $item), ['content' => $justOk])
            ->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'content' => $justOk,
        ]);
    }
}
