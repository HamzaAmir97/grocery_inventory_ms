<?php

use App\Models\Category;
use App\Models\Item;
use App\Models\Subcategory;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Database\QueryException;

it('refuses to delete a category with subcategories', function () {
    $category = Category::factory()->create();
    Subcategory::factory()->for($category)->create();

    expect(fn () => $category->delete())->toThrow(QueryException::class);
});

it('refuses to delete a category with items', function () {
    $item = Item::factory()->create();

    expect(fn () => $item->category->delete())->toThrow(QueryException::class);
});

it('refuses to delete a subcategory with items', function () {
    $item = Item::factory()->create();

    expect(fn () => $item->subcategory->delete())->toThrow(QueryException::class);
});

it('refuses to delete a unit with items', function () {
    $unit = Unit::factory()->create();
    Item::factory()->create(['unit_id' => $unit->id]);

    expect(fn () => $unit->delete())->toThrow(QueryException::class);
});

it('refuses to delete a supplier with items', function () {
    $supplier = Supplier::factory()->create();
    Item::factory()->create(['supplier_id' => $supplier->id]);

    expect(fn () => $supplier->delete())->toThrow(QueryException::class);
});
