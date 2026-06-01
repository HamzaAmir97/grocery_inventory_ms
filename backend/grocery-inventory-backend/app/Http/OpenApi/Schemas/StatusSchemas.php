<?php

namespace App\Http\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'StatusServiceIdentity',
    required: ['name', 'version', 'environment'],
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Grocery Inventory Management API'),
        new OA\Property(property: 'version', type: 'string', example: '1.0.0'),
        new OA\Property(property: 'environment', type: 'string', example: 'local'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'StatusDependencies',
    required: ['database'],
    properties: [
        new OA\Property(property: 'database', type: 'boolean', example: true),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'StatusReport',
    required: ['status', 'service', 'dependencies', 'checked_at'],
    properties: [
        new OA\Property(property: 'status', type: 'string', enum: ['ok', 'degraded'], example: 'ok'),
        new OA\Property(property: 'service', ref: '#/components/schemas/StatusServiceIdentity'),
        new OA\Property(property: 'dependencies', ref: '#/components/schemas/StatusDependencies'),
        new OA\Property(property: 'checked_at', type: 'string', format: 'date-time', example: '2026-05-29T12:00:00+00:00'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'StatusResponse',
    required: ['success', 'message', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'message', type: 'string', example: 'Service status retrieved.'),
        new OA\Property(property: 'data', ref: '#/components/schemas/StatusReport'),
    ],
    type: 'object'
)]
class StatusSchemas {}
