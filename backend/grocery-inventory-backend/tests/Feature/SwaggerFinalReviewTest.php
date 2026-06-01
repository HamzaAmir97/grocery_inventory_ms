<?php

use Illuminate\Support\Facades\Artisan;

function swaggerFinalReviewDocument(bool $regenerate = false): array
{
    static $document = null;

    if ($regenerate || $document === null) {
        Artisan::call('l5-swagger:generate');

        $document = json_decode(
            file_get_contents(storage_path('api-docs/api-docs.json')),
            true,
            flags: JSON_THROW_ON_ERROR
        );
    }

    return $document;
}

function swaggerFinalReviewExpectedOperations(): array
{
    $settingsIndexParameters = ['search', 'sort_by', 'sort_dir', 'page', 'per_page'];
    $unitIndexParameters = ['search', 'sort_by', 'sort_dir', 'page', 'per_page'];
    $itemIndexParameters = ['search', 'category_id', 'subcategory_id', 'unit_id', 'supplier_id', 'low_stock', 'sort_by', 'sort_dir', 'page', 'per_page'];

    return [
        ['method' => 'post', 'path' => '/api/auth/login', 'tag' => 'Auth', 'protected' => false, 'success' => 200, 'request' => '#/components/schemas/LoginRequest', 'parameters' => [], 'data' => '#/components/schemas/LoginResponse', 'failures' => [401 => 'UnauthorizedResponse', 405 => 'MethodNotAllowedResponse', 422 => 'ValidationErrorResponse', 500 => 'ServerErrorResponse']],
        ['method' => 'get', 'path' => '/api/status', 'tag' => 'Status', 'protected' => false, 'success' => 200, 'parameters' => [], 'data' => '#/components/schemas/StatusReport', 'failures' => [500 => 'ServerErrorResponse']],
        ['method' => 'post', 'path' => '/api/auth/logout', 'tag' => 'Auth', 'protected' => true, 'success' => 200, 'parameters' => [], 'failures' => [401 => 'UnauthorizedResponse', 405 => 'MethodNotAllowedResponse', 500 => 'ServerErrorResponse']],
        ['method' => 'get', 'path' => '/api/auth/me', 'tag' => 'Auth', 'protected' => true, 'success' => 200, 'parameters' => [], 'has_data' => true, 'failures' => [401 => 'UnauthorizedResponse', 405 => 'MethodNotAllowedResponse', 500 => 'ServerErrorResponse']],
        ['method' => 'post', 'path' => '/api/auth/refresh', 'tag' => 'Auth', 'protected' => true, 'success' => 200, 'parameters' => [], 'data' => '#/components/schemas/LoginResponse', 'failures' => [401 => 'UnauthorizedResponse', 405 => 'MethodNotAllowedResponse', 500 => 'ServerErrorResponse']],

        ['method' => 'get', 'path' => '/api/categories', 'tag' => 'Categories', 'protected' => true, 'success' => 200, 'parameters' => $settingsIndexParameters, 'items' => '#/components/schemas/Category', 'paginated' => true, 'failures' => [401 => 'UnauthorizedResponse', 405 => 'MethodNotAllowedResponse', 500 => 'ServerErrorResponse']],
        ['method' => 'post', 'path' => '/api/categories', 'tag' => 'Categories', 'protected' => true, 'success' => 201, 'request' => '#/components/schemas/CategoryUpsertRequest', 'parameters' => [], 'data' => '#/components/schemas/Category', 'failures' => [401 => 'UnauthorizedResponse', 405 => 'MethodNotAllowedResponse', 422 => 'ValidationErrorResponse', 500 => 'ServerErrorResponse']],
        ['method' => 'get', 'path' => '/api/categories/{category}', 'tag' => 'Categories', 'protected' => true, 'success' => 200, 'parameters' => ['category'], 'data' => '#/components/schemas/Category', 'failures' => [401 => 'UnauthorizedResponse', 404 => 'NotFoundResponse', 405 => 'MethodNotAllowedResponse', 500 => 'ServerErrorResponse']],
        ['method' => 'put', 'path' => '/api/categories/{category}', 'tag' => 'Categories', 'protected' => true, 'success' => 200, 'request' => '#/components/schemas/CategoryUpsertRequest', 'parameters' => ['category'], 'data' => '#/components/schemas/Category', 'failures' => [401 => 'UnauthorizedResponse', 404 => 'NotFoundResponse', 405 => 'MethodNotAllowedResponse', 422 => 'ValidationErrorResponse', 500 => 'ServerErrorResponse']],
        ['method' => 'delete', 'path' => '/api/categories/{category}', 'tag' => 'Categories', 'protected' => true, 'success' => 200, 'parameters' => ['category'], 'failures' => [401 => 'UnauthorizedResponse', 404 => 'NotFoundResponse', 405 => 'MethodNotAllowedResponse', 409 => 'ConflictResponse', 500 => 'ServerErrorResponse']],

        ['method' => 'get', 'path' => '/api/subcategories', 'tag' => 'Subcategories', 'protected' => true, 'success' => 200, 'parameters' => $settingsIndexParameters, 'items' => '#/components/schemas/Subcategory', 'paginated' => true, 'failures' => [401 => 'UnauthorizedResponse', 405 => 'MethodNotAllowedResponse', 500 => 'ServerErrorResponse']],
        ['method' => 'post', 'path' => '/api/subcategories', 'tag' => 'Subcategories', 'protected' => true, 'success' => 201, 'request' => '#/components/schemas/SubcategoryUpsertRequest', 'parameters' => [], 'data' => '#/components/schemas/Subcategory', 'failures' => [401 => 'UnauthorizedResponse', 405 => 'MethodNotAllowedResponse', 422 => 'ValidationErrorResponse', 500 => 'ServerErrorResponse']],
        ['method' => 'get', 'path' => '/api/subcategories/{subcategory}', 'tag' => 'Subcategories', 'protected' => true, 'success' => 200, 'parameters' => ['subcategory'], 'data' => '#/components/schemas/Subcategory', 'failures' => [401 => 'UnauthorizedResponse', 404 => 'NotFoundResponse', 405 => 'MethodNotAllowedResponse', 500 => 'ServerErrorResponse']],
        ['method' => 'put', 'path' => '/api/subcategories/{subcategory}', 'tag' => 'Subcategories', 'protected' => true, 'success' => 200, 'request' => '#/components/schemas/SubcategoryUpsertRequest', 'parameters' => ['subcategory'], 'data' => '#/components/schemas/Subcategory', 'failures' => [401 => 'UnauthorizedResponse', 404 => 'NotFoundResponse', 405 => 'MethodNotAllowedResponse', 422 => 'ValidationErrorResponse', 500 => 'ServerErrorResponse']],
        ['method' => 'delete', 'path' => '/api/subcategories/{subcategory}', 'tag' => 'Subcategories', 'protected' => true, 'success' => 200, 'parameters' => ['subcategory'], 'failures' => [401 => 'UnauthorizedResponse', 404 => 'NotFoundResponse', 405 => 'MethodNotAllowedResponse', 409 => 'ConflictResponse', 500 => 'ServerErrorResponse']],

        ['method' => 'get', 'path' => '/api/units', 'tag' => 'Units', 'protected' => true, 'success' => 200, 'parameters' => $unitIndexParameters, 'items' => '#/components/schemas/Unit', 'paginated' => true, 'failures' => [401 => 'UnauthorizedResponse', 405 => 'MethodNotAllowedResponse', 500 => 'ServerErrorResponse']],
        ['method' => 'post', 'path' => '/api/units', 'tag' => 'Units', 'protected' => true, 'success' => 201, 'request' => '#/components/schemas/UnitUpsertRequest', 'parameters' => [], 'data' => '#/components/schemas/Unit', 'failures' => [401 => 'UnauthorizedResponse', 405 => 'MethodNotAllowedResponse', 422 => 'ValidationErrorResponse', 500 => 'ServerErrorResponse']],
        ['method' => 'get', 'path' => '/api/units/{unit}', 'tag' => 'Units', 'protected' => true, 'success' => 200, 'parameters' => ['unit'], 'data' => '#/components/schemas/Unit', 'failures' => [401 => 'UnauthorizedResponse', 404 => 'NotFoundResponse', 405 => 'MethodNotAllowedResponse', 500 => 'ServerErrorResponse']],
        ['method' => 'put', 'path' => '/api/units/{unit}', 'tag' => 'Units', 'protected' => true, 'success' => 200, 'request' => '#/components/schemas/UnitUpsertRequest', 'parameters' => ['unit'], 'data' => '#/components/schemas/Unit', 'failures' => [401 => 'UnauthorizedResponse', 404 => 'NotFoundResponse', 405 => 'MethodNotAllowedResponse', 422 => 'ValidationErrorResponse', 500 => 'ServerErrorResponse']],
        ['method' => 'delete', 'path' => '/api/units/{unit}', 'tag' => 'Units', 'protected' => true, 'success' => 200, 'parameters' => ['unit'], 'failures' => [401 => 'UnauthorizedResponse', 404 => 'NotFoundResponse', 405 => 'MethodNotAllowedResponse', 409 => 'ConflictResponse', 500 => 'ServerErrorResponse']],

        ['method' => 'get', 'path' => '/api/suppliers', 'tag' => 'Suppliers', 'protected' => true, 'success' => 200, 'parameters' => $settingsIndexParameters, 'items' => '#/components/schemas/Supplier', 'paginated' => true, 'failures' => [401 => 'UnauthorizedResponse', 405 => 'MethodNotAllowedResponse', 500 => 'ServerErrorResponse']],
        ['method' => 'post', 'path' => '/api/suppliers', 'tag' => 'Suppliers', 'protected' => true, 'success' => 201, 'request' => '#/components/schemas/SupplierUpsertRequest', 'parameters' => [], 'data' => '#/components/schemas/Supplier', 'failures' => [401 => 'UnauthorizedResponse', 405 => 'MethodNotAllowedResponse', 422 => 'ValidationErrorResponse', 500 => 'ServerErrorResponse']],
        ['method' => 'get', 'path' => '/api/suppliers/{supplier}', 'tag' => 'Suppliers', 'protected' => true, 'success' => 200, 'parameters' => ['supplier'], 'data' => '#/components/schemas/Supplier', 'failures' => [401 => 'UnauthorizedResponse', 404 => 'NotFoundResponse', 405 => 'MethodNotAllowedResponse', 500 => 'ServerErrorResponse']],
        ['method' => 'put', 'path' => '/api/suppliers/{supplier}', 'tag' => 'Suppliers', 'protected' => true, 'success' => 200, 'request' => '#/components/schemas/SupplierUpsertRequest', 'parameters' => ['supplier'], 'data' => '#/components/schemas/Supplier', 'failures' => [401 => 'UnauthorizedResponse', 404 => 'NotFoundResponse', 405 => 'MethodNotAllowedResponse', 422 => 'ValidationErrorResponse', 500 => 'ServerErrorResponse']],
        ['method' => 'delete', 'path' => '/api/suppliers/{supplier}', 'tag' => 'Suppliers', 'protected' => true, 'success' => 200, 'parameters' => ['supplier'], 'failures' => [401 => 'UnauthorizedResponse', 404 => 'NotFoundResponse', 405 => 'MethodNotAllowedResponse', 409 => 'ConflictResponse', 500 => 'ServerErrorResponse']],

        ['method' => 'get', 'path' => '/api/items', 'tag' => 'Items', 'protected' => true, 'success' => 200, 'parameters' => $itemIndexParameters, 'items' => '#/components/schemas/Item', 'paginated' => true, 'failures' => [401 => 'UnauthorizedResponse', 405 => 'MethodNotAllowedResponse', 422 => 'ValidationErrorResponse', 500 => 'ServerErrorResponse']],
        ['method' => 'post', 'path' => '/api/items', 'tag' => 'Items', 'protected' => true, 'success' => 201, 'request' => '#/components/schemas/ItemUpsertRequest', 'parameters' => [], 'data' => '#/components/schemas/Item', 'failures' => [401 => 'UnauthorizedResponse', 405 => 'MethodNotAllowedResponse', 422 => 'ValidationErrorResponse', 500 => 'ServerErrorResponse']],
        ['method' => 'get', 'path' => '/api/items/{item}', 'tag' => 'Items', 'protected' => true, 'success' => 200, 'parameters' => ['item'], 'data' => '#/components/schemas/Item', 'failures' => [401 => 'UnauthorizedResponse', 404 => 'NotFoundResponse', 405 => 'MethodNotAllowedResponse', 500 => 'ServerErrorResponse']],
        ['method' => 'put', 'path' => '/api/items/{item}', 'tag' => 'Items', 'protected' => true, 'success' => 200, 'request' => '#/components/schemas/ItemUpsertRequest', 'parameters' => ['item'], 'data' => '#/components/schemas/Item', 'failures' => [401 => 'UnauthorizedResponse', 404 => 'NotFoundResponse', 405 => 'MethodNotAllowedResponse', 422 => 'ValidationErrorResponse', 500 => 'ServerErrorResponse']],
        ['method' => 'delete', 'path' => '/api/items/{item}', 'tag' => 'Items', 'protected' => true, 'success' => 200, 'parameters' => ['item'], 'failures' => [401 => 'UnauthorizedResponse', 404 => 'NotFoundResponse', 405 => 'MethodNotAllowedResponse', 500 => 'ServerErrorResponse']],
        ['method' => 'get', 'path' => '/api/items/{item}/movements', 'tag' => 'Items', 'protected' => true, 'success' => 200, 'parameters' => ['item', 'page', 'per_page'], 'items' => '#/components/schemas/StockMovement', 'paginated' => true, 'failures' => [401 => 'UnauthorizedResponse', 404 => 'NotFoundResponse', 405 => 'MethodNotAllowedResponse', 500 => 'ServerErrorResponse']],

        ['method' => 'get', 'path' => '/api/lookups/categories', 'tag' => 'Lookups', 'protected' => true, 'success' => 200, 'parameters' => [], 'items' => '#/components/schemas/CategoryLookup', 'failures' => [401 => 'UnauthorizedResponse', 405 => 'MethodNotAllowedResponse', 500 => 'ServerErrorResponse']],
        ['method' => 'get', 'path' => '/api/lookups/subcategories', 'tag' => 'Lookups', 'protected' => true, 'success' => 200, 'parameters' => ['category_id'], 'items' => '#/components/schemas/SubcategoryLookup', 'failures' => [401 => 'UnauthorizedResponse', 405 => 'MethodNotAllowedResponse', 422 => 'ValidationErrorResponse', 500 => 'ServerErrorResponse']],
        ['method' => 'get', 'path' => '/api/lookups/units', 'tag' => 'Lookups', 'protected' => true, 'success' => 200, 'parameters' => [], 'items' => '#/components/schemas/UnitLookup', 'failures' => [401 => 'UnauthorizedResponse', 405 => 'MethodNotAllowedResponse', 500 => 'ServerErrorResponse']],
        ['method' => 'get', 'path' => '/api/lookups/suppliers', 'tag' => 'Lookups', 'protected' => true, 'success' => 200, 'parameters' => [], 'items' => '#/components/schemas/SupplierLookup', 'failures' => [401 => 'UnauthorizedResponse', 405 => 'MethodNotAllowedResponse', 500 => 'ServerErrorResponse']],

        ['method' => 'get', 'path' => '/api/dashboard/stats', 'tag' => 'Dashboard', 'protected' => true, 'success' => 200, 'parameters' => [], 'data' => '#/components/schemas/DashboardStats', 'failures' => [401 => 'UnauthorizedResponse', 405 => 'MethodNotAllowedResponse', 500 => 'ServerErrorResponse']],
    ];
}

