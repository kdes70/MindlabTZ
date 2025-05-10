<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Random\RandomException;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Seed the application's database.
     * @throws RandomException
     */
    public function run(): void
    {
        $counts = [0, random_int(3, 7)];

        Category::factory()
            ->has(
                Category::factory()->times($counts[array_rand($counts)])
                    ->has(
                        Category::factory()->times($counts[array_rand($counts)]),
                        'children'
                    ),
                'children'
            )
            ->create();

        Category::fixTree();
    }
}
