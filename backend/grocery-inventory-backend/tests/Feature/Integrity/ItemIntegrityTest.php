<?php

use App\Exceptions\SubcategoryMismatchException;
use App\Models\Category;
use App\Models\Item;
use App\Models\Subcategory;
use Illuminate\Database\QueryException;

it('refuses negative prices', function () {
    expect(fn () => Item::factory()->create(['price' => -0.01]))->toThrow(QueryException::class);
});

it('refuses negative stock quantities', function () {
    expect(fn () => Item::factory()->create(['stock_quantity' => -1]))->toThrow(QueryException::class);
});

it('refuses negative low stock thresholds', function () {
    expect(fn () => Item::factory()->create(['low_stock_threshold' => -1]))->toThrow(QueryException::class);
});

it('refuses subcategories from a different category', function () {
    $category = Category::factory()->create();
    $otherCategory = Category::factory()->create();
    $subcategory = Subcategory::factory()->for($otherCategory)->create();

    expect(fn () => Item::factory()->create([
        'category_id' => $category->id,
        'subcategory_id' => $subcategory->id,
    ]))->toThrow(SubcategoryMismatchException::class);
});

it('accepts subcategories that belong to the selected category', function () {
    $category = Category::factory()->create();
    $subcategory = Subcategory::factory()->for($category)->create();
    $item = Item::factory()->create([
        'category_id' => $category->id,
        'subcategory_id' => $subcategory->id,
    ]);

    $this->assertModelExists($item);
});
