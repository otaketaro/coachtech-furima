<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable; 

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_path',
        'postal_code',
        'address',
        'building',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /** 出品した商品（出品者） */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    /** 自分が付けたコメント */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /** 自分が付けたいいね（レコード） */
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    /** いいねした商品（likes 経由の多対多） */
    public function likedItems(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'likes')->withTimestamps();
    }

    /** 自分が購入した履歴 */
    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class, 'buyer_id');
    }
}
