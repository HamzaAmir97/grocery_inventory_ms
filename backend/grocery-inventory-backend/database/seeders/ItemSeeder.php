<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::query()->with('subcategories')->orderBy('id')->get()->values();
        $units = Unit::query()->orderBy('id')->get()->values();
        $suppliers = Supplier::query()->orderBy('id')->get()->values();

        $seedRows = [
            ['name' => 'Organic eggs 12pk', 'sku' => 'EGG-ORG-12', 'category' => 'Dairy', 'subcategory' => 'Cheese', 'unit' => 'Pack', 'supplier' => 'Daily Dairy Co', 'price' => 4.20, 'stock' => 24, 'threshold' => 6],
            ['name' => 'Basmati rice 5kg', 'sku' => 'RICE-BAS-5KG', 'category' => 'Bakery', 'subcategory' => 'Bread', 'unit' => 'Kilogram', 'supplier' => 'Fresh Farm Supplies', 'price' => 12.00, 'stock' => 4, 'threshold' => 10],
            ['name' => 'Olive oil 1L', 'sku' => 'OIL-OL-1L', 'category' => 'Household', 'subcategory' => 'Cleaning Supplies', 'unit' => 'Liter', 'supplier' => 'City Grocery Wholesale', 'price' => 8.50, 'stock' => 7, 'threshold' => 20],
            ['name' => 'Sourdough loaf', 'sku' => 'BRD-SRD-1', 'category' => 'Bakery', 'subcategory' => 'Bread', 'unit' => 'Piece', 'supplier' => 'Fresh Farm Supplies', 'price' => 5.40, 'stock' => 18, 'threshold' => 5],
            ['name' => 'Tomato sauce 400g', 'sku' => 'CAN-TOM-400', 'category' => 'Household', 'subcategory' => 'Paper Goods', 'unit' => 'Piece', 'supplier' => 'City Grocery Wholesale', 'price' => 2.10, 'stock' => 0, 'threshold' => 12],
            ['name' => 'Whole milk 1L', 'sku' => 'DRY-MLK-1L', 'category' => 'Dairy', 'subcategory' => 'Milk', 'unit' => 'Liter', 'supplier' => 'Daily Dairy Co', 'price' => 1.85, 'stock' => 5, 'threshold' => 15],
            ['name' => 'Greek yogurt cups', 'sku' => 'YGT-GRK-6', 'category' => 'Dairy', 'subcategory' => 'Yogurt', 'unit' => 'Pack', 'supplier' => 'Daily Dairy Co', 'price' => 6.80, 'stock' => 16, 'threshold' => 6],
            ['name' => 'Cheddar block', 'sku' => 'CHS-CHED-500', 'category' => 'Dairy', 'subcategory' => 'Cheese', 'unit' => 'Piece', 'supplier' => 'Daily Dairy Co', 'price' => 7.25, 'stock' => 9, 'threshold' => 4],
            ['name' => 'Orange juice', 'sku' => 'JUI-ORG-1L', 'category' => 'Beverages', 'subcategory' => 'Juices', 'unit' => 'Liter', 'supplier' => 'City Grocery Wholesale', 'price' => 3.90, 'stock' => 14, 'threshold' => 6],
            ['name' => 'Sparkling water pack', 'sku' => 'BEV-SPK-6', 'category' => 'Beverages', 'subcategory' => 'Soft Drinks', 'unit' => 'Pack', 'supplier' => 'City Grocery Wholesale', 'price' => 5.10, 'stock' => 22, 'threshold' => 8],
            ['name' => 'Ground coffee', 'sku' => 'COF-GRD-500', 'category' => 'Beverages', 'subcategory' => 'Hot Beverages', 'unit' => 'Piece', 'supplier' => 'City Grocery Wholesale', 'price' => 10.50, 'stock' => 12, 'threshold' => 5],
            ['name' => 'Bananas', 'sku' => 'FRU-BAN-1KG', 'category' => 'Fruits & Vegetables', 'subcategory' => 'Fresh Fruits', 'unit' => 'Kilogram', 'supplier' => 'Fresh Farm Supplies', 'price' => 1.95, 'stock' => 28, 'threshold' => 7],
            ['name' => 'Spinach bunch', 'sku' => 'GRN-SPN-1', 'category' => 'Fruits & Vegetables', 'subcategory' => 'Leafy Greens', 'unit' => 'Piece', 'supplier' => 'Fresh Farm Supplies', 'price' => 1.35, 'stock' => 11, 'threshold' => 4],
            ['name' => 'Sweet potatoes', 'sku' => 'VEG-SWT-1KG', 'category' => 'Fruits & Vegetables', 'subcategory' => 'Root Vegetables', 'unit' => 'Kilogram', 'supplier' => 'Fresh Farm Supplies', 'price' => 2.60, 'stock' => 19, 'threshold' => 6],
            ['name' => 'Blueberry muffins', 'sku' => 'BAK-MUF-4', 'category' => 'Bakery', 'subcategory' => 'Pastries', 'unit' => 'Pack', 'supplier' => 'Fresh Farm Supplies', 'price' => 4.75, 'stock' => 10, 'threshold' => 4],
            ['name' => 'Celebration cake', 'sku' => 'BAK-CAK-1', 'category' => 'Bakery', 'subcategory' => 'Cakes', 'unit' => 'Piece', 'supplier' => 'Fresh Farm Supplies', 'price' => 22.00, 'stock' => 3, 'threshold' => 2],
            ['name' => 'Dish soap', 'sku' => 'HOU-DSH-750', 'category' => 'Household', 'subcategory' => 'Cleaning Supplies', 'unit' => 'Liter', 'supplier' => 'City Grocery Wholesale', 'price' => 3.40, 'stock' => 13, 'threshold' => 5],
            ['name' => 'Paper towels', 'sku' => 'HOU-PPR-6', 'category' => 'Household', 'subcategory' => 'Paper Goods', 'unit' => 'Pack', 'supplier' => 'City Grocery Wholesale', 'price' => 6.20, 'stock' => 8, 'threshold' => 4],
            ['name' => 'Laundry detergent', 'sku' => 'HOU-LND-2L', 'category' => 'Household', 'subcategory' => 'Laundry', 'unit' => 'Liter', 'supplier' => 'City Grocery Wholesale', 'price' => 9.80, 'stock' => 6, 'threshold' => 4],
            ['name' => 'Apple juice', 'sku' => 'JUI-APL-1L', 'category' => 'Beverages', 'subcategory' => 'Juices', 'unit' => 'Liter', 'supplier' => 'City Grocery Wholesale', 'price' => 3.50, 'stock' => 17, 'threshold' => 5],
        ];

        foreach ($seedRows as $index => $row) {
            $category = $categories->firstWhere('name', $row['category']);
            $subcategory = $category?->subcategories->firstWhere('name', $row['subcategory']);
            $unit = $units->firstWhere('name', $row['unit']);
            $supplier = $suppliers->firstWhere('name', $row['supplier']);
            Item::query()->updateOrCreate(
                ['sku' => $row['sku']],
                [
                    'name' => $row['name'],
                    'category_id' => $category?->id,
                    'subcategory_id' => $subcategory?->id,
                    'unit_id' => $unit?->id,
                    'supplier_id' => $supplier?->id,
                    'price' => $row['price'],
                    'stock_quantity' => $row['stock'],
                    'low_stock_threshold' => $row['threshold'],
                    'is_active' => true,
                ]
            );
        }
    }
}
