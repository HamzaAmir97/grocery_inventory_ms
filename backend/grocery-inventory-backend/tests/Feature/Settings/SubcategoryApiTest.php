<?php

use App\Models\Category;
use App\Models\Item;
use App\Models\Subcategory;

beforeEach(function () {
    $this->headers = settingsAuthHeaders($this);
});

it('lists, searches, paginates, creates, shows, updates, and deletes subcategories', function () {
    $dairy = Category::query()->where('name', 'Dairy')->sole();

    $this->withHeaders($this->headers)->getJson('/api/subcategories')->assertSuccessful()->assertJsonPath('meta.total', 15);
    $this->withHeaders($this->headers)->getJson('/api/subcategories?per_page=3')->assertSuccessful()->assertJsonCount(3, 'data')->assertJsonPath('meta.per_page', 3);
    $this->withHeaders($this->headers)->getJson('/api/subcategories?search=Milk')->assertSuccessful()->assertJsonCount(1, 'data')->assertJsonPath('data.0.name', 'Milk');
    $this->withHeaders($this->headers)->getJson('/api/subcategories?page=9999')->assertSuccessful()->assertJsonCount(0, 'data');

    $subcategoryId = $this->withHeaders($this->headers)->postJson('/api/subcategories', [
        'category_id' => $dairy->id,
        'name' => 'Cream',
    ])->assertCreated()->json('data.id');

    $this->withHeaders($this->headers)->getJson("/api/subcategories/{$subcategoryId}")->assertSuccessful()->assertJsonPath('data.name', 'Cream');
    $this->withHeaders($this->headers)->putJson("/api/subcategories/{$subcategoryId}", ['category_id' => $dairy->id, 'name' => 'Creamers'])->assertSuccessful()->assertJsonPath('data.name', 'Creamers');
    $this->withHeaders($this->headers)->putJson("/api/subcategories/{$subcategoryId}", ['category_id' => $dairy->id, 'name' => 'Creamers'])->assertSuccessful();
    $this->withHeaders($this->headers)->deleteJson("/api/subcategories/{$subcategoryId}")->assertSuccessful()->assertExactJson(['success' => true, 'message' => 'Subcategory deleted successfully.']);
});

it('validates subcategory writes', function () {
    $dairy = Category::query()->where('name', 'Dairy')->sole();
    $bakery = Category::query()->where('name', 'Bakery')->sole();

    $this->withHeaders($this->headers)->postJson('/api/subcategories', ['category_id' => 999999, 'name' => 'Ghost'])->assertUnprocessable()->assertJsonStructure(['errors' => ['category_id']]);
    $this->withHeaders($this->headers)->postJson('/api/subcategories', ['name' => 'No Parent'])->assertUnprocessable()->assertJsonStructure(['errors' => ['category_id']]);
    $this->withHeaders($this->headers)->postJson('/api/subcategories', ['category_id' => $dairy->id, 'name' => 'Milk'])->assertUnprocessable()->assertJsonStructure(['errors' => ['name']]);
    $this->withHeaders($this->headers)->postJson('/api/subcategories', ['category_id' => $bakery->id, 'name' => 'Milk'])->assertCreated();

    $dairyScoped = Subcategory::factory()->create(['category_id' => $dairy->id, 'name' => 'Scoped']);
    Subcategory::factory()->create(['category_id' => $bakery->id, 'name' => 'Scoped']);

    $this->withHeaders($this->headers)->putJson("/api/subcategories/{$dairyScoped->id}", [
        'category_id' => $bakery->id,
        'name' => 'Scoped',
    ])->assertUnprocessable()->assertJsonStructure(['errors' => ['name']]);
});

it('sorts subcategory indexes by parent category', function () {
    $alpha = Category::factory()->create(['name' => 'Sort Parent Alpha']);
    $zulu = Category::factory()->create(['name' => 'Sort Parent Zulu']);
    Subcategory::factory()->create(['category_id' => $alpha->id, 'name' => 'Scoped Alpha']);
    Subcategory::factory()->create(['category_id' => $zulu->id, 'name' => 'Scoped Zulu']);

    $data = $this->withHeaders($this->headers)
        ->getJson('/api/subcategories?search=Sort%20Parent&sort_by=category&sort_dir=desc&per_page=10')
        ->assertSuccessful()
        ->json('data');

    expect(array_map(fn (array $row): string => $row['category']['name'], $data))
        ->toBe(['Sort Parent Zulu', 'Sort Parent Alpha']);
});

it('refuses subcategory deletes with items and returns not found for unknown ids', function () {
    $subcategory = Subcategory::factory()->create();
    Item::factory()->create(['category_id' => $subcategory->category_id, 'subcategory_id' => $subcategory->id]);

    $this->withHeaders($this->headers)->deleteJson("/api/subcategories/{$subcategory->id}")
        ->assertConflict()
        ->assertExactJson(['success' => false, 'message' => 'This subcategory still has items.']);

    expect(Subcategory::query()->whereKey($subcategory->id)->exists())->toBeTrue();

    $this->withHeaders($this->headers)->deleteJson('/api/subcategories/999999')
        ->assertNotFound()
        ->assertExactJson(['success' => false, 'message' => 'Subcategory not found.']);
});
