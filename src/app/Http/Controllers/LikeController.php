<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Like;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** いいね追加 */
    public function store(Request $request, Item $item)
    {
        // 任意：自分の出品をいいね不可にしたい場合
        // if ($item->user_id === $request->user()->id) {
        //     return back(303)->with('error', '自分の出品にはいいねできません。');
        // }

        try {
            Like::firstOrCreate([
                'user_id' => $request->user()->id,
                'item_id' => $item->id,
            ]);
        } catch (QueryException $e) {
            // UNIQUE制約等の競合は無視して正常遷移
        }

        return back(303);
    }

    /** いいね解除 */
    public function destroy(Request $request, Item $item)
    {
        Like::where('user_id', $request->user()->id)
            ->where('item_id', $item->id)
            ->delete();

        return back(303);
    }
}
