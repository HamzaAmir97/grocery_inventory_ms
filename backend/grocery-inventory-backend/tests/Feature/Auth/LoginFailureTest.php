<?php

use Database\Seeders\AdminUserSeeder;

it('returns an identical response for wrong passwords and unknown emails', function () {
    $this->seed(AdminUserSeeder::class);

    $wrongPasswordResponse = $this->postJson('/api/auth/login', [
        'email' => 'admin@example.com',
        'password' => 'wrong-password',
    ])->assertUnauthorized()
        ->assertExactJson([
            'success' => false,
            'message' => 'Invalid credentials.',
        ]);

    $unknownEmailResponse = $this->postJson('/api/auth/login', [
        'email' => 'unknown@example.com',
        'password' => 'password',
    ])->assertUnauthorized()
        ->assertExactJson([
            'success' => false,
            'message' => 'Invalid credentials.',
        ]);

    expect($unknownEmailResponse->getContent())->toBe($wrongPasswordResponse->getContent())
        ->and($wrongPasswordResponse->json())->not->toHaveKeys(['data', 'errors'])
        ->and($unknownEmailResponse->json())->not->toHaveKeys(['data', 'errors']);
});
