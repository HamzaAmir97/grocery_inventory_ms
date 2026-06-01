<?php

use App\Models\Category;
use App\Models\Item;
use App\Models\Subcategory;

beforeEach(function () {
    $this->headers = settingsAuthHeaders($this);
});

it('validates required fields, non-negative numbers, foreign keys, sku uniqueness, and unknown fields', function () {
    $this->withHeaders($this->headers)->postJson('/api/items')
        ->assertUnprocessable()
        ->assertJsonPath('success', false)
        ->assertJsonStructure(['errors' => ['name', 'category_id', 'subcategory_id', 'unit_id', 'supplier_id', 'price', 'stock_quantity']]);

    $this->withHeaders($this->headers)->postJson('/api/items', inventoryPayload(['name' => '']))
        ->assertUnprocessable()
        ->assertJsonStructure(['errors' => ['name']]);

    foreach (['price', 'stock_quantity', 'low_stock_threshold'] as $field) {
        $this->withHeaders($this->headers)->postJson('/api/items', inventoryPayload([$field => -1]))
            ->assertUnprocessable()
            ->assertJsonStructure(['errors' => [$field]]);
    }

    foreach (['category_id', 'subcategory_id', 'unit_id', 'supplier_id'] as $field) {
        $this->withHeaders($this->headers)->postJson('/api/items', inventoryPayload([$field => 999999]))
            ->assertUnprocessable()
            ->assertJsonStructure(['errors' => [$field]]);
    }

    $dairy = Category::query()->where('name', 'Dairy')->sole();
    $bakerySubcategory = Subcategory::query()
        ->whereRelation('category', 'name', 'Bakery')
        ->firstOrFail();

    $this->withHeaders($this->headers)->postJson('/api/items', inventoryPayload([
        'category_id' => $dairy->id,
        'subcategory_id' => $bakerySubcategory->id,
    ]))->assertUnprocessable()
        ->assertJsonStructure(['errors' => ['subcategory_id']]);

    $existing = Item::factory()->create(inventoryPayload([
        'name' => 'Existing SKU Item',
        'sku' => 'EXISTING-SKU-ITEM',
    ]));

    $this->withHeaders($this->headers)->postJson('/api/items', inventoryPayload(['sku' => $existing->sku]))
        ->assertUnprocessable()
        ->assertJsonStructure(['errors' => ['sku']]);

    $emptySkuId = $this->withHeaders($this->headers)->postJson('/api/items', inventoryPayload([
        'name' => 'Empty SKU Item',
        'sku' => '',
    ]))->assertCreated()
        ->assertJsonPath('data.sku', null)
        ->json('data.id');

    expect(Item::query()->findOrFail($emptySkuId)->sku)->toBeNull();

    $this->withHeaders($this->headers)->postJson('/api/items', inventoryPayload([
        'name' => 'Second Null SKU Item',
        'sku' => null,
    ]))->assertCreated()
        ->assertJsonPath('data.sku', null);

    $createdId = $this->withHeaders($this->headers)->postJson('/api/items', inventoryPayload([
        'name' => 'Unknown Field Item',
        'sku' => 'UNKNOWN-FIELD-ITEM',
        'extra_field' => 'ignored',
    ]))->assertCreated()
        ->json('data.id');

    expect(Item::query()->findOrFail($createdId)->getAttributes())->not->toHaveKey('extra_field');
});

it('allows ignore-self sku, partial updates, and validates swapped category subcategory pairs', function () {
    $itemId = $this->withHeaders($this->headers)->postJson('/api/items', inventoryPayload([
        'name' => 'Swappable Item',
        'sku' => 'SWAPPABLE-ITEM',
    ]))->assertCreated()->json('data.id');

    $this->withHeaders($this->headers)->putJson("/api/items/{$itemId}", [
        'sku' => 'SWAPPABLE-ITEM',
    ])->assertSuccessful();

    $this->withHeaders($this->headers)->putJson("/api/items/{$itemId}", [
        'price' => 5.99,
    ])->assertSuccessful()
        ->assertJsonPath('data.price', '5.99');

    $bakery = Category::query()->where('name', 'Bakery')->sole();
    $bakerySubcategory = Subcategory::query()->where('category_id', $bakery->id)->firstOrFail();
    $dairySubcategory = Subcategory::query()->whereRelation('category', 'name', 'Dairy')->firstOrFail();

    $this->withHeaders($this->headers)->putJson("/api/items/{$itemId}", [
        'category_id' => $bakery->id,
        'subcategory_id' => $bakerySubcategory->id,
    ])->assertSuccessful()
        ->assertJsonPath('data.category_id', $bakery->id)
        ->assertJsonPath('data.subcategory_id', $bakerySubcategory->id);

    $this->withHeaders($this->headers)->putJson("/api/items/{$itemId}", [
        'category_id' => $bakery->id,
        'subcategory_id' => $dairySubcategory->id,
    ])->assertUnprocessable()
        ->assertJsonStructure(['errors' => ['subcategory_id']]);
});
