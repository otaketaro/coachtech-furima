<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 出品者（固定）
        User::updateOrCreate(
            ['email' => 'seller1@example.com'],
            [
                'name' => '出品者1',
                'password' => Hash::make('password123'),
            ]
        );

        // 購入者（固定）
        User::updateOrCreate(
            ['email' => 'buyer1@example.com'],
            [
                'name' => '購入者1',
                'password' => Hash::make('password123'),
            ]
        );

        // ランダムダミーユーザー 10人
        User::factory(10)->create();
    }
}
