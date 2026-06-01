<?php

use App\Models\Category;
use App\Models\Item;
use App\Models\Subcategory;
use Illuminate\Database\Eloquent\Relations\HasMany;

it('persists and exposes catalog relationships', function () {
    $category = Category::factory()->create();
    $subcategory = Subcategory::factory()->for($category)->create();
    $item = Item::factory()->create([
        'category_id' => $category->id,
        'subcategory_id' => $subcategory->id,
    ]);

    $this->assertModelExists($category);

    expect($category->subcategories())->toBeInstanceOf(HasMany::class)
        ->and($category->items())->toBeInstanceOf(HasMany::class)
        ->and($category->subcategories)->toHaveCount(1)
        ->and($category->items)->toHaveCount(1)
        ->and($category->subcategories->first()->is($subcategory))->toBeTrue()
        ->and($category->items->first()->is($item))->toBeTrue();
});
