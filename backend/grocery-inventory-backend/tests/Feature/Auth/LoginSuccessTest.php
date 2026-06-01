<?php

use App\Models\User;
use Database\Seeders\AdminUserSeeder;

it('returns a bearer token and admin profile for valid credentials', function () {
    $this->seed(AdminUserSeeder::class);

    $admin = User::query()->where('email', 'admin@example.com')->sole();

    $response = $this->postJson('/api/auth/login', [
        'email' => 'admin@example.com',
        'password' => 'password',
    ])->assertSuccessful();

    expect($response->json('success'))->toBeTrue()
        ->and($response->json('data.token'))->toBeString()->not->toBeEmpty();

    $response->assertJsonPath('data.token_type', 'Bearer')
        ->assertJsonPath('data.expires_in', 3600)
        ->assertJsonPath('data.user.id', $admin->id)
        ->assertJsonPath('data.user.name', 'Admin User')
        ->assertJsonPath('data.user.email', 'admin@example.com');
});
