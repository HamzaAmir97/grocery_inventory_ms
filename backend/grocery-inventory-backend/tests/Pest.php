<?php

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Supplier;
use App\Models\Unit;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}

function settingsAuthHeaders(TestCase $test): array
{
    $test->seed(DatabaseSeeder::class);

    $token = $test->postJson('/api/auth/login', [
        'email' => 'admin@example.com',
        'password' => 'password',
    ])->json('data.token');

    return ['Authorization' => "Bearer {$token}"];
}

function inventoryPayload(array $overrides = []): array
{
    $category = Category::query()->where('name', 'Dairy')->sole();
    $subcategory = Subcategory::query()->where('category_id', $category->id)->where('name', 'Milk')->sole();
    $unit = Unit::query()->where('name', 'Liter')->sole();
    $supplier = Supplier::query()->where('name', 'Daily Dairy Co')->sole();

    return array_merge([
        'name' => 'Test Whole Milk 1L',
        'sku' => 'TEST-MILK-1L',
        'category_id' => $category->id,
        'subcategory_id' => $subcategory->id,
        'unit_id' => $unit->id,
        'supplier_id' => $supplier->id,
        'price' => 2.49,
        'stock_quantity' => 25,
        'low_stock_threshold' => 5,
        'description' => 'Test inventory item.',
        'is_active' => true,
    ], $overrides);
}
