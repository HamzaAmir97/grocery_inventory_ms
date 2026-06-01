<?php

use Database\Seeders\AdminUserSeeder;

it('invalidates the current token', function () {
    $this->seed(AdminUserSeeder::class);

    $token = $this->postJson('/api/auth/login', [
        'email' => 'admin@example.com',
        'password' => 'password',
    ])->json('data.token');

    $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/auth/logout')
        ->assertSuccessful()
        ->assertExactJson([
            'success' => true,
            'message' => 'Successfully signed out.',
        ]);

    $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/auth/me')
        ->assertUnauthorized()
        ->assertExactJson([
            'success' => false,
            'message' => 'Unauthenticated.',
        ]);
});

it('requires a token to sign out', function () {
    $this->postJson('/api/auth/logout')
        ->assertUnauthorized()
        ->assertExactJson([
            'success' => false,
            'message' => 'Unauthenticated.',
        ]);
});
