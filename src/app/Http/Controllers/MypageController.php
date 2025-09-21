<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Http\Request;

class MypageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * マイページ
     * - ?page が無ければ ?page=sell へリダイレクト（初期表示は「出品した商品」）
     * - ページネーションのクエリ名を 'p' に変更して、タブ用の 'page' と衝突しないようにする
     */
    public function index(Request $request)
    {
        // 初回アクセスで ?page が無いときは sell へ正規化
        if (! $request->has('page')) {
            return redirect()->route('mypage.index', ['page' => 'sell']);
        }

        $user = $request->user();
        $tab = $request->query('page', 'sell'); // 'sell' or 'buy'

        if ($tab === 'buy') {
            // 自分が購入した商品（購入履歴）
            $purchases = Purchase::with('item')
                ->where('buyer_id', $user->id)   // ★ 修正ポイント！
                ->orderByDesc('id')
                ->paginate(20, ['*'], 'p')       // ← ページネーションは ?p=2 のように
                ->withQueryString();             // ← ?page=buy を維持

            return view('mypage.index', [
                'user' => $user,
                'tab' => 'buy',
                'items' => null,
                'purchases' => $purchases,
            ]);
        }

        // デフォルト：自分が出品した商品
        $items = Item::where('user_id', $user->id)
            ->with('purchase')
            ->orderByDesc('id')
            ->paginate(20, ['*'], 'p')   // ← ページネーションは ?p=2 のように
            ->withQueryString();         // ← ?page=sell を維持

        return view('mypage.index', [
            'user' => $user,
            'tab' => 'sell',
            'items' => $items,
            'purchases' => null,
        ]);
    }
}
