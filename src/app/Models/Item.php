<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    // ステータス
    public const STATUS_SELLING = 'selling';

    public const STATUS_SOLD = 'sold';

    protected $fillable = [
        'user_id',
        'title',
        'brand',
        'description',
        'price',
        'condition',
        'status',
        'image_path',
    ];

    // （任意）価格は整数として扱うと便利
    protected $casts = [
        'price' => 'integer',
    ];

    // ===== リレーション =====
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_item');
    }

    // ===== 商品状態ラベル（日本語対応） =====
    public const CONDITION_LABELS = [
        'new' => '新品・未使用',
        'like_new' => '未使用に近い',
        'good' => '目立った傷や汚れなし',
        'fair' => 'やや傷や汚れあり',
        'poor' => '全体的に状態が悪い',
    ];

    public function getConditionLabelAttribute()
    {
        return self::CONDITION_LABELS[$this->condition] ?? $this->condition;
    }

    // ===== 売却判定（SOLDバッジ用） =====
    // ・status が 'sold' なら true
    // ・関連の purchase が存在すれば true
    public function getIsSoldAttribute(): bool
    {
        return $this->status === self::STATUS_SOLD || $this->purchase()->exists();
    }

    // （任意）出品中だけ取得したいときに使えるスコープ
    public function scopeSelling($query)
    {
        return $query->where('status', self::STATUS_SELLING);
    }
}
