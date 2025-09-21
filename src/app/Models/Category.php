<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /** items との多対多（中間テーブルに timestamps あり） */
    public function items()
    {
        return $this->belongsToMany(Item::class, 'category_item')->withTimestamps();
    }

    /** 名前順で並べたいとき用のスコープ（任意） */
    public function scopeOrdered($query)
    {
        return $query->orderBy('name');
    }
}
