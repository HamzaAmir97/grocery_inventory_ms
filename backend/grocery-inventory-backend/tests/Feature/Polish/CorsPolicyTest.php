<?php

beforeEach(function () {
    config([
        'cors.allowed_origins' => ['http://localhost:3000'],
        'cors.allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
        'cors.allowed_headers' => ['Authorization', 'Content-Type', 'Accept'],
    ]);
});

it('allows approved dashboard origin preflight with required json api headers', function () {
    $response = $this->withHeaders([
        'Origin' => 'http://localhost:3000',
        'Access-Control-Request-Method' => 'POST',
        'Access-Control-Request-Headers' => 'Authorization, Content-Type, Accept',
    ])->options('/api/auth/login');

    $response
        ->assertSuccessful()
        ->assertHeader('Access-Control-Allow-Origin', 'http://localhost:3000');

    expect($response->headers->get('Access-Control-Allow-Headers'))->toContain('authorization')
        ->toContain('content-type')
        ->toContain('accept')
        ->and($response->headers->get('Access-Control-Allow-Methods'))->toContain('POST');
});

it('allows approved actual dashboard requests and refuses unapproved origins', function () {
    $headers = settingsAuthHeaders($this);

    $this->withHeaders([
        ...$headers,
        'Origin' => 'http://localhost:3000',
        'Accept' => 'application/json',
    ])->getJson('/api/dashboard/stats')
        ->assertSuccessful()
        ->assertHeader('Access-Control-Allow-Origin', 'http://localhost:3000');

    $unapprovedResponse = $this->withHeaders([
        'Origin' => 'http://evil.example',
        'Access-Control-Request-Method' => 'POST',
        'Access-Control-Request-Headers' => 'Authorization, Content-Type, Accept',
    ])->options('/api/auth/login');

    expect($unapprovedResponse->headers->get('Access-Control-Allow-Origin'))->not->toBe('http://evil.example');
});

it('covers api and documentation paths in the configured cors policy', function () {
    expect(config('cors.paths'))->toContain('api/*')
        ->toContain('docs')
        ->toContain('api/documentation')
        ->and(config('cors.allowed_headers'))->toContain('Authorization')
        ->toContain('Content-Type')
        ->toContain('Accept');
});

it('allows local swagger ui origins to try api requests', function () {
    config(['cors.allowed_origins' => [
        'http://localhost:3000',
        'http://localhost:8000',
        'http://127.0.0.1:8000',
    ]]);

    foreach (['http://localhost:8000', 'http://127.0.0.1:8000'] as $origin) {
        $this->withHeaders([
            'Origin' => $origin,
            'Access-Control-Request-Method' => 'POST',
            'Access-Control-Request-Headers' => 'Content-Type, Accept',
        ])->options('/api/auth/login')
            ->assertSuccessful()
            ->assertHeader('Access-Control-Allow-Origin', $origin);
    }
});

it('normalizes comma-separated configured dashboard origins', function () {
    config(['cors.allowed_origins' => array_map(
        fn (string $origin): string => rtrim(trim($origin), '/'),
        explode(',', 'http://localhost:3000/, http://dashboard.example')
    )]);

    $response = $this->withHeaders([
        'Origin' => 'http://dashboard.example',
        'Access-Control-Request-Method' => 'GET',
    ])->options('/api/dashboard/stats');

    $response->assertSuccessful()
        ->assertHeader('Access-Control-Allow-Origin', 'http://dashboard.example');
});
