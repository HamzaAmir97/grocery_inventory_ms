<?php

use App\Models\Category;
use App\Models\Item;
use App\Models\Subcategory;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

function staffAuthHeaders($test): array
{
    User::factory()->staff()->create([
        'email' => 'staff@example.com',
        'password' => 'password',
    ]);

    $token = $test->postJson('/api/auth/login', [
        'email' => 'staff@example.com',
        'password' => 'password',
    ])->json('data.token');

    return ['Authorization' => "Bearer {$token}"];
}

it('authorizes administrators and denies staff through policies', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $staff = User::factory()->staff()->create();
    $category = Category::factory()->create();
    $item = Item::factory()->create();

    expect($admin->can('viewAny', Category::class))->toBeTrue()
        ->and($admin->can('create', Item::class))->toBeTrue()
        ->and($admin->can('viewMovements', $item))->toBeTrue()
        ->and($staff->can('viewAny', Category::class))->toBeFalse()
        ->and($staff->can('create', Item::class))->toBeFalse()
        ->and($staff->can('delete', $category))->toBeFalse()
        ->and($staff->can('viewMovements', $item))->toBeFalse();
});

it('forbids staff users from resource, lookup, and dashboard operations', function () {
    $this->seed(DatabaseSeeder::class);

    $category = Category::query()->where('name', 'Dairy')->sole();
    $subcategory = Subcategory::query()->where('category_id', $category->id)->where('name', 'Milk')->sole();
    $unit = Unit::query()->where('name', 'Liter')->sole();
    $supplier = Supplier::query()->where('name', 'Daily Dairy Co')->sole();
    $item = Item::query()->firstOrFail();
    $headers = staffAuthHeaders($this);

    $cases = [
        ['GET', '/api/categories', []],
        ['POST', '/api/categories', ['name' => 'Staff Category']],
        ['GET', "/api/categories/{$category->id}", []],
        ['PUT', "/api/categories/{$category->id}", ['description' => 'Denied']],
        ['DELETE', "/api/categories/{$category->id}", []],
        ['GET', '/api/subcategories', []],
        ['POST', '/api/subcategories', ['category_id' => $category->id, 'name' => 'Staff Subcategory']],
        ['GET', "/api/subcategories/{$subcategory->id}", []],
        ['PUT', "/api/subcategories/{$subcategory->id}", ['description' => 'Denied']],
        ['DELETE', "/api/subcategories/{$subcategory->id}", []],
        ['GET', '/api/units', []],
        ['POST', '/api/units', ['name' => 'Staff Unit', 'symbol' => 'su']],
        ['GET', "/api/units/{$unit->id}", []],
        ['PUT', "/api/units/{$unit->id}", ['is_active' => false]],
        ['DELETE', "/api/units/{$unit->id}", []],
        ['GET', '/api/suppliers', []],
        ['POST', '/api/suppliers', ['name' => 'Staff Supplier']],
        ['GET', "/api/suppliers/{$supplier->id}", []],
        ['PUT', "/api/suppliers/{$supplier->id}", ['phone' => '555-0101']],
        ['DELETE', "/api/suppliers/{$supplier->id}", []],
        ['GET', '/api/items', []],
        ['POST', '/api/items', inventoryPayload(['name' => 'Staff Item', 'sku' => 'STAFF-ITEM'])],
        ['GET', "/api/items/{$item->id}", []],
        ['PUT', "/api/items/{$item->id}", ['price' => 3.25]],
        ['DELETE', "/api/items/{$item->id}", []],
        ['GET', "/api/items/{$item->id}/movements", []],
        ['GET', '/api/lookups/categories', []],
        ['GET', '/api/lookups/subcategories', []],
        ['GET', '/api/lookups/units', []],
        ['GET', '/api/lookups/suppliers', []],
        ['GET', '/api/dashboard/stats', []],
    ];

    foreach ($cases as [$method, $uri, $payload]) {
        $this->withHeaders($headers)
            ->json($method, $uri, $payload)
            ->assertForbidden()
            ->assertExactJson([
                'success' => false,
                'message' => 'This action is unauthorized.',
            ]);
    }
});
