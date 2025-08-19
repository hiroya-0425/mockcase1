<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Item;
use App\Models\User;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->word,
            'brand' => $this->faker->company,
            'price' => $this->faker->numberBetween(100, 10000),
            'image' => 'items/sample.jpg',
            'description' => $this->faker->sentence,
            'condition' => '新品',
            'is_sold' => false,
        ];
    }


    public function sold()
    {
        return $this->state(fn() => ['is_sold' => true]);
    }
}
