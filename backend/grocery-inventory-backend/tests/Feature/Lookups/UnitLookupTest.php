<?php

use App\Models\Unit;

beforeEach(function () {
    $this->headers = settingsAuthHeaders($this);
});

it('returns active units as slim alphabetized lookup records', function () {
    $response = $this->withHeaders($this->headers)->getJson('/api/lookups/units')
        ->assertSuccessful()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Units lookups retrieved.')
        ->assertJsonMissingPath('meta');

    $data = $response->json('data');

    expect($data)->toHaveCount(4);

    collect($data)->each(function (array $unit): void {
        expect(array_keys($unit))->toBe(['id', 'name', 'symbol']);
    });

    $names = collect($data)->pluck('name')->all();

    expect($names)->toBe(collect($names)->sort()->values()->all());
});

it('excludes inactive units until they are reactivated', function () {
    $unit = Unit::query()->where('name', 'Liter')->sole();

    $unit->update(['is_active' => false]);

    $this->withHeaders($this->headers)->getJson('/api/lookups/units')
        ->assertSuccessful()
        ->assertJsonMissing(['name' => 'Liter']);

    $unit->update(['is_active' => true]);

    $this->withHeaders($this->headers)->getJson('/api/lookups/units')
        ->assertSuccessful()
        ->assertJsonFragment(['name' => 'Liter']);
});
