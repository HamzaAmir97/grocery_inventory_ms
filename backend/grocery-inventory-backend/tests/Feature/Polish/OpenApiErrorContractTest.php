<?php

use Illuminate\Support\Facades\Artisan;

function polishOpenApiDocument(): array
{
    Artisan::call('l5-swagger:generate');

    return json_decode(file_get_contents(storage_path('api-docs/api-docs.json')), true, flags: JSON_THROW_ON_ERROR);
}

it('exposes reusable schemas for every standard failure family', function () {
    $schemas = polishOpenApiDocument()['components']['schemas'] ?? [];

    expect($schemas)->toHaveKeys([
        'ValidationErrorResponse',
        'UnauthorizedResponse',
        'ConflictResponse',
        'NotFoundResponse',
        'MethodNotAllowedResponse',
        'ServerErrorResponse',
    ]);
});

it('documents bearer security and failure responses on protected operations', function (string $path, string $method, array $responses) {
    $operation = polishOpenApiDocument()['paths'][$path][$method] ?? null;

    expect($operation)->toBeArray()
        ->and($operation['security'])->toBe([['bearerAuth' => []]]);

    foreach ($responses as $status => $schema) {
        expect($operation['responses'][(string) $status]['content']['application/json']['schema']['$ref'] ?? null)
            ->toBe("#/components/schemas/{$schema}");
    }
})->with([
    'categories index' => ['/api/categories', 'get', [401 => 'UnauthorizedResponse', 500 => 'ServerErrorResponse']],
    'categories store' => ['/api/categories', 'post', [401 => 'UnauthorizedResponse', 422 => 'ValidationErrorResponse', 500 => 'ServerErrorResponse']],
    'categories show' => ['/api/categories/{category}', 'get', [401 => 'UnauthorizedResponse', 404 => 'NotFoundResponse', 500 => 'ServerErrorResponse']],
    'categories update' => ['/api/categories/{category}', 'put', [401 => 'UnauthorizedResponse', 404 => 'NotFoundResponse', 422 => 'ValidationErrorResponse', 500 => 'ServerErrorResponse']],
    'categories delete' => ['/api/categories/{category}', 'delete', [401 => 'UnauthorizedResponse', 404 => 'NotFoundResponse', 409 => 'ConflictResponse', 500 => 'ServerErrorResponse']],
    'items store' => ['/api/items', 'post', [401 => 'UnauthorizedResponse', 422 => 'ValidationErrorResponse', 500 => 'ServerErrorResponse']],
    'items show' => ['/api/items/{item}', 'get', [401 => 'UnauthorizedResponse', 404 => 'NotFoundResponse', 500 => 'ServerErrorResponse']],
    'dashboard stats' => ['/api/dashboard/stats', 'get', [401 => 'UnauthorizedResponse', 500 => 'ServerErrorResponse']],
    'lookup categories' => ['/api/lookups/categories', 'get', [401 => 'UnauthorizedResponse', 500 => 'ServerErrorResponse']],
]);

it('documents validation and server failure on the login operation', function () {
    $operation = polishOpenApiDocument()['paths']['/api/auth/login']['post'] ?? null;

    expect($operation)->toBeArray()
        ->and($operation['responses']['422']['content']['application/json']['schema']['$ref'] ?? null)->toBe('#/components/schemas/ValidationErrorResponse')
        ->and($operation['responses']['401']['content']['application/json']['schema']['$ref'] ?? null)->toBe('#/components/schemas/UnauthorizedResponse')
        ->and($operation['responses']['500']['content']['application/json']['schema']['$ref'] ?? null)->toBe('#/components/schemas/ServerErrorResponse');
});
