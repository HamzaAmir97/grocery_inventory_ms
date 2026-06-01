<?php

namespace App\Http\OpenApi;

use App\Support\ServiceIdentity;
use OpenApi\Attributes as OA;

#[OA\OpenApi(
    openapi: '3.1.0',
    info: new OA\Info(
        title: ServiceIdentity::NAME,
        version: ServiceIdentity::VERSION,
        description: 'REST API for the Grocery Inventory Management System. Consumed by the Next.js admin dashboard. Authentication is JWT Bearer.',
        contact: new OA\Contact(email: 'contact@hamzahamir.site', url: 'https://www.hamzahamir.site/en')
    ),
    servers: [new OA\Server(url: L5_SWAGGER_CONST_HOST)],
    tags: [
        new OA\Tag(name: 'Auth', description: 'Authentication and session operations.'),
        new OA\Tag(name: 'Categories', description: 'Settings category operations.'),
        new OA\Tag(name: 'Subcategories', description: 'Settings subcategory operations.'),
        new OA\Tag(name: 'Units', description: 'Settings unit operations.'),
        new OA\Tag(name: 'Suppliers', description: 'Settings supplier operations.'),
        new OA\Tag(name: 'Items', description: 'Inventory item management.'),
        new OA\Tag(name: 'Lookups', description: 'Compact option lists for forms and filters.'),
        new OA\Tag(name: 'Dashboard', description: 'Operational dashboard summaries.'),
        new OA\Tag(name: 'Status', description: 'Public service status and identity.'),
    ]
)]
class OpenApiDefinition {}
