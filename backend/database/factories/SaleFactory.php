<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sale>
 */
class SaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'client_id' => Client::factory(),
            'quantity' => $this->faker->numberBetween(1, 10),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'sale_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            // 'sale_date' => $this->faker->dateTime(),
        ];
    }
}
