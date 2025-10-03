<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->words(2, true),
            'brand' => $this->faker->optional()->word(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->numberBetween(300, 50000),
            'condition' => 'good',
            'status' => Item::STATUS_SELLING,
            'image_path' => 'items/dummy.jpg',
        ];
    }
}
