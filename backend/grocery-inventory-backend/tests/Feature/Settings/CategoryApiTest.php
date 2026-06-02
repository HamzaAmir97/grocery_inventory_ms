<?php

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->headers = settingsAuthHeaders($this);
});

it('lists, searches, paginates, creates, shows, updates, and deletes categories', function () {
    $this->withHeaders($this->headers)->getJson('/api/categories')
        ->assertSuccessful()
        ->assertJsonPath('success', true)
        ->assertJsonPath('meta.total', 5);

    $this->withHeaders($this->headers)->getJson('/api/categories?per_page=3')
        ->assertSuccessful()
        ->assertJsonCount(3, 'data')
        ->assertJsonPath('meta.per_page', 3);

    $this->withHeaders($this->headers)->getJson('/api/categories?per_page=999')
        ->assertUnprocessable()
        ->assertJsonValidationErrors('per_page');

    $this->withHeaders($this->headers)->getJson('/api/categories?search=Dair')
        ->assertSuccessful()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Dairy');

    $this->withHeaders($this->headers)->getJson('/api/categories?search=%20%20')
        ->assertSuccessful()
        ->assertJsonPath('meta.total', 5);

    $this->withHeaders($this->headers)->getJson('/api/categories?page=9999')
        ->assertSuccessful()
        ->assertJsonCount(0, 'data');

    $categoryId = $this->withHeaders($this->headers)->postJson('/api/categories', [
        'name' => 'Frozen',
        'description' => 'Frozen goods',
    ])->assertCreated()
        ->assertJsonPath('message', 'Category created successfully.')
        ->json('data.id');

    $this->withHeaders($this->headers)->getJson("/api/categories/{$categoryId}")
        ->assertSuccessful()
        ->assertJsonPath('data.name', 'Frozen');

    $this->withHeaders($this->headers)->putJson("/api/categories/{$categoryId}", [
        'name' => 'Frozen Foods',
        'description' => 'Frozen foods and meals',
    ])->assertSuccessful()
        ->assertJsonPath('data.name', 'Frozen Foods');

    $this->withHeaders($this->headers)->putJson("/api/categories/{$categoryId}", [
        'name' => 'Frozen Foods',
    ])->assertSuccessful();

    $this->withHeaders($this->headers)->deleteJson("/api/categories/{$categoryId}")
        ->assertSuccessful()
        ->assertExactJson(['success' => true, 'message' => 'Category deleted successfully.']);
});

it('validates category writes and ignores unknown fields', function () {
    $this->withHeaders($this->headers)->postJson('/api/categories')->assertUnprocessable()->assertJsonStructure(['errors' => ['name']]);
    $this->withHeaders($this->headers)->postJson('/api/categories', ['name' => ''])->assertUnprocessable()->assertJsonStructure(['errors' => ['name']]);
    $this->withHeaders($this->headers)->postJson('/api/categories', ['name' => 'Dairy'])->assertUnprocessable()->assertJsonStructure(['errors' => ['name']]);

    $dairy = Category::query()->where('name', 'Dairy')->sole();
    $bakery = Category::query()->where('name', 'Bakery')->sole();

    $this->withHeaders($this->headers)->putJson("/api/categories/{$dairy->id}", ['name' => 'Bakery'])->assertUnprocessable()->assertJsonStructure(['errors' => ['name']]);
    $this->withHeaders($this->headers)->putJson("/api/categories/{$bakery->id}", ['name' => 'Bakery'])->assertSuccessful();

    $createdId = $this->withHeaders($this->headers)->postJson('/api/categories', [
        'name' => 'Pantry',
        'extra_field' => 'ignored',
    ])->assertCreated()->json('data.id');

    expect(Category::query()->findOrFail($createdId)->getAttributes())->not->toHaveKey('extra_field');

    $this->withHeaders($this->headers)->postJson('/api/categories', ['name' => str_repeat('a', 256)])->assertUnprocessable()->assertJsonStructure(['errors' => ['name']]);
});

it('sorts category indexes and validates supported sort fields', function () {
    Category::factory()->create(['name' => 'Sort Test Alpha']);
    Category::factory()->create(['name' => 'Sort Test Beta']);

    $data = $this->withHeaders($this->headers)
        ->getJson('/api/categories?search=Sort%20Test&sort_by=name&sort_dir=desc&per_page=10')
        ->assertSuccessful()
        ->json('data');

    expect(array_column($data, 'name'))->toBe(['Sort Test Beta', 'Sort Test Alpha']);

    $this->withHeaders($this->headers)
        ->getJson('/api/categories?sort_by=not_allowed')
        ->assertUnprocessable()
        ->assertJsonStructure(['errors' => ['sort_by']]);

    $this->withHeaders($this->headers)
        ->getJson('/api/categories?sort_by=phone')
        ->assertUnprocessable()
        ->assertJsonStructure(['errors' => ['sort_by']]);
});

it('refuses category deletes with dependents and returns not found for unknown ids', function () {
    $dairy = Category::query()->where('name', 'Dairy')->sole();

    $this->withHeaders($this->headers)->deleteJson("/api/categories/{$dairy->id}")
        ->assertConflict()
        ->assertExactJson(['success' => false, 'message' => 'This category still has subcategories.']);

    $category = Category::factory()->create(['name' => 'Item Only Category']);
    $otherCategory = Category::factory()->create();
    $otherSubcategory = Subcategory::factory()->create(['category_id' => $otherCategory->id]);
    $unit = Unit::factory()->create();
    $supplier = Supplier::factory()->create();

    DB::table('items')->insert([
        'name' => 'Orphan pairing item',
        'sku' => null,
        'category_id' => $category->id,
        'subcategory_id' => $otherSubcategory->id,
        'unit_id' => $unit->id,
        'supplier_id' => $supplier->id,
        'price' => 1,
        'stock_quantity' => 1,
        'low_stock_threshold' => 1,
        'description' => null,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->withHeaders($this->headers)->deleteJson("/api/categories/{$category->id}")
        ->assertConflict()
        ->assertExactJson(['success' => false, 'message' => 'This category still has items.']);

    expect(Category::query()->whereKey($dairy->id)->exists())->toBeTrue()
        ->and(Category::query()->whereKey($category->id)->exists())->toBeTrue();

    $this->withHeaders($this->headers)->deleteJson('/api/categories/999999')
        ->assertNotFound()
        ->assertExactJson(['success' => false, 'message' => 'Category not found.']);
});
