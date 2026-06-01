<?php

use App\Models\Item;

beforeEach(function () {
    $this->headers = settingsAuthHeaders($this);
});

function expectItemShape(array $item): void
{
    expect($item)->toHaveKeys([
        'id', 'name', 'sku', 'category_id', 'subcategory_id', 'unit_id', 'supplier_id',
        'category', 'subcategory', 'unit', 'supplier', 'price', 'stock_quantity',
        'low_stock_threshold', 'description', 'is_active', 'created_at', 'updated_at',
    ])
        ->and($item['category'])->toHaveKeys(['id', 'name'])
        ->and($item['subcategory'])->toHaveKeys(['id', 'name'])
        ->and($item['unit'])->toHaveKeys(['id', 'name', 'symbol'])
        ->and($item['supplier'])->toHaveKeys(['id', 'name']);
}

it('lists, shows, creates, updates, partially updates, and deletes items', function () {
    $seededCount = Item::query()->count();

    $listResponse = $this->withHeaders($this->headers)->getJson('/api/items?per_page=5')
        ->assertSuccessful()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Items fetched successfully.')
        ->assertJsonPath('meta.current_page', 1)
        ->assertJsonPath('meta.per_page', 5)
        ->assertJsonPath('meta.total', $seededCount);

    expect($listResponse->json('meta.last_page'))->toBeGreaterThanOrEqual(1)
        ->and($listResponse->json('data'))->toHaveCount(5);

    expectItemShape($listResponse->json('data.0'));

    $itemId = $listResponse->json('data.0.id');

    $this->withHeaders($this->headers)->getJson("/api/items/{$itemId}")
        ->assertSuccessful()
        ->assertJsonPath('message', 'Item retrieved.')
        ->assertJsonPath('data.id', $itemId);

    $this->withHeaders($this->headers)->getJson('/api/items/999999')
        ->assertNotFound()
        ->assertExactJson(['success' => false, 'message' => 'Item not found.']);

    $createdId = $this->withHeaders($this->headers)->postJson('/api/items', inventoryPayload())
        ->assertCreated()
        ->assertJsonPath('message', 'Item created successfully.')
        ->assertJsonPath('data.name', 'Test Whole Milk 1L')
        ->assertJsonStructure(['data' => ['category', 'subcategory', 'unit', 'supplier']])
        ->json('data.id');

    $this->assertModelExists(Item::query()->findOrFail($createdId));

    $this->withHeaders($this->headers)->putJson("/api/items/{$createdId}", inventoryPayload([
        'name' => 'Updated Whole Milk 1L',
        'sku' => 'TEST-MILK-1L-UPDATED',
        'price' => 3.19,
    ]))->assertSuccessful()
        ->assertJsonPath('message', 'Item updated successfully.')
        ->assertJsonPath('data.name', 'Updated Whole Milk 1L');

    $this->withHeaders($this->headers)->putJson("/api/items/{$createdId}", [
        'stock_quantity' => 99,
    ])->assertSuccessful()
        ->assertJsonPath('data.stock_quantity', 99)
        ->assertJsonPath('data.name', 'Updated Whole Milk 1L');

    $this->withHeaders($this->headers)->deleteJson("/api/items/{$createdId}")
        ->assertSuccessful()
        ->assertExactJson(['success' => true, 'message' => 'Item deleted successfully.']);

    expect(Item::query()->whereKey($createdId)->exists())->toBeFalse();

    $this->withHeaders($this->headers)->deleteJson('/api/items/999999')
        ->assertNotFound()
        ->assertExactJson(['success' => false, 'message' => 'Item not found.']);
});
