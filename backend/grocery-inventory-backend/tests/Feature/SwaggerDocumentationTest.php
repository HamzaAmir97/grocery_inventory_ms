<?php

use Illuminate\Support\Facades\Artisan;

function ensureOpenApiDocumentExists(): void
{
    if (! file_exists(storage_path('api-docs/api-docs.json'))) {
        Artisan::call('l5-swagger:generate');
    }
}

function openApiDocument(): array
{
    ensureOpenApiDocumentExists();

    return json_decode(file_get_contents(storage_path('api-docs/api-docs.json')), true, flags: JSON_THROW_ON_ERROR);
}

test('the swagger documentation page renders the configured api title', function () {
    ensureOpenApiDocumentExists();

    $this->get('/api/documentation')
        ->assertSuccessful()
        ->assertSee('Grocery Inventory Management API');
});

test('the docs endpoint returns a valid openapi document', function () {
    ensureOpenApiDocumentExists();

    $response = $this->get('/docs')
        ->assertSuccessful()
        ->assertJsonPath('info.title', 'Grocery Inventory Management API');

    expect($response->json('openapi'))->toStartWith('3.');
});

test('the openapi document exposes bearer jwt authentication', function () {
    $securityScheme = openApiDocument()['components']['securitySchemes']['bearerAuth'] ?? null;

    expect($securityScheme)->toMatchArray([
        'type' => 'http',
        'scheme' => 'bearer',
        'bearerFormat' => 'JWT',
    ]);
});

test('the openapi document exposes a configurable server url', function () {
    $serverUrl = openApiDocument()['servers'][0]['url'] ?? '';

    expect($serverUrl)->toBeString()->not->toBeEmpty();
});

it('contains the :schema reusable schema', function (string $schema) {
    expect(openApiDocument()['components']['schemas'] ?? [])->toHaveKey($schema);
})->with([
    'LoginRequest',
    'LoginResponse',
    'ValidationErrorResponse',
    'UnauthorizedResponse',
    'ConflictResponse',
    'NotFoundResponse',
    'MethodNotAllowedResponse',
    'ServerErrorResponse',
    'Category',
    'Subcategory',
    'Unit',
    'Supplier',
    'Item',
    'DashboardStats',
    'DashboardItem',
    'PaginationMeta',
    'CategoryLookup',
    'SubcategoryLookup',
    'UnitLookup',
    'SupplierLookup',
    'ItemUpsertRequest',
]);

it('documents the lookup endpoints', function () {
    $document = openApiDocument();

    foreach (['/api/lookups/categories', '/api/lookups/subcategories', '/api/lookups/units', '/api/lookups/suppliers'] as $path) {
        expect($document['paths'][$path]['get'] ?? null)->toBeArray()
            ->and($document['paths'][$path]['get']['tags'])->toContain('Lookups')
            ->and($document['paths'][$path]['get']['security'])->toBe([['bearerAuth' => []]]);
    }

    expect($document['paths']['/api/lookups/subcategories']['get']['parameters'][0]['name'] ?? null)->toBe('category_id');
});

it('documents the inventory item endpoints', function () {
    $document = openApiDocument();

    foreach (['get', 'post'] as $method) {
        expect($document['paths']['/api/items'][$method] ?? null)->toBeArray()
            ->and($document['paths']['/api/items'][$method]['tags'])->toContain('Items')
            ->and($document['paths']['/api/items'][$method]['security'])->toBe([['bearerAuth' => []]]);
    }

    foreach (['get', 'put', 'delete'] as $method) {
        expect($document['paths']['/api/items/{item}'][$method] ?? null)->toBeArray()
            ->and($document['paths']['/api/items/{item}'][$method]['tags'])->toContain('Items')
            ->and($document['paths']['/api/items/{item}'][$method]['security'])->toBe([['bearerAuth' => []]]);
    }

    $parameters = collect($document['paths']['/api/items']['get']['parameters'] ?? [])->pluck('name')->all();

    expect($parameters)->toBe([
        'search',
        'category_id',
        'subcategory_id',
        'unit_id',
        'supplier_id',
        'low_stock',
        'sort_by',
        'sort_dir',
        'page',
        'per_page',
    ]);
});

it('documents the dashboard endpoint', function () {
    $document = openApiDocument();
    $operation = $document['paths']['/api/dashboard/stats']['get'] ?? null;

    expect($operation)->toBeArray()
        ->and($operation['tags'])->toContain('Dashboard')
        ->and($operation['security'])->toBe([['bearerAuth' => []]])
        ->and($operation['responses']['200']['content']['application/json']['schema']['properties']['data']['$ref'] ?? null)->toBe('#/components/schemas/DashboardStats')
        ->and($document['components']['schemas']['DashboardStats']['properties']['summary_cards']['items']['$ref'] ?? null)->toBe('#/components/schemas/DashboardSummaryCard')
        ->and($document['components']['schemas']['DashboardStats']['properties']['inventory_growth']['items']['$ref'] ?? null)->toBe('#/components/schemas/DashboardGrowthPoint')
        ->and($document['components']['schemas']['DashboardStats']['properties']['category_breakdown']['$ref'] ?? null)->toBe('#/components/schemas/DashboardCategoryBreakdown')
        ->and($document['components']['schemas']['DashboardStats']['properties']['recent_items']['items']['$ref'] ?? null)->toBe('#/components/schemas/DashboardItem')
        ->and($document['components']['schemas']['DashboardStats']['properties']['low_stock_list']['items']['$ref'] ?? null)->toBe('#/components/schemas/DashboardItem');
});

test('the openapi document can be regenerated on demand', function () {
    $path = storage_path('api-docs/api-docs.json');

    if (file_exists($path)) {
        unlink($path);
    }

    expect(Artisan::call('l5-swagger:generate'))->toBe(0)
        ->and(file_exists($path))->toBeTrue()
        ->and(json_decode(file_get_contents($path), true))->toBeArray();
});
