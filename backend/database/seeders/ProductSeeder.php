<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $products = Product::factory(20)->create();

        $categories = Category::all();

        // Привязываем для каждого продукта случайные категории
        $products->each(fn($product) => $product->categories()->attach(
            $categories->random(random_int(1, min(3, $categories->count())))->pluck('id')->all()
        ));
    }
}
