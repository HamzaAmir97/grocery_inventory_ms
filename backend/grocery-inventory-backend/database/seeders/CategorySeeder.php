<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Fruits & Vegetables',
                'description' => 'Fresh produce, fruits and vegetables sold by weight or piece.',
            ],
            [
                'name' => 'Dairy',
                'description' => 'Milk, cheese, yogurt and other refrigerated dairy products.',
            ],
            [
                'name' => 'Bakery',
                'description' => 'Breads, pastries and cakes baked on-site or daily-delivered.',
            ],
            [
                'name' => 'Beverages',
                'description' => 'Soft drinks, juices, hot beverages and bottled drinks.',
            ],
            [
                'name' => 'Household',
                'description' => 'Cleaning supplies, paper goods and laundry products.',
            ],
        ];

        foreach ($categories as $category) {
            Category::query()->updateOrCreate(
                ['name' => $category['name']],
                $category + ['is_active' => true]
            );
        }
    }
}
