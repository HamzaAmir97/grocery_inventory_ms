<?php

use App\Models\Category;
use App\Models\Item;
use App\Models\Subcategory;
use App\Models\Supplier;
use App\Models\Unit;

it('soft-deletes a category with subcategories', function () {
    $category = Category::factory()->create();
    $subcategory = Subcategory::factory()->for($category)->create();

    $category->delete();

    expect($category->fresh()->trashed())->toBeTrue()
        ->and($subcategory->fresh()->trashed())->toBeFalse();
});

it('soft-deletes a category with items', function () {
    $item = Item::factory()->create();
    $category = $item->category;

    $category->delete();

    expect($category->fresh()->trashed())->toBeTrue()
        ->and($item->fresh()->trashed())->toBeFalse();
});

it('soft-deletes a subcategory with items', function () {
    $item = Item::factory()->create();
    $subcategory = $item->subcategory;

    $subcategory->delete();

    expect($subcategory->fresh()->trashed())->toBeTrue()
        ->and($item->fresh()->trashed())->toBeFalse();
});

it('soft-deletes a unit with items', function () {
    $unit = Unit::factory()->create();
    $item = Item::factory()->create(['unit_id' => $unit->id]);

    $unit->delete();

    expect($unit->fresh()->trashed())->toBeTrue()
        ->and($item->fresh()->trashed())->toBeFalse();
});

it('soft-deletes a supplier with items', function () {
    $supplier = Supplier::factory()->create();
    $item = Item::factory()->create(['supplier_id' => $supplier->id]);

    $supplier->delete();

    expect($supplier->fresh()->trashed())->toBeTrue()
        ->and($item->fresh()->trashed())->toBeFalse();
});
