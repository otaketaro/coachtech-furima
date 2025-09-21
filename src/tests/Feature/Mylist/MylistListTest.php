<?php

namespace Tests\Feature\Mylist;

use App\Models\Item;
use App\Models\Like;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class MylistListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function いいねした商品だけが表示される_未ログインは何も表示されない()
    {
        $user = User::factory()->create();

        $liked = Item::factory()->create(['title' => 'いいね対象']);
        $unliked = Item::factory()->create(['title' => 'いいねしてない']);

        // ユーザーのいいねを作成
        Like::query()->create([
            'user_id' => $user->id,
            'item_id' => $liked->id,
        ]);

        // 未ログイン状態を明示化
        Auth::logout();
        $this->flushSession();

        // 未ログイン：マイリストは空（タイトルが出ない）
        $this->get(route('items.index', ['tab' => 'mylist']))
            ->assertOk()
            ->assertDontSee('いいね対象')
            ->assertDontSee('いいねしてない');

        // ログイン：いいねしたものだけ表示
        $this->actingAs($user)->get(route('items.index', ['tab' => 'mylist']))
            ->assertOk()
            ->assertSee('いいね対象')
            ->assertDontSee('いいねしてない');
    }

    /** @test */
    public function マイリストでも購入済み商品は_sold表示される()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();

        // 商品を作って、user が「いいね」
        $item = Item::factory()->for($seller, 'seller')->create(['title' => '売れた商品']);
        Like::query()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // その商品を別ユーザーが購入（= 売り切れ）
        $buyer = User::factory()->create();
        Purchase::query()->create([
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'payment_method' => 'convenience', // DBは 'convenience' / 'card'
            'shipping_postal_code' => '123-4567',
            'shipping_address' => '東京都千代田区1-1',
            'shipping_building' => null,
        ]);

        // ログインしてマイリストを表示 → タイトルも「Sold」バッジも見える
        $this->actingAs($user)->get(route('items.index', ['tab' => 'mylist']))
            ->assertOk()
            ->assertSee('売れた商品')
            ->assertSee('Sold'); // ← これが重要（アサーションを必ず入れる）
    }
}
