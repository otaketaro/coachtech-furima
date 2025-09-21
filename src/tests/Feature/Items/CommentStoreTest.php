<?php

namespace Tests\Feature\Items;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentStoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 未ログインはコメント投稿できずログイン画面へリダイレクトされる()
    {
        $item = Item::factory()->create();

        $this->post(route('comments.store', $item), ['content' => 'guest comment'])
            ->assertRedirect(route('login')); // authミドルウェアで /login へ
    }

    /** @test */
    public function ログイン済みはコメント投稿できる_必須と255文字上限のバリデーションも効く()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 必須違反
        $this->actingAs($user)
            ->post(route('comments.store', $item), ['content' => ''])
            ->assertSessionHasErrors(['content']);

        // 256文字はNG
        $tooLong = str_repeat('a', 256);
        $this->actingAs($user)
            ->post(route('comments.store', $item), ['content' => $tooLong])
            ->assertSessionHasErrors(['content']);

        // 255文字はOK -> DBに保存される
        $ok = str_repeat('a', 255);
        $this->actingAs($user)
            ->post(route('comments.store', $item), ['content' => $ok])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('comments', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'content' => $ok,
        ]);
    }
}
