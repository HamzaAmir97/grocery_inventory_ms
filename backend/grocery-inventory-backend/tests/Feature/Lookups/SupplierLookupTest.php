<?php

use App\Models\Supplier;

beforeEach(function () {
    $this->headers = settingsAuthHeaders($this);
});

it('returns active suppliers as slim alphabetized lookup records', function () {
    $response = $this->withHeaders($this->headers)->getJson('/api/lookups/suppliers')
        ->assertSuccessful()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Suppliers lookups retrieved.')
        ->assertJsonMissingPath('meta');

    $data = $response->json('data');

    expect($data)->toHaveCount(3);

    collect($data)->each(function (array $supplier): void {
        expect(array_keys($supplier))->toBe(['id', 'name']);
    });

    $names = collect($data)->pluck('name')->all();

    expect($names)->toBe(collect($names)->sort()->values()->all());
});

it('excludes inactive suppliers until they are reactivated', function () {
    $supplier = Supplier::query()->where('name', 'Daily Dairy Co')->sole();

    $supplier->update(['is_active' => false]);

    $this->withHeaders($this->headers)->getJson('/api/lookups/suppliers')
        ->assertSuccessful()
        ->assertJsonMissing(['name' => 'Daily Dairy Co']);

    $supplier->update(['is_active' => true]);

    $this->withHeaders($this->headers)->getJson('/api/lookups/suppliers')
        ->assertSuccessful()
        ->assertJsonFragment(['name' => 'Daily Dairy Co']);
});
