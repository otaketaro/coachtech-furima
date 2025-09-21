<?php

namespace Tests\Feature\Items;

use App\Models\Item;
use App\Models\Like;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchByTitleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 商品名の部分一致でヒットし非一致は表示されない()
    {
        Item::factory()->create(['title' => 'Blue Camera']);
        Item::factory()->create(['title' => 'Red Phone']);

        // q=Cam で部分一致
        $res = $this->get(route('items.index', ['q' => 'Cam']));

        $res->assertOk()
            ->assertSee('Blue Camera')
            ->assertDontSee('Red Phone');
    }

    /** @test */
    public function 検索状態はタブ遷移でも保持されマイリストにも反映される()
    {
        $user = User::factory()->create();

        $liked = Item::factory()->create(['title' => 'My Liked Camera']);
        $unliked = Item::factory()->create(['title' => 'Other Camera']);

        Like::create(['user_id' => $user->id, 'item_id' => $liked->id]);

        // 一般タブで検索 → マイリストタブのリンクに q が付いていることを確認
        $resList = $this->get(route('items.index', ['q' => 'Camera']));
        $resList->assertOk()
            // aタグのhref内に ?tab=mylist&q=Camera が含まれる（エスケープを考慮して &amp; になることもある）
            ->assertSee('tab=mylist')
            ->assertSee('q=Camera');

        // マイリスト側でも同じ q で絞り込まれる（今回は直接パラメータを付けて検証）
        $resMylist = $this->actingAs($user)->get(route('items.index', ['tab' => 'mylist', 'q' => 'Camera']));
        $resMylist->assertOk()
            ->assertSee('My Liked Camera')  // いいね済み & 部分一致 → 表示
            ->assertDontSee('Other Camera'); // いいねしてない → 表示されない
    }
}
