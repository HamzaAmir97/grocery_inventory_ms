<?php

it('brands the generated openapi document and documents public status', function () {
    $document = serverExperienceOpenApiDocument();

    expect($document['info']['contact'] ?? null)->toBeArray()
        ->and($document['tags'] ?? [])->sequence(
            fn ($tag) => $tag->name->toBe('Auth'),
            fn ($tag) => $tag->name->toBe('Categories'),
            fn ($tag) => $tag->name->toBe('Subcategories'),
            fn ($tag) => $tag->name->toBe('Units'),
            fn ($tag) => $tag->name->toBe('Suppliers'),
            fn ($tag) => $tag->name->toBe('Items'),
            fn ($tag) => $tag->name->toBe('Lookups'),
            fn ($tag) => $tag->name->toBe('Dashboard'),
            fn ($tag) => $tag->name->toBe('Status'),
        );

    $operation = $document['paths']['/api/status']['get'] ?? null;

    expect($operation)->toBeArray()
        ->and($operation['tags'] ?? [])->toContain('Status')
        ->and($operation['security'] ?? [])->toBe([])
        ->and($operation['responses']['200']['content']['application/json']['schema']['$ref'] ?? null)
        ->toBe('#/components/schemas/StatusResponse');
});