function swaggerFinalReviewOperationPairs(array $operations): array
{
    $pairs = array_map(
        fn (array $operation): string => strtoupper($operation['method']).' '.$operation['path'],
        $operations
    );

    sort($pairs);

    return array_values($pairs);
}

function swaggerFinalReviewDocumentedOperations(array $document): array
{
    $operations = [];

    foreach ($document['paths'] ?? [] as $path => $methods) {
        foreach ($methods as $method => $_operation) {
            if (! in_array(strtolower($method), ['get', 'post', 'put', 'delete'], true)) {
                continue;
            }

            $operations[] = strtoupper($method).' '.$path;
        }
    }

    sort($operations);

    return array_values($operations);
}

function swaggerFinalReviewOperation(array $document, array $expected): array
{
    $operation = $document['paths'][$expected['path']][$expected['method']] ?? null;

    expect($operation, strtoupper($expected['method']).' '.$expected['path'].' is missing from the generated OpenAPI document')->toBeArray();

    return $operation;
}

function swaggerFinalReviewResponseSchema(array $operation, int $status, string $operationName): array
{
    $schema = $operation['responses'][(string) $status]['content']['application/json']['schema'] ?? null;

    expect($schema, "{$operationName} is missing JSON schema content for {$status}")->toBeArray();

    return $schema;
}

