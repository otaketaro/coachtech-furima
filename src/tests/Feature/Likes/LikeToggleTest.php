<?php

namespace Tests\Feature\Likes;

use App\Models\Item;
use App\Models\Like;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikeToggleTest extends TestCase
{
    use RefreshDatabase;

    private function likePayload(): array
    {
        // いいねはIDだけで十分（CSRFはテストで自動処理される）
        return [];
    }

    /** @test */
    public function ログインユーザーは商品にいいねできる()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // いいね実行
        $res = $this->actingAs($user)->post(route('likes.store', $item), $this->likePayload());
        $res->assertRedirect(); // 実装依存（詳細画面など）なのでリダイレクトだけ確認

        // DBに保存されたか
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // 詳細画面でカウントが増えている
        $this->actingAs($user)->get(route('items.show', $item))
            ->assertOk()
            ->assertSee((string) 1); // いいね数 1 が見える
    }

    /** @test */
    public function いいね後は解除ボタン表示に切り替わる_フォームメソッドで判定()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // まずは未いいね状態：詳細に「POST /item/{id}/like」フォーム（= いいねボタン）がある想定
        $this->actingAs($user)->get(route('items.show', $item))
            ->assertOk()
            ->assertSee('/item/'.$item->id.'/like'); // actionの存在でざっくり確認

        // いいねする
        $this->actingAs($user)->post(route('likes.store', $item), $this->likePayload())
            ->assertRedirect();

        // いいね後：詳細に「DELETE /item/{id}/like」フォーム（= 解除ボタン）がある想定
        // LaravelのDELETEは< input name="_method" value="DELETE" >の疑似メソッドで表現されることが多い
        $this->actingAs($user)->get(route('items.show', $item))
            ->assertOk()
            ->assertSee('/item/'.$item->id.'/like')
            ->assertSee('_method')
            ->assertSee('DELETE');
    }

    /** @test */
    public function 再度押すといいね解除できカウントが減る()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 先にいいね済みにしておく
        Like::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // 解除実行
        $res = $this->actingAs($user)->delete(route('likes.destroy', $item));
        $res->assertRedirect();

        // DBから消えている
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // 詳細でカウント0（少なくとも 1 は出ない）
        $this->actingAs($user)->get(route('items.show', $item))
            ->assertOk()
            ->assertDontSee('metric__num">1')->assertSee('<span class="metric__num">0</span>', false);
    }
}
