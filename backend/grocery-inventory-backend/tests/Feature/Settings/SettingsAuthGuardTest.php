<?php

use Database\Seeders\DatabaseSeeder;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Route;

function settingsRouteCases(): array
{
    return collect(Route::getRoutes())
        ->filter(fn (RoutingRoute $route) => preg_match('#^api/(categories|subcategories|units|suppliers)#', $route->uri()) === 1)
        ->flatMap(function (RoutingRoute $route): array {
            return collect($route->methods())
                ->reject(fn (string $method) => $method === 'HEAD')
                ->mapWithKeys(fn (string $method): array => [
                    "{$method} {$route->uri()}" => [$method, settingsConcreteUri($route->uri())],
                ])
                ->all();
        })
        ->all();
}

function settingsConcreteUri(string $uri): string
{
    return '/'.str_replace(
        ['{category}', '{subcategory}', '{unit}', '{supplier}'],
        ['1', '1', '1', '1'],
        $uri
    );
}

it('rejects every settings route without a token', function () {
    $this->seed(DatabaseSeeder::class);

    foreach (settingsRouteCases() as [$method, $uri]) {
        $this->json($method, $uri)
            ->assertUnauthorized()
            ->assertExactJson([
                'success' => false,
                'message' => 'Unauthenticated.',
            ]);
    }
});

it('rejects every settings route with a logged out token', function () {
    $headers = settingsAuthHeaders($this);

    $this->withHeaders($headers)->postJson('/api/auth/logout')->assertSuccessful();

    foreach (settingsRouteCases() as [$method, $uri]) {
        $this->withHeaders($headers)
            ->json($method, $uri)
            ->assertUnauthorized()
            ->assertExactJson([
                'success' => false,
                'message' => 'Unauthenticated.',
            ]);
    }
});
