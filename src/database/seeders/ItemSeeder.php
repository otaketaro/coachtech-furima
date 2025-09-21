<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        // 出品者（なければ1人作成）
        $seller = User::first() ?? User::factory()->create([
            'name' => 'seller1',
            'email' => 'seller1@example.com',
        ]);

        // 教材の「商品データ一覧」 → condition をコード値に統一
        $items = [
            [
                'title' => '腕時計',
                'price' => 15000,
                'brand' => 'Rolax',
                'desc' => 'スタイリッシュなデザインのメンズ腕時計',
                'img' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
                'cond' => 'good',       // 良好
            ],
            [
                'title' => 'HDD',
                'price' => 5000,
                'brand' => '西芝',
                'desc' => '高速で信頼性の高いハードディスク',
                'img' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg',
                'cond' => 'good',       // 目立った傷や汚れなし → good
            ],
            [
                'title' => '玉ねぎ3束',
                'price' => 300,
                'brand' => null,
                'desc' => '新鮮な玉ねぎ3束のセット',
                'img' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg',
                'cond' => 'fair',       // やや傷や汚れあり
            ],
            [
                'title' => '革靴',
                'price' => 4000,
                'brand' => null,
                'desc' => 'クラシックなデザインの革靴',
                'img' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg',
                'cond' => 'poor',       // 状態が悪い
            ],
            [
                'title' => 'ノートPC',
                'price' => 45000,
                'brand' => null,
                'desc' => '高性能なノートパソコン',
                'img' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg',
                'cond' => 'good',       // 良好 → good
            ],
            [
                'title' => 'マイク',
                'price' => 8000,
                'brand' => null,
                'desc' => '高音質のレコーディング用マイク',
                'img' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg',
                'cond' => 'good',       // 目立った傷や汚れなし → good
            ],
            [
                'title' => 'ショルダーバッグ',
                'price' => 3500,
                'brand' => null,
                'desc' => 'おしゃれなショルダーバッグ',
                'img' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg',
                'cond' => 'fair',       // やや傷や汚れあり
            ],
            [
                'title' => 'タンブラー',
                'price' => 500,
                'brand' => null,
                'desc' => '使いやすいタンブラー',
                'img' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg',
                'cond' => 'poor',       // 状態が悪い
            ],
            [
                'title' => 'コーヒーミル',
                'price' => 4000,
                'brand' => 'Starbacks',
                'desc' => '手動のコーヒーミル',
                'img' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
                'cond' => 'good',       // 良好 → good
            ],
            [
                'title' => 'メイクセット',
                'price' => 2500,
                'brand' => null,
                'desc' => '便利なメイクアップセット',
                'img' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
                'cond' => 'good',       // 目立った傷や汚れなし → good
            ],
        ];

        // 便宜的に全商品へ「その他」カテゴリを付与
        $defaultCategory = Category::firstOrCreate(['name' => 'その他']);

        foreach ($items as $i) {
            $item = Item::create([
                'user_id' => $seller->id,
                'title' => $i['title'],
                'brand' => $i['brand'],
                'description' => $i['desc'],
                'price' => $i['price'],
                'condition' => $i['cond'],            // ← コード値
                'status' => Item::STATUS_SELLING,  // ← 定数を使用
                'image_path' => $i['img'],             // ダミーはURLのまま保存
            ]);

            $item->categories()->syncWithoutDetaching([$defaultCategory->id]);
        }
    }
}
