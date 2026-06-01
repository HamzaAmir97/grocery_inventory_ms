<?php

use App\Models\Item;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Relations\HasMany;

it('persists and exposes items', function () {
    $unit = Unit::factory()->create();
    $item = Item::factory()->create(['unit_id' => $unit->id]);

    $this->assertModelExists($unit);

    expect($unit->items())->toBeInstanceOf(HasMany::class)
        ->and($unit->items)->toHaveCount(1)
        ->and($unit->items->first()->is($item))->toBeTrue();
});
