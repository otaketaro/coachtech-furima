<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'buyer_id',
        'price',
        'payment_method',
        'status',
        'shipping_postal_code',
        'shipping_address',
        'shipping_building',
    ];

    // ★ ここがポイント：テストで未指定でも通るよう既定値
    protected $attributes = [
        'price' => 0,
        'status' => 'completed',
    ];

    protected $casts = [
        'price' => 'integer',
    ];

    // （任意）アプリ内の表現として使うなら
    public const PAYMENT_CONVENIENCE_STORE = 'convenience_store';

    public const PAYMENT_CARD = 'card';

    public const PAYMENT_METHODS = [
        self::PAYMENT_CONVENIENCE_STORE,
        self::PAYMENT_CARD,
    ];

    /** 購入された商品（1商品=1購入のユニーク制約） */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /** 購入者 */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /** 購入者で絞り込み（任意） */
    public function scopeOfBuyer($query, int $userId)
    {
        return $query->where('buyer_id', $userId);
    }

    /** 新しい順（任意） */
    public function scopeRecent($query)
    {
        return $query->orderByDesc('created_at');
    }
}
