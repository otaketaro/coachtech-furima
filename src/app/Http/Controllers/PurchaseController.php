<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 購入手続き画面（PG06）
     */
    public function create(Item $item)
    {
        // 自分の出品は不可
        if ($item->user_id === auth()->id()) {
            return redirect()
                ->route('items.show', $item)
                ->with('error', '自分の出品は購入できません。');
        }

        // 売り切れは不可
        if ($item->purchase()->exists()) {
            return redirect()
                ->route('items.show', $item)
                ->with('error', 'この商品は購入済みです。');
        }

        // 関連
        $item->loadMissing(['seller:id,name', 'categories:id,name']);

        // 住所の初期値（セッション > プロフィール）
        $sessionKey = "purchase.{$item->id}.shipping";
        $sess = session($sessionKey, []);
        $user = auth()->user();

        $prefill = [
            'shipping_postal_code' => $sess['shipping_postal_code'] ?? ($user->postal_code ?? ''),
            'shipping_address' => $sess['shipping_address'] ?? ($user->address ?? ''),
            'shipping_building' => $sess['shipping_building'] ?? ($user->building ?? ''),
        ];

        return view('purchase.create', compact('item', 'prefill'));
    }

    /**
     * 送付先住所変更（PG07）：表示
     */
    public function editAddress(Item $item)
    {
        if ($item->user_id === auth()->id() || $item->purchase()->exists()) {
            return redirect()->route('purchase.create', $item)
                ->with('error', 'この商品の配送先は変更できません。');
        }

        $sessionKey = "purchase.{$item->id}.shipping";
        $sess = session($sessionKey, []);
        $user = auth()->user();

        $form = [
            'shipping_postal_code' => old('shipping_postal_code', $sess['shipping_postal_code'] ?? ($user->postal_code ?? '')),
            'shipping_address' => old('shipping_address', $sess['shipping_address'] ?? ($user->address ?? '')),
            'shipping_building' => old('shipping_building', $sess['shipping_building'] ?? ($user->building ?? '')),
        ];

        return view('purchase.address', compact('item', 'form'));
    }

    /**
     * 送付先住所変更（PG07）：更新（セッションへ保存）
     */
    public function updateAddress(Request $request, Item $item)
    {
        $data = $request->validate(
            [
                'shipping_postal_code' => ['required', 'regex:/^\d{3}-?\d{4}$/'],
                'shipping_address' => ['required', 'max:255'],
                'shipping_building' => ['nullable', 'max:255'],
            ],
            [
                'shipping_postal_code.regex' => '郵便番号は「123-4567」形式で入力してください',
            ],
            [
                'shipping_postal_code' => '郵便番号',
                'shipping_address' => '住所',
                'shipping_building' => '建物名',
            ]
        );

        // 1234567 → 123-4567 に整形
        $digits = preg_replace('/\D+/', '', $data['shipping_postal_code']);
        $data['shipping_postal_code'] = substr($digits, 0, 3).'-'.substr($digits, 3, 4);

        session(["purchase.{$item->id}.shipping" => $data]);

        return redirect()
            ->route('purchase.create', $item)
            ->with('status', '送付先住所を更新しました。');
    }

    /**
     * 購入確定（PG06 POST）
     */
    public function store(StorePurchaseRequest $request, Item $item)
    {
        $user = $request->user();

        // 自分の出品は不可
        if ($item->user_id === $user->id) {
            return redirect()->route('items.show', $item)
                ->with('error', '自分の出品は購入できません。');
        }

        // ★ 売り切れは errors に入れて 302 返す（CannotDoublePurchaseTest 的に必須）
        if ($item->purchase()->exists()) {
            return back()
                ->withErrors(['purchase' => '既に購入手続きが完了しています。'])
                ->withInput();
        }

        $data = $request->validated();

        // ★ 受け取り値のゆらぎを吸収（UI/テスト両対応）
        //   - UI: 'convenience' / 'credit'
        //   - テスト: 'convenience_store' / 'card'
        //   - DB: 'convenience' / 'card'
        $pmIn = $data['payment_method'];
        $payment = match ($pmIn) {
            'convenience_store', 'convenience' => 'convenience',
            'credit', 'card' => 'card',
            default => $pmIn, // 異常値はそのまま（バリデーションで弾かれる想定）
        };

        try {
            DB::transaction(function () use ($item, $user, $data, $payment) {
                Purchase::create([
                    'item_id' => $item->id,
                    'buyer_id' => $user->id,
                    'price' => $item->price,  // 確定価格
                    'payment_method' => $payment,
                    'status' => 'completed',
                    'shipping_postal_code' => $data['shipping_postal_code'],
                    'shipping_address' => $data['shipping_address'],
                    'shipping_building' => $data['shipping_building'] ?? null,
                ]);

                // 一覧の見え方を安定させるため status も SOLD に（purchase だけでも Sold 表示は出ますが保険）
                if (defined(Item::class.'::STATUS_SOLD')) {
                    $item->update(['status' => Item::STATUS_SOLD]);
                }
            });
        } catch (QueryException $e) {
            // ユニーク制約違反（race）など → errors に積む
            if ($e->getCode() === '23000') {
                return back()
                    ->withErrors(['purchase' => '既に購入手続きが完了しています。'])
                    ->withInput();
            }

            // それ以外はログ＋汎用エラー
            Log::error('purchase.store failed', [
                'item_id' => $item->id,
                'buyer_id' => $user->id,
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'purchase' => '購入処理でエラーが発生しました。時間をおいて再度お試しください。',
            ])->withInput();
        }

        // セッション掃除
        session()->forget("purchase.{$item->id}.shipping");

        return redirect()
            ->route('items.show', $item)
            ->with('status', '購入が完了しました！');
    }
}
