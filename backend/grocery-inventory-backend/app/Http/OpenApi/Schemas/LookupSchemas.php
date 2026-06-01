<?php

namespace App\Http\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CategoryLookup',
    required: ['id', 'name'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Dairy'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'SubcategoryLookup',
    required: ['id', 'category_id', 'name'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 4),
        new OA\Property(property: 'category_id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Milk'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'UnitLookup',
    required: ['id', 'name', 'symbol'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Kilogram'),
        new OA\Property(property: 'symbol', type: 'string', example: 'kg'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'SupplierLookup',
    required: ['id', 'name'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Fresh Farm Supplies'),
    ],
    type: 'object'
)]
class LookupSchemas {}
