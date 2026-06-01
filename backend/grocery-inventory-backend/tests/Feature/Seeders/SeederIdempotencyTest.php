<?php

use App\Models\Category;
use App\Models\Item;
use App\Models\Subcategory;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

it('produces identical counts across repeated fresh seed cycles', function () {
    $snapshots = [];

    for ($iteration = 0; $iteration < 5; $iteration++) {
        Artisan::call('migrate:fresh', ['--seed' => true]);

        $snapshots[] = [
            User::query()->count(),
            Category::query()->count(),
            Subcategory::query()->count(),
            Unit::query()->count(),
            Supplier::query()->count(),
            Item::query()->count(),
        ];
    }

    foreach ($snapshots as $snapshot) {
        expect($snapshot)->toBe($snapshots[0]);
    }
});

it('rolls back partially seeded data when a seed step fails', function () {
    Artisan::call('migrate:fresh');

    try {
        DB::transaction(function (): void {
            (new AdminUserSeeder)->run();

            User::factory()->create(['email' => null]);
        });
    } catch (Throwable) {
        // The assertion below verifies the transaction rollback.
    }

    expect(User::query()->count())->toBe(0);
});
