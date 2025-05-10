<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SaleSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $products = Product::all();

        Sale::factory(20)->create()->each(fn(Sale $sale) => $sale->update([
            'product_id' => $products->random()->id,
            'client_id'  => Client::factory()->create()->id,
        ]));
    }
}
