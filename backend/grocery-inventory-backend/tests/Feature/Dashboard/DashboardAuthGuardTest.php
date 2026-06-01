<?php

it('refuses unauthenticated access without exposing business data', function () {
    $response = $this->get('/api/dashboard/stats')
        ->assertUnauthorized()
        ->assertExactJson(['success' => false, 'message' => 'Unauthenticated.']);

    expect($response->json())->not->toHaveKey('data');
});

it('refuses logged out tokens', function () {
    $headers = settingsAuthHeaders($this);

    $this->withHeaders($headers)->postJson('/api/auth/logout')
        ->assertSuccessful();

    $this->withHeaders($headers)->getJson('/api/dashboard/stats')
        ->assertUnauthorized()
        ->assertExactJson(['success' => false, 'message' => 'Unauthenticated.']);
});