function swaggerFinalReviewResolvedSchema(array $document, array $schema): array
{
    $ref = $schema['$ref'] ?? null;

    if ($ref === null) {
        return $schema;
    }

    $schemaName = str_replace('#/components/schemas/', '', $ref);

    return $document['components']['schemas'][$schemaName] ?? [];
}

function swaggerFinalReviewProperty(array $schema, string $property): array
{
    $value = $schema['properties'][$property] ?? null;

    expect($value, "Schema is missing {$property} property")->toBeArray();

    return $value;
}

function swaggerFinalReviewAssertReviewFinding(string $area, string $target, string $message): array
{
    return [
        'status' => 'fail',
        'severity' => 'blocking',
        'area' => $area,
        'target' => $target,
        'message' => $message,
    ];
}

test('catalogue contains every reviewer-facing operation exactly once and no stale operations', function () {
    $document = swaggerFinalReviewDocument(regenerate: true);
    $expected = swaggerFinalReviewOperationPairs(swaggerFinalReviewExpectedOperations());
    $documented = swaggerFinalReviewDocumentedOperations($document);

    expect($documented)->toBe($expected)
        ->and($documented)->toHaveCount(36)
        ->and(array_values(array_unique($documented)))->toBe($documented);
});

test('domain tags and summaries are clear for every documented operation', function () {
    $document = swaggerFinalReviewDocument();

    foreach (swaggerFinalReviewExpectedOperations() as $expected) {
        $operation = swaggerFinalReviewOperation($document, $expected);
        $name = strtoupper($expected['method']).' '.$expected['path'];

        expect($operation['tags'] ?? [], "{$name} has the wrong Swagger tag")->toContain($expected['tag'])
            ->and($operation['summary'] ?? '', "{$name} needs a consumer-facing summary")->toBeString()->not->toBeEmpty();
    }
});

