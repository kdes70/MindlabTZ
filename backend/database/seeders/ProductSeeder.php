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

        $products->each(function ($product) use ($categories) {
            $count = random_int(1, min(3, $categories->count()));
            $ids = $categories->random($count)->pluck('id')->toArray();
            $product->categories()->sync($ids);
        });
    }
}
