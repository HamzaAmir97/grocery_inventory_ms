<?php

use App\Models\Item;
use App\Models\Supplier;

beforeEach(function () {
    $this->headers = settingsAuthHeaders($this);
});

it('lists, searches, paginates, creates, shows, updates, and deletes suppliers', function () {
    $this->withHeaders($this->headers)->getJson('/api/suppliers')->assertSuccessful()->assertJsonPath('meta.total', 3);
    $this->withHeaders($this->headers)->getJson('/api/suppliers?per_page=2')->assertSuccessful()->assertJsonCount(2, 'data')->assertJsonPath('meta.per_page', 2);
    $this->withHeaders($this->headers)->getJson('/api/suppliers?search=Fresh')->assertSuccessful()->assertJsonCount(1, 'data')->assertJsonPath('data.0.name', 'Fresh Farm Supplies');
    $this->withHeaders($this->headers)->getJson('/api/suppliers?page=9999')->assertSuccessful()->assertJsonCount(0, 'data');

    $supplierId = $this->withHeaders($this->headers)->postJson('/api/suppliers', [
        'name' => 'North Wholesale',
        'contact_person' => 'Nora Smith',
        'phone' => '+1-555-0111',
        'email' => 'north@example.com',
        'address' => '1 North Road',
    ])->assertCreated()
        ->assertJsonPath('data.contact_person', 'Nora Smith')
        ->assertJsonPath('data.phone', '+1-555-0111')
        ->assertJsonPath('data.email', 'north@example.com')
        ->assertJsonPath('data.address', '1 North Road')
        ->json('data.id');

    $this->withHeaders($this->headers)->getJson("/api/suppliers/{$supplierId}")->assertSuccessful()->assertJsonPath('data.name', 'North Wholesale');
    $this->withHeaders($this->headers)->putJson("/api/suppliers/{$supplierId}", ['name' => 'Northern Wholesale'])->assertSuccessful()->assertJsonPath('data.name', 'Northern Wholesale');
    $this->withHeaders($this->headers)->putJson("/api/suppliers/{$supplierId}", ['name' => 'Northern Wholesale'])->assertSuccessful();
    $this->withHeaders($this->headers)->deleteJson("/api/suppliers/{$supplierId}")->assertSuccessful()->assertExactJson(['success' => true, 'message' => 'Supplier deleted successfully.']);
});

it('validates supplier writes', function () {
    $existing = Supplier::query()->where('name', 'Fresh Farm Supplies')->sole();

    $this->withHeaders($this->headers)->postJson('/api/suppliers', ['name' => 'Bad Email', 'email' => 'not-an-email'])->assertUnprocessable()->assertJsonStructure(['errors' => ['email']]);
    $this->withHeaders($this->headers)->postJson('/api/suppliers', ['name' => 'Trimmed Email', 'email' => '  admin@example.com  '])->assertCreated()->assertJsonPath('data.email', 'admin@example.com');
    $this->withHeaders($this->headers)->postJson('/api/suppliers', ['name' => 'Fresh Farm Supplies'])->assertUnprocessable()->assertJsonStructure(['errors' => ['name']]);
    $this->withHeaders($this->headers)->postJson('/api/suppliers', ['name' => ''])->assertUnprocessable()->assertJsonStructure(['errors' => ['name']]);
    $this->withHeaders($this->headers)->putJson("/api/suppliers/{$existing->id}", ['name' => $existing->name])->assertSuccessful();
});

it('searches supplier contacts and sorts supplier indexes', function () {
    Supplier::factory()->create(['name' => 'Sort Supplier Alpha', 'contact_person' => 'Sorting Contact Alpha']);
    Supplier::factory()->create(['name' => 'Sort Supplier Beta', 'contact_person' => 'Sorting Contact Beta']);

    $data = $this->withHeaders($this->headers)
        ->getJson('/api/suppliers?search=Sorting%20Contact&sort_by=contact_person&sort_dir=asc&per_page=10')
        ->assertSuccessful()
        ->json('data');

    expect(array_column($data, 'contact_person'))->toBe(['Sorting Contact Alpha', 'Sorting Contact Beta']);
});

it('refuses supplier deletes with items and returns not found for unknown ids', function () {
    $supplier = Supplier::factory()->create();
    Item::factory()->create(['supplier_id' => $supplier->id]);

    $this->withHeaders($this->headers)->deleteJson("/api/suppliers/{$supplier->id}")
        ->assertConflict()
        ->assertExactJson(['success' => false, 'message' => 'This supplier still has items.']);

    expect(Supplier::query()->whereKey($supplier->id)->exists())->toBeTrue();

    $this->withHeaders($this->headers)->deleteJson('/api/suppliers/999999')
        ->assertNotFound()
        ->assertExactJson(['success' => false, 'message' => 'Supplier not found.']);
});
