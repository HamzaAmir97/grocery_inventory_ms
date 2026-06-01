<?php

namespace App\Http\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'StockMovement',
    required: ['id', 'item_id', 'delta', 'reason', 'created_at'],
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'item_id', type: 'integer'),
        new OA\Property(property: 'delta', type: 'integer', description: 'Positive when stock increases, negative when it decreases.'),
        new OA\Property(property: 'reason', type: 'string', enum: ['initial', 'manual_edit', 'restock', 'sale', 'deleted']),
        new OA\Property(property: 'note', type: 'string', nullable: true),
        new OA\Property(property: 'user', properties: [
            new OA\Property(property: 'id', type: 'integer', nullable: true),
            new OA\Property(property: 'name', type: 'string', nullable: true),
        ], type: 'object', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'StockMovementPaginatedResponse',
    required: ['success', 'message', 'data', 'meta'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'message', type: 'string', example: 'Stock movements fetched successfully.'),
        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/StockMovement')),
        new OA\Property(property: 'meta', ref: '#/components/schemas/PaginationMeta'),
    ],
    type: 'object'
)]
class StockMovementSchemas {}
