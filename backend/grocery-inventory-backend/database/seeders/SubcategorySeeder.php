<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;

class SubcategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subcategories = [
            'Fruits & Vegetables' => ['Fresh Fruits', 'Leafy Greens', 'Root Vegetables'],
            'Dairy' => ['Milk', 'Cheese', 'Yogurt'],
            'Bakery' => ['Bread', 'Pastries', 'Cakes'],
            'Beverages' => ['Soft Drinks', 'Juices', 'Hot Beverages'],
            'Household' => ['Cleaning Supplies', 'Paper Goods', 'Laundry'],
        ];

        foreach ($subcategories as $parentName => $childNames) {
            $category = Category::query()->where('name', $parentName)->firstOrFail();

            foreach ($childNames as $childName) {
                Subcategory::query()->updateOrCreate(
                    ['category_id' => $category->id, 'name' => $childName],
                    ['description' => null, 'is_active' => true]
                );
            }
        }
    }
}
