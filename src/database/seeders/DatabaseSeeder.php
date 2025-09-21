<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            ItemSeeder::class,
            AdminSeeder::class, // ← 管理者アカウント作成Seederを追加！
            UserSeeder::class,  // 出品者・購入者・ダミー
        ]);
    }
}