test('security markers match public and protected operation expectations', function () {
    $document = swaggerFinalReviewDocument();

    foreach (swaggerFinalReviewExpectedOperations() as $expected) {
        $operation = swaggerFinalReviewOperation($document, $expected);
        $name = strtoupper($expected['method']).' '.$expected['path'];

        if ($expected['protected']) {
            expect($operation['security'] ?? null, "{$name} must require bearerAuth")->toBe([['bearerAuth' => []]]);
        } else {
            expect($operation['security'] ?? [], "{$name} must remain public")->toBe([]);
        }
    }
});

test('try-out inputs are documented for path, query, and request body operations', function () {
    $document = swaggerFinalReviewDocument();

    foreach (swaggerFinalReviewExpectedOperations() as $expected) {
        $operation = swaggerFinalReviewOperation($document, $expected);
        $name = strtoupper($expected['method']).' '.$expected['path'];
        $parameterNames = collect($operation['parameters'] ?? [])->pluck('name')->all();

        expect($parameterNames, "{$name} has mismatched documented parameters")->toBe($expected['parameters']);

        if (array_key_exists('request', $expected)) {
            $requestSchemaRef = $operation['requestBody']['content']['application/json']['schema']['$ref'] ?? null;

            expect($requestSchemaRef, "{$name} has the wrong request body schema")->toBe($expected['request']);
        }
    }
});

