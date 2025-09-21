<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseFactory extends Factory
{
    protected $model = Purchase::class;

    public function definition(): array
    {
        return [
            'item_id' => Item::factory(),
            'buyer_id' => User::factory(),
            'price' => $this->faker->numberBetween(500, 50000), // 価格（unsigned int）
            'payment_method' => $this->faker->randomElement(['convenience_store', 'card']),
            'status' => $this->faker->randomElement(['trading', 'completed']),
            'shipping_postal_code' => '123-4567',
            'shipping_address' => '東京都千代田区1-1-1',
            'shipping_building' => 'テストビル',
        ];
    }
}
