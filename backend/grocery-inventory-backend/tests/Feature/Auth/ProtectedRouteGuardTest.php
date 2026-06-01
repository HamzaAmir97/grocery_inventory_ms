<?php

use Illuminate\Support\Facades\Route;

it('requires authentication for every protected api route', function () {
    $protectedRoutes = collect(Route::getRoutes())
        ->filter(fn ($route) => in_array('auth:api', $route->gatherMiddleware(), true));

    expect($protectedRoutes)->not->toBeEmpty();

    $protectedRoutes->each(function ($route): void {
        $method = collect($route->methods())
            ->reject(fn (string $method) => $method === 'HEAD')
            ->first();

        $this->json($method, '/'.$route->uri())
            ->assertUnauthorized()
            ->assertExactJson([
                'success' => false,
                'message' => 'Unauthenticated.',
            ]);
    });
});
