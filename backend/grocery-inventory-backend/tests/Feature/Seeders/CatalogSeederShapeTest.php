<?php

use App\Models\Category;
use App\Models\Item;
use App\Models\Subcategory;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Support\Facades\Artisan;

it('seeds the documented catalog shape', function () {
    Artisan::call('migrate:fresh', ['--seed' => true]);

    expect(Category::query()->count())->toBe(5)
        ->and(Subcategory::query()->count())->toBe(15)
        ->and(Unit::query()->count())->toBe(4)
        ->and(Supplier::query()->count())->toBe(3)
        ->and(Item::query()->count())->toBe(20);

    expect(Category::query()->pluck('name')->all())->toEqualCanonicalizing([
        'Fruits & Vegetables',
        'Dairy',
        'Bakery',
        'Beverages',
        'Household',
    ]);

    Category::query()->withCount('subcategories')->get()->each(function (Category $category): void {
        expect($category->subcategories_count)->toBeGreaterThanOrEqual(1);
    });

    $units = Unit::query()->get(['name', 'symbol'])
        ->map(fn (Unit $unit): array => ['name' => $unit->name, 'symbol' => $unit->symbol])
        ->all();

    expect($units)->toEqualCanonicalizing([
        ['name' => 'Kilogram', 'symbol' => 'kg'],
        ['name' => 'Liter', 'symbol' => 'L'],
        ['name' => 'Piece', 'symbol' => 'pcs'],
        ['name' => 'Pack', 'symbol' => 'pack'],
    ]);

    expect(Supplier::query()->pluck('name')->all())->toEqualCanonicalizing([
        'Fresh Farm Supplies',
        'Daily Dairy Co',
        'City Grocery Wholesale',
    ]);

    expect(Item::query()->lowStock()->count())->toBeGreaterThanOrEqual(2)
        ->and(Item::query()->distinct('category_id')->count('category_id'))->toBeGreaterThanOrEqual(4)
        ->and(Item::query()->distinct('subcategory_id')->count('subcategory_id'))->toBeGreaterThanOrEqual(8)
        ->and(Item::query()->distinct('unit_id')->count('unit_id'))->toBe(4)
        ->and(Item::query()->distinct('supplier_id')->count('supplier_id'))->toBe(3)
        ->and(Item::query()->pluck('price')->unique()->count())->toBeGreaterThan(1)
        ->and(Item::query()->pluck('stock_quantity')->unique()->count())->toBeGreaterThan(1);

    Item::query()->with('subcategory')->get()->each(function (Item $item): void {
        expect($item->subcategory->category_id)->toBe($item->category_id);
    });
});
