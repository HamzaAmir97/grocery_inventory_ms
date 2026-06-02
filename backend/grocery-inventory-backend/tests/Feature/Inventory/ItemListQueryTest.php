<?php

use App\Models\Category;
use App\Models\Item;
use App\Models\Subcategory;
use App\Models\Supplier;
use App\Models\Unit;

beforeEach(function () {
    $this->headers = settingsAuthHeaders($this);
});

it('searches names and sku case-insensitively while treating special characters literally', function () {
    $payload = inventoryPayload([
        'name' => 'Milk Percent 50%',
        'sku' => 'MILK-50-PCT',
        'stock_quantity' => 2,
        'low_stock_threshold' => 5,
    ]);

    Item::factory()->create($payload);
    Item::factory()->create(inventoryPayload([
        'name' => 'Milk Plain Search Fixture',
        'sku' => 'MILK-PLAIN-FIXTURE',
    ]));

    $milkData = $this->withHeaders($this->headers)->getJson('/api/items?search=milk&per_page=100')
        ->assertSuccessful()
        ->json('data');

    expect($milkData)->not->toBeEmpty();

    collect($milkData)->each(function (array $item): void {
        expect(str_contains(strtolower($item['name']), 'milk') || str_contains(strtolower((string) $item['sku']), 'milk'))->toBeTrue();
    });

    $unfilteredTotal = $this->withHeaders($this->headers)->getJson('/api/items')->assertSuccessful()->json('meta.total');

    $this->withHeaders($this->headers)->getJson('/api/items?search=')
        ->assertSuccessful()
        ->assertJsonPath('meta.total', $unfilteredTotal);

    $percentData = $this->withHeaders($this->headers)->getJson('/api/items?search=%25&per_page=100')
        ->assertSuccessful()
        ->json('data');

    expect($percentData)->not->toBeEmpty();

    collect($percentData)->each(function (array $item): void {
        expect(str_contains($item['name'], '%') || str_contains((string) $item['sku'], '%'))->toBeTrue();
    });
});

it('filters by foreign keys, supports unknown ids as empty lists, and validates non-integer filters', function () {
    $dairy = Category::query()->where('name', 'Dairy')->sole();

    $categoryData = $this->withHeaders($this->headers)->getJson("/api/items?category_id={$dairy->id}&per_page=100")
        ->assertSuccessful()
        ->json('data');

    expect($categoryData)->not->toBeEmpty();

    collect($categoryData)->each(fn (array $item) => expect($item['category_id'])->toBe($dairy->id));

    $fixture = Item::query()->firstOrFail();

    $combinedData = $this->withHeaders($this->headers)->getJson("/api/items?subcategory_id={$fixture->subcategory_id}&unit_id={$fixture->unit_id}&supplier_id={$fixture->supplier_id}&per_page=100")
        ->assertSuccessful()
        ->json('data');

    expect($combinedData)->not->toBeEmpty();

    collect($combinedData)->each(function (array $item) use ($fixture): void {
        expect($item['subcategory_id'])->toBe($fixture->subcategory_id)
            ->and($item['unit_id'])->toBe($fixture->unit_id)
            ->and($item['supplier_id'])->toBe($fixture->supplier_id);
    });

    $this->withHeaders($this->headers)->getJson('/api/items?category_id=999999')
        ->assertSuccessful()
        ->assertJsonCount(0, 'data');

    $this->withHeaders($this->headers)->getJson('/api/items?category_id=foo')
        ->assertUnprocessable()
        ->assertJsonPath('success', false)
        ->assertJsonStructure(['errors' => ['category_id']]);
});

it('accepts the boolean low_stock filter as the string sent by the frontend', function () {
    $lowStockData = $this->withHeaders($this->headers)->getJson('/api/items?low_stock=true&per_page=100')
        ->assertSuccessful()
        ->json('data');

    expect($lowStockData)->not->toBeEmpty();

    collect($lowStockData)->each(function (array $item): void {
        expect($item['stock_quantity'])->toBeLessThanOrEqual($item['low_stock_threshold']);
    });
});

it('filters low stock, sorts allowlisted fields, rejects bad sort fields, and defaults newest first', function () {
    $lowStockData = $this->withHeaders($this->headers)->getJson('/api/items?low_stock=1&per_page=100')
        ->assertSuccessful()
        ->json('data');

    expect($lowStockData)->not->toBeEmpty();

    collect($lowStockData)->each(function (array $item): void {
        expect($item['stock_quantity'])->toBeLessThanOrEqual($item['low_stock_threshold']);
    });

    $priceData = $this->withHeaders($this->headers)->getJson('/api/items?sort_by=price&sort_dir=asc&per_page=100')
        ->assertSuccessful()
        ->json('data');

    $prices = collect($priceData)->pluck('price')->map(fn (string $price): float => (float) $price)->all();

    expect($prices)->toBe(collect($prices)->sort()->values()->all());

    $categoryData = $this->withHeaders($this->headers)->getJson('/api/items?sort_by=category&sort_dir=asc&per_page=100')
        ->assertSuccessful()
        ->json('data');

    $categoryNames = collect($categoryData)->pluck('category.name')->all();

    expect($categoryNames)->toBe(collect($categoryNames)->sort()->values()->all());

    $this->withHeaders($this->headers)->getJson('/api/items?sort_by=hahaha')
        ->assertUnprocessable()
        ->assertJsonStructure(['errors' => ['sort_by']]);

    $defaultData = $this->withHeaders($this->headers)->getJson('/api/items')
        ->assertSuccessful()
        ->json('data');

    expect($defaultData[0]['created_at'] >= $defaultData[1]['created_at'])->toBeTrue();

    $this->withHeaders($this->headers)->getJson('/api/items?per_page=999')
        ->assertUnprocessable()
        ->assertJsonStructure(['errors' => ['per_page']]);
});

it('composes search, filters, low stock, sort, and pagination in one request', function () {
    $dairy = Category::query()->where('name', 'Dairy')->sole();
    $subcategory = Subcategory::query()->where('category_id', $dairy->id)->where('name', 'Yogurt')->sole();
    $unit = Unit::query()->where('name', 'Piece')->sole();
    $supplier = Supplier::query()->where('name', 'Daily Dairy Co')->sole();

    Item::factory()->create([
        'name' => 'Milk Combo Fixture',
        'sku' => 'MILK-COMBO-FIXTURE',
        'category_id' => $dairy->id,
        'subcategory_id' => $subcategory->id,
        'unit_id' => $unit->id,
        'supplier_id' => $supplier->id,
        'price' => 4.75,
        'stock_quantity' => 2,
        'low_stock_threshold' => 5,
    ]);

    $data = $this->withHeaders($this->headers)->getJson("/api/items?search=milk&category_id={$dairy->id}&low_stock=1&sort_by=price&sort_dir=desc&per_page=5&page=1")
        ->assertSuccessful()
        ->assertJsonPath('meta.per_page', 5)
        ->json('data');

    expect($data)->not->toBeEmpty();

    collect($data)->each(function (array $item) use ($dairy): void {
        expect($item['category_id'])->toBe($dairy->id)
            ->and($item['stock_quantity'])->toBeLessThanOrEqual($item['low_stock_threshold'])
            ->and(str_contains(strtolower($item['name']), 'milk') || str_contains(strtolower((string) $item['sku']), 'milk'))->toBeTrue();
    });

    $prices = collect($data)->pluck('price')->map(fn (string $price): float => (float) $price)->all();

    expect($prices)->toBe(collect($prices)->sortDesc()->values()->all());
});
