<?php

namespace App\Http\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Item',
    required: ['id', 'name', 'category_id', 'subcategory_id', 'unit_id', 'supplier_id', 'price', 'stock_quantity', 'low_stock_threshold', 'is_active'],
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'sku', type: 'string', nullable: true),
        new OA\Property(property: 'category_id', type: 'integer'),
        new OA\Property(property: 'subcategory_id', type: 'integer'),
        new OA\Property(property: 'unit_id', type: 'integer'),
        new OA\Property(property: 'supplier_id', type: 'integer'),
        new OA\Property(property: 'category', properties: [new OA\Property(property: 'id', type: 'integer'), new OA\Property(property: 'name', type: 'string')], type: 'object'),
        new OA\Property(property: 'subcategory', properties: [new OA\Property(property: 'id', type: 'integer'), new OA\Property(property: 'name', type: 'string')], type: 'object'),
        new OA\Property(property: 'unit', properties: [new OA\Property(property: 'id', type: 'integer'), new OA\Property(property: 'name', type: 'string'), new OA\Property(property: 'symbol', type: 'string')], type: 'object'),
        new OA\Property(property: 'supplier', properties: [new OA\Property(property: 'id', type: 'integer'), new OA\Property(property: 'name', type: 'string')], type: 'object'),
        new OA\Property(property: 'price', type: 'number', format: 'float', minimum: 0),
        new OA\Property(property: 'stock_quantity', type: 'integer', minimum: 0),
        new OA\Property(property: 'low_stock_threshold', type: 'integer', minimum: 0),
        new OA\Property(property: 'description', type: 'string', nullable: true),
        new OA\Property(property: 'is_active', type: 'boolean'),
        new OA\Property(property: 'version', type: 'integer', minimum: 0),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'ItemResponse',
    required: ['success', 'message', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'message', type: 'string', example: 'Item retrieved.'),
        new OA\Property(property: 'data', ref: '#/components/schemas/Item'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'ItemPaginatedResponse',
    required: ['success', 'message', 'data', 'meta'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'message', type: 'string', example: 'Items fetched successfully.'),
        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Item')),
        new OA\Property(property: 'meta', ref: '#/components/schemas/PaginationMeta'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'ItemDeletedResponse',
    required: ['success', 'message'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'message', type: 'string', example: 'Item deleted successfully.'),
    ],
    type: 'object'
)]
class ItemSchemas {}
