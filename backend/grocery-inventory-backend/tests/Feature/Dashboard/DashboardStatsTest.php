<?php

use App\Models\Category;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;

function dashboardSeededHeaders($test): array
{
    return settingsAuthHeaders($test);
}

function dashboardManualHeaders($test): array
{
    $test->seed(AdminUserSeeder::class);

    $token = $test->postJson('/api/auth/login', [
        'email' => 'admin@example.com',
        'password' => 'password',
    ])->json('data.token');

    return ['Authorization' => "Bearer {$token}"];
}

function expectedDashboardStockValue(): string
{
    return number_format((float) Item::query()->selectRaw('COALESCE(SUM(price * stock_quantity), 0) AS total')->value('total'), 2, '.', '');
}

function assertDashboardItemShape(array $item): void
{
    expect(array_keys($item))->toBe(['id', 'name', 'sku', 'category', 'supplier', 'unit_symbol', 'stock_quantity', 'low_stock_threshold', 'price', 'status', 'status_label', 'status_tone'])
        ->and($item['id'])->toBeInt()
        ->and($item['name'])->toBeString()
        ->and(is_string($item['sku']) || $item['sku'] === null)->toBeTrue()
        ->and($item['category'])->toBeString()
        ->and($item['supplier'])->toBeString()
        ->and($item['unit_symbol'])->toBeString()
        ->and($item['stock_quantity'])->toBeInt()
        ->and($item['low_stock_threshold'])->toBeInt()
        ->and($item['price'])->toBeString()
        ->and($item['status'])->toBeString()
        ->and($item['status_label'])->toBeString()
        ->and($item['status_tone'])->toBeString();
}

it('returns accurate headline aggregates for the seeded catalog', function () {
    $response = $this->withHeaders(dashboardSeededHeaders($this))->getJson('/api/dashboard/stats')
        ->assertSuccessful()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Dashboard summary retrieved.');

    $data = $response->json('data');

    expect($data['total_items'])->toBe(Item::query()->count())
        ->and($data['total_categories'])->toBe(Category::query()->count())
        ->and($data['total_suppliers'])->toBe(Supplier::query()->count())
        ->and($data['low_stock_items'])->toBe(Item::query()->lowStock()->count())
        ->and($data['total_stock_value'])->toBe(expectedDashboardStockValue())
        ->and($data['total_stock_value'])->toBeString()->toMatch('/^\d+\.\d{2}$/')
        ->and($data['total_items'])->toBeInt()
        ->and($data['total_categories'])->toBeInt()
        ->and($data['total_suppliers'])->toBeInt()
        ->and($data['low_stock_items'])->toBeInt()
        ->and($data['summary_cards'])->toHaveCount(4)
        ->and($data['inventory_growth'])->toHaveCount(12)
        ->and($data['inventory_growth_year'])->toBe(now()->year)
        ->and($data['category_breakdown']['total'])->toBe(Item::query()->count());
});

it('returns zeros and empty lists for an empty catalog', function () {
    User::factory()->create([
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
    ]);

    $token = $this->postJson('/api/auth/login', [
        'email' => 'admin@example.com',
        'password' => 'password',
    ])->json('data.token');

    $this->withHeaders(['Authorization' => "Bearer {$token}"])->getJson('/api/dashboard/stats')
        ->assertSuccessful()
        ->assertJsonPath('data.total_items', 0)
        ->assertJsonPath('data.total_categories', 0)
        ->assertJsonPath('data.total_suppliers', 0)
        ->assertJsonPath('data.low_stock_items', 0)
        ->assertJsonPath('data.total_stock_value', '0.00')
        ->assertJsonPath('data.summary_cards', [
            ['key' => 'total_items', 'label' => 'Total items', 'value' => 0, 'badge' => '+0', 'badge_tone' => 'success'],
            ['key' => 'categories', 'label' => 'Categories', 'value' => 0, 'badge' => '+0', 'badge_tone' => 'success'],
            ['key' => 'suppliers', 'label' => 'Suppliers', 'value' => 0, 'badge' => '+0', 'badge_tone' => 'success'],
            ['key' => 'low_stock', 'label' => 'Low stock', 'value' => 0, 'badge' => 'healthy', 'badge_tone' => 'success'],
        ])
        ->assertJsonPath('data.inventory_growth_year', now()->year)
        ->assertJsonCount(12, 'data.inventory_growth')
        ->assertJsonPath('data.category_breakdown.total', 0)
        ->assertJsonPath('data.category_breakdown.items', [])
        ->assertJsonPath('data.recent_items', [])
        ->assertJsonPath('data.low_stock_list', []);
});