test('login documents how to obtain and apply a bearer credential', function () {
    $document = swaggerFinalReviewDocument();
    $schemas = $document['components']['schemas'] ?? [];

    expect($schemas['LoginRequest']['required'] ?? [])->toBe(['email', 'password'])
        ->and($schemas['LoginResponse']['required'] ?? [])->toBe(['success', 'message', 'data'])
        ->and($schemas['LoginResponse']['properties']['data']['required'] ?? [])->toBe(['token', 'token_type', 'expires_in', 'user'])
        ->and($schemas['LoginResponse']['properties']['data']['properties']['token_type']['example'] ?? null)->toBe('Bearer')
        ->and($document['components']['securitySchemes']['bearerAuth'] ?? [])->toMatchArray([
            'type' => 'http',
            'scheme' => 'bearer',
            'bearerFormat' => 'JWT',
        ]);
});

test('success responses use the standard success envelope and domain data shape', function () {
    $document = swaggerFinalReviewDocument();

    foreach (swaggerFinalReviewExpectedOperations() as $expected) {
        $operation = swaggerFinalReviewOperation($document, $expected);
        $name = strtoupper($expected['method']).' '.$expected['path'];
        $schema = swaggerFinalReviewResolvedSchema($document, swaggerFinalReviewResponseSchema($operation, $expected['success'], $name));
        $required = $schema['required'] ?? [];

        expect($required, "{$name} success response must require success and message")->toContain('success', 'message');

        if (($expected['paginated'] ?? false) === true) {
            expect($required, "{$name} paginated response must require data and meta")->toContain('data', 'meta');
            expect(swaggerFinalReviewProperty($schema, 'data')['items']['$ref'] ?? null, "{$name} must return the expected data item schema")->toBe($expected['items']);
            expect(swaggerFinalReviewProperty($schema, 'meta')['$ref'] ?? null, "{$name} must use PaginationMeta")->toBe('#/components/schemas/PaginationMeta');

            continue;
        }

        if (array_key_exists('items', $expected)) {
            expect($required, "{$name} list response must require data")->toContain('data');
            expect(swaggerFinalReviewProperty($schema, 'data')['items']['$ref'] ?? null, "{$name} must return the expected list item schema")->toBe($expected['items']);

            continue;
        }

        if (array_key_exists('data', $expected)) {
            expect($required, "{$name} success response must require data")->toContain('data');

            if ($expected['data'] !== '#/components/schemas/LoginResponse') {
                expect(swaggerFinalReviewProperty($schema, 'data')['$ref'] ?? null, "{$name} must return the expected data schema")->toBe($expected['data']);
            }
        }

        if (($expected['has_data'] ?? false) === true) {
            expect($required, "{$name} success response must require data")->toContain('data');
        }
    }
});

