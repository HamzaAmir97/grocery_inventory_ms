<?php

use App\Models\Category;
use App\Models\Subcategory;

beforeEach(function () {
    $this->headers = settingsAuthHeaders($this);
});

it('returns active subcategories as slim alphabetized lookup records', function () {
    $response = $this->withHeaders($this->headers)->getJson('/api/lookups/subcategories')
        ->assertSuccessful()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Subcategories lookups retrieved.')
        ->assertJsonMissingPath('meta');

    $data = $response->json('data');

    expect($data)->toHaveCount(15);

    collect($data)->each(function (array $subcategory): void {
        expect(array_keys($subcategory))->toBe(['id', 'category_id', 'name']);
    });

    $names = collect($data)->pluck('name')->all();

    expect($names)->toBe(collect($names)->sort()->values()->all());
});

it('filters subcategories by parent category and validates category id', function () {
    $dairy = Category::query()->where('name', 'Dairy')->sole();
    $bakery = Category::query()->where('name', 'Bakery')->sole();

    $dairyData = $this->withHeaders($this->headers)->getJson("/api/lookups/subcategories?category_id={$dairy->id}")
        ->assertSuccessful()
        ->json('data');

    expect($dairyData)->toHaveCount(3)
        ->and(collect($dairyData)->pluck('category_id')->unique()->values()->all())->toBe([$dairy->id])
        ->and(collect($dairyData)->pluck('name')->sort()->values()->all())->toBe(['Cheese', 'Milk', 'Yogurt']);

    $bakeryData = $this->withHeaders($this->headers)->getJson("/api/lookups/subcategories?category_id={$bakery->id}")
        ->assertSuccessful()
        ->json('data');

    expect($bakeryData)->toHaveCount(3)
        ->and(collect($bakeryData)->pluck('category_id')->unique()->values()->all())->toBe([$bakery->id])
        ->and(collect($bakeryData)->pluck('name')->sort()->values()->all())->toBe(['Bread', 'Cakes', 'Pastries']);

    $this->withHeaders($this->headers)->getJson('/api/lookups/subcategories')
        ->assertSuccessful()
        ->assertJsonCount(15, 'data');

    $this->withHeaders($this->headers)->getJson('/api/lookups/subcategories?category_id=')
        ->assertSuccessful()
        ->assertJsonCount(15, 'data');

    $this->withHeaders($this->headers)->getJson('/api/lookups/subcategories?category_id=999999')
        ->assertSuccessful()
        ->assertJsonCount(0, 'data');

    $this->withHeaders($this->headers)->getJson('/api/lookups/subcategories?category_id=foo')
        ->assertUnprocessable()
        ->assertJsonPath('success', false)
        ->assertJsonStructure(['errors' => ['category_id']]);
});

it('excludes inactive subcategories and subcategories with inactive parents', function () {
    $dairy = Category::query()->where('name', 'Dairy')->sole();
    $milk = Subcategory::query()->where('name', 'Milk')->sole();

    $milk->update(['is_active' => false]);

    $this->withHeaders($this->headers)->getJson('/api/lookups/subcategories')
        ->assertSuccessful()
        ->assertJsonMissing(['name' => 'Milk']);

    $this->withHeaders($this->headers)->getJson("/api/lookups/subcategories?category_id={$dairy->id}")
        ->assertSuccessful()
        ->assertJsonMissing(['name' => 'Milk']);

    $milk->update(['is_active' => true]);
    $dairy->update(['is_active' => false]);

    $this->withHeaders($this->headers)->getJson("/api/lookups/subcategories?category_id={$dairy->id}")
        ->assertSuccessful()
        ->assertJsonCount(0, 'data');

    $data = $this->withHeaders($this->headers)->getJson('/api/lookups/subcategories')
        ->assertSuccessful()
        ->json('data');

    expect(collect($data)->pluck('category_id')->contains($dairy->id))->toBeFalse();

    $dairy->update(['is_active' => true]);

    $this->withHeaders($this->headers)->getJson("/api/lookups/subcategories?category_id={$dairy->id}")
        ->assertSuccessful()
        ->assertJsonFragment(['name' => 'Milk']);
});
