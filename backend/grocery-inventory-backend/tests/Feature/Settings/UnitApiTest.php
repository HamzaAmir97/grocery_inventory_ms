<?php

use App\Models\Item;
use App\Models\Unit;

beforeEach(function () {
    $this->headers = settingsAuthHeaders($this);
});

it('lists, paginates, creates, shows, updates, and deletes units', function () {
    $this->withHeaders($this->headers)->getJson('/api/units')->assertSuccessful()->assertJsonPath('meta.total', 4);
    $this->withHeaders($this->headers)->getJson('/api/units?per_page=3')->assertSuccessful()->assertJsonCount(3, 'data')->assertJsonPath('meta.per_page', 3);
    $this->withHeaders($this->headers)->getJson('/api/units?per_page=999')->assertUnprocessable()->assertJsonValidationErrors('per_page');
    $this->withHeaders($this->headers)->getJson('/api/units?page=9999')->assertSuccessful()->assertJsonCount(0, 'data');

    $unitId = $this->withHeaders($this->headers)->postJson('/api/units', ['name' => 'Box', 'symbol' => 'box'])->assertCreated()->json('data.id');

    $this->withHeaders($this->headers)->getJson("/api/units/{$unitId}")->assertSuccessful()->assertJsonPath('data.symbol', 'box');
    $this->withHeaders($this->headers)->putJson("/api/units/{$unitId}", ['name' => 'Carton', 'symbol' => 'ctn'])->assertSuccessful()->assertJsonPath('data.name', 'Carton');
    $this->withHeaders($this->headers)->putJson("/api/units/{$unitId}", ['name' => 'Carton', 'symbol' => 'ctn'])->assertSuccessful();
    $this->withHeaders($this->headers)->deleteJson("/api/units/{$unitId}")->assertSuccessful()->assertExactJson(['success' => true, 'message' => 'Unit deleted successfully.']);
});

it('validates unit writes', function () {
    $kilogram = Unit::query()->where('name', 'Kilogram')->sole();
    $liter = Unit::query()->where('name', 'Liter')->sole();

    $this->withHeaders($this->headers)->postJson('/api/units', ['name' => 'Kilogram', 'symbol' => 'kg-new'])->assertUnprocessable()->assertJsonStructure(['errors' => ['name']]);
    $this->withHeaders($this->headers)->postJson('/api/units', ['name' => 'Gram', 'symbol' => 'kg'])->assertUnprocessable()->assertJsonStructure(['errors' => ['symbol']]);
    $this->withHeaders($this->headers)->postJson('/api/units', ['name' => 'Kilogram', 'symbol' => 'kg'])->assertUnprocessable()->assertJsonStructure(['errors' => ['name', 'symbol']]);
    $this->withHeaders($this->headers)->postJson('/api/units', ['name' => 'Very Long Unit', 'symbol' => str_repeat('a', 51)])->assertUnprocessable()->assertJsonStructure(['errors' => ['symbol']]);
    $this->withHeaders($this->headers)->putJson("/api/units/{$kilogram->id}", ['name' => $kilogram->name, 'symbol' => $kilogram->symbol])->assertSuccessful();
    $this->withHeaders($this->headers)->putJson("/api/units/{$kilogram->id}", ['name' => $liter->name, 'symbol' => $kilogram->symbol])->assertUnprocessable()->assertJsonStructure(['errors' => ['name']]);
});

it('searches unit symbols and sorts unit indexes', function () {
    Unit::factory()->create(['name' => 'Sort Unit Alpha', 'symbol' => 'sua']);
    Unit::factory()->create(['name' => 'Sort Unit Beta', 'symbol' => 'sub']);

    $data = $this->withHeaders($this->headers)
        ->getJson('/api/units?search=su&sort_by=symbol&sort_dir=desc&per_page=10')
        ->assertSuccessful()
        ->json('data');

    expect(array_column($data, 'symbol'))->toBe(['sub', 'sua']);
});

it('refuses unit deletes with items and returns not found for unknown ids', function () {
    $unit = Unit::factory()->create();
    Item::factory()->create(['unit_id' => $unit->id]);

    $this->withHeaders($this->headers)->deleteJson("/api/units/{$unit->id}")
        ->assertConflict()
        ->assertExactJson(['success' => false, 'message' => 'This unit still has items.']);

    expect(Unit::query()->whereKey($unit->id)->exists())->toBeTrue();

    $this->withHeaders($this->headers)->deleteJson('/api/units/999999')
        ->assertNotFound()
        ->assertExactJson(['success' => false, 'message' => 'Unit not found.']);
});
