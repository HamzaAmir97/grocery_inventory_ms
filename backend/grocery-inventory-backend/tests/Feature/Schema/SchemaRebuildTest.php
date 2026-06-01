<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

it('creates the :table table', function (string $table) {
    expect(Schema::hasTable($table))->toBeTrue();
})->with([
    'categories',
    'subcategories',
    'units',
    'suppliers',
    'items',
]);

it('rebuilds the schema deterministically', function () {
    expect(Artisan::call('migrate:fresh'))->toBe(0)
        ->and(Artisan::call('migrate:fresh'))->toBe(0);
});
