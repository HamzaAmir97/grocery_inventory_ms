<?php

use App\Models\Category;

beforeEach(function () {
    $this->headers = settingsAuthHeaders($this);
});

it('returns active categories as slim alphabetized lookup records', function () {
    $response = $this->withHeaders($this->headers)->getJson('/api/lookups/categories')
        ->assertSuccessful()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Categories lookups retrieved.')
        ->assertJsonMissingPath('meta');

    $data = $response->json('data');

    expect($data)->toHaveCount(5);

    collect($data)->each(function (array $category): void {
        expect(array_keys($category))->toBe(['id', 'name']);
    });

    $names = collect($data)->pluck('name')->all();

    expect($names)->toBe(collect($names)->sort()->values()->all());

    $this->withHeaders($this->headers)->postJson('/api/categories', [
        'name' => 'Frozen',
        'description' => 'Frozen goods',
    ])->assertCreated();

    $this->withHeaders($this->headers)->getJson('/api/lookups/categories')
        ->assertSuccessful()
        ->assertJsonFragment(['name' => 'Frozen']);
});

it('excludes inactive categories until they are reactivated', function () {
    $bakery = Category::query()->where('name', 'Bakery')->sole();

    $bakery->update(['is_active' => false]);

    $this->withHeaders($this->headers)->getJson('/api/lookups/categories')
        ->assertSuccessful()
        ->assertJsonMissing(['name' => 'Bakery']);

    $bakery->update(['is_active' => true]);

    $this->withHeaders($this->headers)->getJson('/api/lookups/categories')
        ->assertSuccessful()
        ->assertJsonFragment(['name' => 'Bakery']);
});
