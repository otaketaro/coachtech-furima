<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'user_id',
        'content',
    ];

    /** コメント対象の商品 */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /** コメントしたユーザー */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** 新着順で取りたいとき用 */
    public function scopeRecent($query)
    {
        return $query->orderByDesc('created_at');
    }

    // （任意）コメント追加・更新時に親Itemのupdated_atも更新したいなら↓を有効化
    // protected $touches = ['item'];
}
