<?php

use App\Models\Item;
use App\Models\Subcategory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

it('persists with a category and exposes relationships', function () {
    $subcategory = Subcategory::factory()->create();
    $item = Item::factory()->create([
        'category_id' => $subcategory->category_id,
        'subcategory_id' => $subcategory->id,
    ]);

    $this->assertModelExists($subcategory);

    expect($subcategory->category_id)->not->toBeNull()
        ->and($subcategory->category())->toBeInstanceOf(BelongsTo::class)
        ->and($subcategory->items())->toBeInstanceOf(HasMany::class)
        ->and($subcategory->category->is($subcategory->category()->first()))->toBeTrue()
        ->and($subcategory->items)->toHaveCount(1)
        ->and($subcategory->items->first()->is($item))->toBeTrue();
});
