<?php

use Illuminate\Support\Facades\Route;

function inventoryRouteProbes(): array
{
    return collect(Route::getRoutes())
        ->filter(fn ($route): bool => str_starts_with($route->uri(), 'api/items'))
        ->map(function ($route): array {
            $method = collect($route->methods())->reject(fn (string $method): bool => $method === 'HEAD')->first();
            $uri = '/'.str_replace('{item}', '1', $route->uri());

            return [$method, $uri];
        })
        ->sortBy(fn (array $probe): string => $probe[0].$probe[1])
        ->values()
        ->all();
}

it('refuses unauthenticated access to every item route', function () {
    $probes = inventoryRouteProbes();

    expect($probes)->toHaveCount(6);

    foreach ($probes as [$method, $uri]) {
        $this->json($method, $uri)
            ->assertUnauthorized()
            ->assertExactJson(['success' => false, 'message' => 'Unauthenticated.']);
    }
});

it('refuses logged out tokens on every item route', function () {
    $headers = settingsAuthHeaders($this);

    $this->withHeaders($headers)->postJson('/api/auth/logout')
        ->assertSuccessful();

    foreach (inventoryRouteProbes() as [$method, $uri]) {
        $this->withHeaders($headers)->json($method, $uri)
            ->assertUnauthorized()
            ->assertExactJson(['success' => false, 'message' => 'Unauthenticated.']);
    }
});