test('failure responses use reusable standard failure schemas', function () {
    $document = swaggerFinalReviewDocument();

    foreach (swaggerFinalReviewExpectedOperations() as $expected) {
        $operation = swaggerFinalReviewOperation($document, $expected);
        $name = strtoupper($expected['method']).' '.$expected['path'];

        foreach ($expected['failures'] as $status => $schema) {
            expect($operation['responses'][(string) $status]['content']['application/json']['schema']['$ref'] ?? null, "{$name} {$status} must reference {$schema}")
                ->toBe("#/components/schemas/{$schema}");
        }
    }
});

test('reusable schema catalogue contains every shared review schema', function () {
    $schemas = swaggerFinalReviewDocument()['components']['schemas'] ?? [];

    expect($schemas)->toHaveKeys([
        'LoginRequest',
        'LoginResponse',
        'ValidationErrorResponse',
        'UnauthorizedResponse',
        'ConflictResponse',
        'NotFoundResponse',
        'MethodNotAllowedResponse',
        'ServerErrorResponse',
        'StatusResponse',
        'StatusReport',
        'StatusServiceIdentity',
        'StatusDependencies',
        'Category',
        'CategoryUpsertRequest',
        'Subcategory',
        'SubcategoryUpsertRequest',
        'Unit',
        'UnitUpsertRequest',
        'Supplier',
        'SupplierUpsertRequest',
        'Item',
        'ItemUpsertRequest',
        'DashboardStats',
        'DashboardItem',
        'DashboardSummaryCard',
        'DashboardGrowthPoint',
        'DashboardCategoryBreakdownItem',
        'DashboardCategoryBreakdown',
        'PaginationMeta',
        'CategoryLookup',
        'SubcategoryLookup',
        'UnitLookup',
        'SupplierLookup',
    ]);
});

