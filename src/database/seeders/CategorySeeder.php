<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'ファッション',
            '家電',
            '食品',
            '家具',
            '雑貨',
            'コスメ',
            'PC周辺機器',
            'その他',
        ];
        foreach ($names as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}
