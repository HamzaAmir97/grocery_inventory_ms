<?php

use App\Models\User;
use Database\Seeders\AdminUserSeeder;

it('returns the signed-in user profile for a valid token', function () {
    $this->seed(AdminUserSeeder::class);

    $admin = User::query()->where('email', 'admin@example.com')->sole();
    $token = $this->postJson('/api/auth/login', [
        'email' => 'admin@example.com',
        'password' => 'password',
    ])->json('data.token');

    $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/auth/me')
        ->assertSuccessful()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'User retrieved.')
        ->assertJsonPath('data.id', $admin->id)
        ->assertJsonPath('data.name', 'Admin User')
        ->assertJsonPath('data.email', 'admin@example.com');
});

it('rejects missing tokens', function () {
    $this->getJson('/api/auth/me')
        ->assertUnauthorized()
        ->assertExactJson([
            'success' => false,
            'message' => 'Unauthenticated.',
        ]);
});

it('rejects invalid tokens', function () {
    $this->withHeader('Authorization', 'Bearer garbage.not.a.jwt')
        ->getJson('/api/auth/me')
        ->assertUnauthorized()
        ->assertExactJson([
            'success' => false,
            'message' => 'Unauthenticated.',
        ]);
});

it('rejects expired tokens', function () {
    $this->seed(AdminUserSeeder::class);
    config(['jwt.ttl' => 0]);

    $token = $this->postJson('/api/auth/login', [
        'email' => 'admin@example.com',
        'password' => 'password',
    ])->json('data.token');

    $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/auth/me')
        ->assertUnauthorized()
        ->assertExactJson([
            'success' => false,
            'message' => 'Unauthenticated.',
        ]);
});
