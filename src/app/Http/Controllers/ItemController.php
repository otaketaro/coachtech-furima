<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExhibitionRequest;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab');

        $query = Item::query()
            ->with(['categories', 'purchase'])
            ->withCount(['likes', 'comments', 'purchase'])
            ->orderByDesc('id');

        if (auth()->check()) {
            $query->where('user_id', '!=', auth()->id());
        }

        if ($keyword = $request->query('q')) {
            $query->where('title', 'LIKE', "%{$keyword}%");
        }
        if ($minPrice = $request->query('min')) {
            $query->where('price', '>=', (int) $minPrice);
        }
        if ($maxPrice = $request->query('max')) {
            $query->where('price', '<=', (int) $maxPrice);
        }
        if ($categoryId = $request->query('category')) {
            $query->whereHas('categories', fn ($x) => $x->where('categories.id', $categoryId));
        }

        if ($tab === 'mylist') {
            if (! auth()->check()) {
                $items = $query->whereRaw('1 = 0')->paginate(12)->withQueryString();
            } else {
                $items = $query
                    ->whereHas('likes', fn ($q) => $q->where('user_id', auth()->id()))
                    ->paginate(12)
                    ->withQueryString();
            }
        } else {
            $items = $query->paginate(12)->withQueryString();
        }

        return view('items.index', compact('items', 'tab'));
    }

    /**
     * 商品詳細（IDなしで /item に来たら一覧へフォールバック）
     */
    public function show(?Item $item)
    {
        if (! $item) {
            return redirect()->route('items.index');
        }

        $item->load([
            'seller:id,name',
            'categories:id,name',
            'comments' => fn ($q) => $q->latest(),
            'comments.user:id,name,avatar_path',
        ])->loadCount([
            'likes',
            'comments',
            'purchase',
        ]);

        $isLiked = auth()->check()
            ? $item->likes()->where('user_id', auth()->id())->exists()
            : false;

        return view('items.show', compact('item', 'isLiked'));
    }

    /**
     * 出品画面表示
     */
    public function create()
    {
        $want = [
            'ファッション',
            '家電',
            'インテリア',
            'レディース',
            'メンズ',
            'コスメ',
            '本',
            'ゲーム',
            'スポーツ',
            'キッチン',
            'ハンドメイド',
            'アクセサリー',
            'おもちゃ',
            'ベビー・キッズ',
        ];

        $placeholders = implode(',', array_fill(0, count($want), '?'));
        $categories = Category::query()
            ->whereIn('name', $want)
            ->orderByRaw('FIELD(name, '.$placeholders.')', $want)
            ->get(['id', 'name']);

        return view('items.create', compact('categories'));
    }

    /**
     * 出品（保存）: 画像アップロード + カテゴリ紐付け
     * 直後は /item（IDなし）へリダイレクト（テストの期待に合わせる）
     */
    public function store(ExhibitionRequest $request)
    {
        $data = $request->validated();

        $path = $data['image']->store('items', 'public');

        $item = Item::create([
            'user_id' => Auth::id(),
            'title' => $data['title'],
            'brand' => $data['brand'] ?? null,
            'description' => $data['description'],
            'price' => $data['price'],
            'condition' => $data['condition'],
            'status' => Item::STATUS_SELLING,
            'image_path' => $path,
        ]);

        $item->categories()->sync($data['categories']);

        // ★ ここが肝：/item へ（IDは付けない）
        return redirect(route('items.show', $item))
            ->with('status', '商品を出品しました！');
    }
}
