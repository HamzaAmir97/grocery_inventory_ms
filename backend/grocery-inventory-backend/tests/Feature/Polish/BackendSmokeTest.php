<?php

use App\Models\Item;
use Database\Seeders\DatabaseSeeder;

it('smoke tests the polished backend contract across delivered features', function () {
    $this->seed(DatabaseSeeder::class);

    $token = $this->postJson('/api/auth/login', [
        'email' => 'admin@example.com',
        'password' => 'password',
    ])->assertSuccessful()
        ->assertJsonPath('success', true)
        ->json('data.token');

    $headers = ['Authorization' => "Bearer {$token}"];

    $this->withHeaders($headers)->getJson('/api/auth/me')
        ->assertSuccessful()
        ->assertJsonPath('data.email', 'admin@example.com');

    $categoryId = $this->withHeaders($headers)->postJson('/api/categories', [
        'name' => 'Smoke Category',
        'description' => 'Created during backend polish smoke test.',
    ])->assertCreated()
        ->json('data.id');

    $this->withHeaders($headers)->getJson("/api/categories/{$categoryId}")
        ->assertSuccessful()
        ->assertJsonPath('data.name', 'Smoke Category');

    $this->withHeaders($headers)->putJson("/api/categories/{$categoryId}", [
        'name' => 'Smoke Category Updated',
    ])->assertSuccessful()
        ->assertJsonPath('data.name', 'Smoke Category Updated');

    $this->withHeaders($headers)->getJson('/api/lookups/categories')
        ->assertSuccessful()
        ->assertJsonPath('success', true);

    $itemId = $this->withHeaders($headers)->postJson('/api/items', inventoryPayload([
        'name' => 'Smoke Item',
        'sku' => 'SMOKE-ITEM',
    ]))->assertCreated()
        ->json('data.id');

    $this->withHeaders($headers)->getJson("/api/items/{$itemId}")
        ->assertSuccessful()
        ->assertJsonPath('data.name', 'Smoke Item');

    $this->withHeaders($headers)->getJson('/api/dashboard/stats')
        ->assertSuccessful()
        ->assertJsonPath('success', true);

    $this->withHeaders($headers)->postJson('/api/items', [])
        ->assertUnprocessable()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Validation failed.')
        ->assertJsonStructure(['errors' => ['name']])
        ->assertJsonMissingPath('data');

    $this->withHeaders($headers)->getJson('/api/items/999999')
        ->assertNotFound()
        ->assertExactJson(['success' => false, 'message' => 'Item not found.']);

    expect(Item::query()->whereKey($itemId)->exists())->toBeTrue();
});
