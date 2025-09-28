<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use Illuminate\Support\Facades\Route;

// ===== 公開（未ログインOK） =====
// PG01/PG02: 商品一覧（/?tab=mylist も同じindexで処理）
Route::get('/', [ItemController::class, 'index'])->name('items.index');

// PG05: 商品詳細
Route::get('/item/{item}',   [ItemController::class, 'show'])->name('items.show');

// ===== ログイン必須 =====
Route::middleware('auth')->group(function () {

    // いいね
    Route::post('/item/{item}/like', [LikeController::class, 'store'])->name('likes.store');
    Route::delete('/item/{item}/like', [LikeController::class, 'destroy'])->name('likes.destroy');

    // コメント投稿
    Route::post('/item/{item}/comments', [CommentController::class, 'store'])->name('comments.store');

    // PG09/PG11/PG12: マイページ（?page=buy / ?page=sell で切替）
    Route::get('/mypage', [MypageController::class, 'index'])->name('mypage.index');

    // PG10: プロフィール編集
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('mypage.profile.edit');
    Route::put('/mypage/profile', [ProfileController::class, 'update'])->name('mypage.profile.update');

    // PG08: 出品（画面表示）
    Route::get('/sell', [ItemController::class, 'create'])->name('items.create');

    // ✅ 正規: 要件・テストに合わせて /items へ POST
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');

    // 🧯 互換: 既存フォームが /sell に POST していても壊さない暫定受け口（任意）
    Route::post('/sell', [ItemController::class, 'store'])->name('items.store_legacy');

    // PG06: 購入手続き
    Route::get('/purchase/{item}', [PurchaseController::class, 'create'])->name('purchase.create');
    Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->name('purchase.store');

    // PG07: 送付先住所変更
    Route::get('/purchase/address/{item}', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
    Route::put('/purchase/address/{item}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');
});
