<?php

use App\Models\Item;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

it('persists with all parent relationships and casts', function () {
    $item = Item::factory()->create([
        'price' => 12.5,
        'stock_quantity' => '4',
        'low_stock_threshold' => '10',
        'is_active' => 1,
    ]);

    $this->assertModelExists($item);

    expect($item->category_id)->not->toBeNull()
        ->and($item->subcategory_id)->not->toBeNull()
        ->and($item->unit_id)->not->toBeNull()
        ->and($item->supplier_id)->not->toBeNull()
        ->and($item->category())->toBeInstanceOf(BelongsTo::class)
        ->and($item->subcategory())->toBeInstanceOf(BelongsTo::class)
        ->and($item->unit())->toBeInstanceOf(BelongsTo::class)
        ->and($item->supplier())->toBeInstanceOf(BelongsTo::class)
        ->and($item->category->is($item->category()->first()))->toBeTrue()
        ->and($item->subcategory->is($item->subcategory()->first()))->toBeTrue()
        ->and($item->unit->is($item->unit()->first()))->toBeTrue()
        ->and($item->supplier->is($item->supplier()->first()))->toBeTrue()
        ->and($item->is_active)->toBeBool()
        ->and($item->price)->toBe('12.50')
        ->and($item->getCasts()['price'])->toBe('decimal:2');
});

it('finds low stock items', function () {
    Item::factory()->create(['stock_quantity' => 20, 'low_stock_threshold' => 5]);
    $lowStockItem = Item::factory()->lowStock()->create();

    expect(Item::lowStock()->get())->toHaveCount(1)
        ->and(Item::lowStock()->first()->is($lowStockItem))->toBeTrue();
});
