<?php

use App\Models\Category;
use App\Models\Item;
use App\Models\Subcategory;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Database\QueryException;

it('requires category names to be unique', function () {
    Category::factory()->create(['name' => 'Produce']);

    expect(fn () => Category::factory()->create(['name' => 'Produce']))->toThrow(QueryException::class);
});

it('requires subcategory names to be unique within a category', function () {
    $category = Category::factory()->create();
    Subcategory::factory()->for($category)->create(['name' => 'Leafy Greens']);

    expect(fn () => Subcategory::factory()->for($category)->create(['name' => 'Leafy Greens']))->toThrow(QueryException::class);
});

it('allows the same subcategory name under different categories', function () {
    Subcategory::factory()->for(Category::factory())->create(['name' => 'Seasonal']);
    $subcategory = Subcategory::factory()->for(Category::factory())->create(['name' => 'Seasonal']);

    $this->assertModelExists($subcategory);
});

it('requires unit names to be unique', function () {
    Unit::factory()->create(['name' => 'Kilogram']);

    expect(fn () => Unit::factory()->create(['name' => 'Kilogram']))->toThrow(QueryException::class);
});

it('requires unit symbols to be unique', function () {
    Unit::factory()->create(['symbol' => 'kg']);

    expect(fn () => Unit::factory()->create(['symbol' => 'kg']))->toThrow(QueryException::class);
});

it('requires supplier names to be unique', function () {
    Supplier::factory()->create(['name' => 'Acme Foods']);

    expect(fn () => Supplier::factory()->create(['name' => 'Acme Foods']))->toThrow(QueryException::class);
});

it('requires item sku values to be unique when set', function () {
    Item::factory()->create(['sku' => 'SKU-001']);

    expect(fn () => Item::factory()->create(['sku' => 'SKU-001']))->toThrow(QueryException::class);
});

it('allows multiple items without sku values', function () {
    Item::factory()->create(['sku' => null]);
    $item = Item::factory()->create(['sku' => null]);

    $this->assertModelExists($item);
});
