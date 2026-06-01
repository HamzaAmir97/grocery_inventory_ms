<?php

use App\Models\Item;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Relations\HasMany;

it('persists and exposes items', function () {
    $supplier = Supplier::factory()->create();
    $item = Item::factory()->create(['supplier_id' => $supplier->id]);

    $this->assertModelExists($supplier);

    expect($supplier->items())->toBeInstanceOf(HasMany::class)
        ->and($supplier->items)->toHaveCount(1)
        ->and($supplier->items->first()->is($item))->toBeTrue();
});
