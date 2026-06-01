<?php

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

it('seeds the documented admin user', function () {
    Artisan::call('db:seed', ['--class' => AdminUserSeeder::class]);

    $admin = User::query()->where('email', 'admin@example.com')->sole();

    expect(User::query()->where('email', 'admin@example.com')->count())->toBe(1)
        ->and($admin->name)->toBe('Admin User')
        ->and(Hash::check('password', $admin->password))->toBeTrue()
        ->and($admin->password)->not->toBe('password');
});