test('paginated list operations reuse the same pagination metadata schema', function () {
    $document = swaggerFinalReviewDocument();

    foreach (swaggerFinalReviewExpectedOperations() as $expected) {
        if (($expected['paginated'] ?? false) !== true) {
            continue;
        }

        $operation = swaggerFinalReviewOperation($document, $expected);
        $name = strtoupper($expected['method']).' '.$expected['path'];
        $schema = swaggerFinalReviewResolvedSchema($document, swaggerFinalReviewResponseSchema($operation, $expected['success'], $name));

        expect($schema['properties']['meta']['$ref'] ?? null, "{$name} must use PaginationMeta")->toBe('#/components/schemas/PaginationMeta');
    }
});

test('documented examples and schema-required fields stay aligned', function () {
    $schemas = swaggerFinalReviewDocument()['components']['schemas'] ?? [];

    foreach ($schemas as $schemaName => $schema) {
        foreach (($schema['required'] ?? []) as $requiredProperty) {
            expect($schema['properties'][$requiredProperty] ?? null, "{$schemaName} requires {$requiredProperty} but does not define it")->toBeArray();
        }
    }

    $dashboardExample = $schemas['DashboardStats']['example'] ?? [];

    expect($dashboardExample)->toHaveKeys(['total_items', 'total_categories', 'total_suppliers', 'low_stock_items', 'total_stock_value', 'summary_cards', 'inventory_growth_year', 'inventory_growth', 'category_breakdown', 'recent_items', 'low_stock_list']);
});

test('openapi document can be regenerated and parsed without manual generated-file edits', function () {
    $path = storage_path('api-docs/api-docs.json');

    if (file_exists($path)) {
        unlink($path);
    }

    $document = swaggerFinalReviewDocument(regenerate: true);

    expect(file_exists($path))->toBeTrue()
        ->and($document['openapi'] ?? '')->toStartWith('3.')
        ->and($document['info']['title'] ?? null)->toBe('Grocery Inventory Management API');
});

test('docs endpoint returns the final review document shape', function () {
    $response = $this->get('/docs')
        ->assertSuccessful()
        ->assertJsonPath('info.title', 'Grocery Inventory Management API');

    expect($response->json('servers.0.url'))->toBeString()->not->toBeEmpty()
        ->and($response->json('paths'))->toBeArray()
        ->and($response->json('components.schemas'))->toBeArray()
        ->and($response->json('components.securitySchemes.bearerAuth'))->toMatchArray([
            'type' => 'http',
            'scheme' => 'bearer',
            'bearerFormat' => 'JWT',
        ]);
});

test('swagger documentation page renders current api information', function () {
    swaggerFinalReviewDocument();

    $this->get('/api/documentation')
        ->assertSuccessful()
        ->assertSee('Grocery Inventory Management API')
        ->assertSee('/docs');
});

test('final review findings name their affected target', function () {
    $finding = swaggerFinalReviewAssertReviewFinding(
        area: 'operation',
        target: 'GET /api/items',
        message: 'Missing PaginationMeta reference.'
    );

    expect($finding)->toMatchArray([
        'status' => 'fail',
        'severity' => 'blocking',
        'area' => 'operation',
        'target' => 'GET /api/items',
    ])
        ->and($finding['message'])->not->toBeEmpty();
});
