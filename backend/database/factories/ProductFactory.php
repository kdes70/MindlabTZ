<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'article' => $this->faker->unique()->word(),
            'description' => $this->faker->sentence(),
            'specifications' => [
                // Example specifications
                'color' => $this->faker->colorName(),
                'size' => $this->faker->word(),
                'weight' => $this->faker->randomFloat(2, 0.1, 10),
            ],
            'price' => $this->faker->randomFloat(2, 1, 100),
            'quantity_in_stock' => $this->faker->numberBetween(1, 100),
        ];
    }
}