it('reflects changed stock values on the next call and includes inactive items', function () {
    $headers = dashboardSeededHeaders($this);
    $item = Item::query()->firstOrFail();
    $expectedInitialValue = expectedDashboardStockValue();

    $this->withHeaders($headers)->getJson('/api/dashboard/stats')
        ->assertSuccessful()
        ->assertJsonPath('data.total_stock_value', $expectedInitialValue);

    $item->update(['stock_quantity' => $item->stock_quantity + 10]);

    $this->withHeaders($headers)->getJson('/api/dashboard/stats')
        ->assertSuccessful()
        ->assertJsonPath('data.total_stock_value', expectedDashboardStockValue());

    $inactive = Item::factory()->create([
        'is_active' => false,
        'stock_quantity' => 1,
        'low_stock_threshold' => 5,
        'price' => 5.00,
    ]);

    $response = $this->withHeaders($headers)->getJson('/api/dashboard/stats')
        ->assertSuccessful();

    expect($response->json('data.total_items'))->toBe(Item::query()->count())
        ->and($response->json('data.low_stock_items'))->toBe(Item::query()->lowStock()->count())
        ->and($response->json('data.total_stock_value'))->toBe(expectedDashboardStockValue())
        ->and(collect($response->json('data.low_stock_list'))->pluck('id'))->toContain($inactive->id);
});

it('returns recent items in the enriched shape ordered by recency and id', function () {
    $headers = dashboardSeededHeaders($this);
    $response = $this->withHeaders($headers)->getJson('/api/dashboard/stats')
        ->assertSuccessful();

    $recent = $response->json('data.recent_items');
    $expectedIds = Item::query()->orderByDesc('created_at')->orderByDesc('id')->limit(5)->pluck('id')->all();

    expect($recent)->toHaveCount(5)
        ->and(collect($recent)->pluck('id')->all())->toBe($expectedIds);

    collect($recent)->each(fn (array $item) => assertDashboardItemShape($item));

    $previousLastId = $recent[4]['id'];
    $newItem = Item::factory()->create(['created_at' => now()->addSecond(), 'updated_at' => now()->addSecond()]);

    $refetched = $this->withHeaders($headers)->getJson('/api/dashboard/stats')
        ->assertSuccessful()
        ->json('data.recent_items');

    expect($refetched[0]['id'])->toBe($newItem->id)
        ->and(collect($refetched)->pluck('id'))->not->toContain($previousLastId);

    $timestamp = now()->addMinutes(5);
    $sameTimeItems = Item::factory()->count(3)->create([
        'created_at' => $timestamp,
        'updated_at' => $timestamp,
    ]);

    $sameTimeIds = $sameTimeItems->pluck('id')->sortDesc()->values()->all();
    $latestRecentIds = collect($this->withHeaders($headers)->getJson('/api/dashboard/stats')->json('data.recent_items'))->pluck('id')->all();

    expect(array_slice($latestRecentIds, 0, 3))->toBe($sameTimeIds);
});

it('returns an empty recent item list for an empty catalog', function () {
    $headers = dashboardManualHeaders($this);

    $this->withHeaders($headers)->getJson('/api/dashboard/stats')
        ->assertSuccessful()
        ->assertJsonPath('data.recent_items', []);
});

it('returns a capped low stock list with no false positives', function () {
    $headers = dashboardSeededHeaders($this);
    $response = $this->withHeaders($headers)->getJson('/api/dashboard/stats')
        ->assertSuccessful();

    $lowStockList = $response->json('data.low_stock_list');
    $expectedIds = Item::query()->lowStock()->orderBy('stock_quantity')->orderBy('low_stock_threshold')->orderBy('id')->limit(4)->pluck('id')->all();

    expect($lowStockList)->not->toBeEmpty()
        ->and($lowStockList)->toHaveCount(count($expectedIds))
        ->and(collect($lowStockList)->pluck('id')->all())->toBe($expectedIds);

    collect($lowStockList)->each(function (array $item): void {
        assertDashboardItemShape($item);

        $model = Item::query()->findOrFail($item['id']);
        expect($model->stock_quantity)->toBeLessThanOrEqual($model->low_stock_threshold);
    });

    $notLowStock = Item::factory()->create(['stock_quantity' => 100, 'low_stock_threshold' => 5]);

    $afterNotLowStock = $this->withHeaders($headers)->getJson('/api/dashboard/stats')
        ->assertSuccessful()
        ->json('data.low_stock_list');

    expect(collect($afterNotLowStock)->pluck('id'))->not->toContain($notLowStock->id);

    $lowStockItemId = $lowStockList[0]['id'];
    $initialCount = Item::query()->lowStock()->count();
    Item::query()->findOrFail($lowStockItemId)->update(['stock_quantity' => 99, 'low_stock_threshold' => 5]);

    $afterRaise = $this->withHeaders($headers)->getJson('/api/dashboard/stats')
        ->assertSuccessful();

    expect($afterRaise->json('data.low_stock_items'))->toBe($initialCount - 1)
        ->and(collect($afterRaise->json('data.low_stock_list'))->pluck('id'))->not->toContain($lowStockItemId);

    Item::factory()->lowStock()->count(12)->create(['stock_quantity' => 0, 'low_stock_threshold' => 5]);

    $this->withHeaders($headers)->getJson('/api/dashboard/stats')
        ->assertSuccessful()
        ->assertJsonCount(4, 'data.low_stock_list');
});

it('returns an empty low stock list when no items qualify', function () {
    $headers = dashboardSeededHeaders($this);

    Item::query()->update(['stock_quantity' => 99, 'low_stock_threshold' => 5]);

    $this->withHeaders($headers)->getJson('/api/dashboard/stats')
        ->assertSuccessful()
        ->assertJsonPath('data.low_stock_items', 0)
        ->assertJsonPath('data.low_stock_list', []);
});
