<?php

use App\Models\Supplier;

beforeEach(function () {
    $this->headers = settingsAuthHeaders($this);
});

it('lets a soft-deleted item free its SKU for reuse', function () {
    $firstId = $this->withHeaders($this->headers)
        ->postJson('/api/items', inventoryPayload(['name' => 'Reuse A', 'sku' => 'REUSE-SKU-1']))
        ->assertCreated()
        ->json('data.id');

    $this->withHeaders($this->headers)->deleteJson("/api/items/{$firstId}")->assertSuccessful();

    // The SKU was freed by the soft delete, so a new item may take it.
    $this->withHeaders($this->headers)
        ->postJson('/api/items', inventoryPayload(['name' => 'Reuse B', 'sku' => 'REUSE-SKU-1']))
        ->assertCreated();
});

it('blocks deleting a supplier still referenced by a soft-deleted item', function () {
    $supplier = Supplier::factory()->create();

    $itemId = $this->withHeaders($this->headers)
        ->postJson('/api/items', inventoryPayload(['sku' => 'GUARD-SKU-1', 'supplier_id' => $supplier->id]))
        ->assertCreated()
        ->json('data.id');

    $this->withHeaders($this->headers)->deleteJson("/api/items/{$itemId}")->assertSuccessful();

    // The trashed item still references the supplier, so the guard must block (409),
    // not let the hard delete reach a database FK violation.
    $this->withHeaders($this->headers)
        ->deleteJson("/api/suppliers/{$supplier->id}")
        ->assertStatus(409)
        ->assertJsonPath('message', 'This supplier still has items.');
});

it('excludes soft-deleted items from dashboard totals', function () {
    $before = $this->withHeaders($this->headers)->getJson('/api/dashboard/stats')->json('data.total_items');

    $itemId = $this->withHeaders($this->headers)
        ->postJson('/api/items', inventoryPayload(['sku' => 'DASH-SKU-1']))
        ->assertCreated()
        ->json('data.id');

    $this->withHeaders($this->headers)->deleteJson("/api/items/{$itemId}")->assertSuccessful();

    $after = $this->withHeaders($this->headers)->getJson('/api/dashboard/stats')->json('data.total_items');

    expect($after)->toBe($before);
});
