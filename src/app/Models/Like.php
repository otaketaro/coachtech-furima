<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Like extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'user_id',
    ];

    /** いいね対象の商品 */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /** いいねしたユーザー */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** ユーザーで絞り込み（任意） */
    public function scopeOfUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /** 商品で絞り込み（任意） */
    public function scopeOfItem($query, int $itemId)
    {
        return $query->where('item_id', $itemId);
    }
}
