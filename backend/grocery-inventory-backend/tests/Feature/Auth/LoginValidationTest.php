<?php

use Database\Seeders\AdminUserSeeder;

it('returns validation errors for missing credentials', function () {
    $this->postJson('/api/auth/login')
        ->assertUnprocessable()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Validation failed.')
        ->assertJsonStructure([
            'errors' => ['email', 'password'],
        ]);
});

it('requires a password when only email is supplied', function () {
    $this->postJson('/api/auth/login', [
        'email' => 'admin@example.com',
    ])->assertUnprocessable()
        ->assertJsonPath('success', false)
        ->assertJsonStructure([
            'errors' => ['password'],
        ]);
});

it('requires a valid email address', function () {
    $this->postJson('/api/auth/login', [
        'email' => 'not-an-email',
        'password' => 'password',
    ])->assertUnprocessable()
        ->assertJsonPath('success', false)
        ->assertJsonStructure([
            'errors' => ['email'],
        ]);
});

it('normalizes whitespace and casing before authentication', function () {
    $this->seed(AdminUserSeeder::class);

    $this->postJson('/api/auth/login', [
        'email' => '  ADMIN@example.COM ',
        'password' => 'password',
    ])->assertSuccessful();
});
