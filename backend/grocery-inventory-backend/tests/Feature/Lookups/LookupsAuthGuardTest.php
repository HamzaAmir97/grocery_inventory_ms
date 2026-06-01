<?php

use Illuminate\Support\Facades\Route;

function lookupRouteUris(): array
{
    return collect(Route::getRoutes())
        ->filter(fn ($route): bool => str_starts_with($route->uri(), 'api/lookups'))
        ->map(fn ($route): string => '/'.$route->uri())
        ->sort()
        ->values()
        ->all();
}

it('refuses unauthenticated access to every lookup route', function () {
    $uris = lookupRouteUris();

    expect($uris)->toHaveCount(4);

    foreach ($uris as $uri) {
        $this->get($uri)
            ->assertUnauthorized()
            ->assertExactJson(['success' => false, 'message' => 'Unauthenticated.']);
    }
});

it('refuses logged out tokens on every lookup route', function () {
    $headers = settingsAuthHeaders($this);

    $this->withHeaders($headers)->postJson('/api/auth/logout')
        ->assertSuccessful();

    foreach (lookupRouteUris() as $uri) {
        $this->withHeaders($headers)->getJson($uri)
            ->assertUnauthorized()
            ->assertExactJson(['success' => false, 'message' => 'Unauthenticated.']);
    }
});
