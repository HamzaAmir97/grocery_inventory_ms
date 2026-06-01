<?php

namespace App\Http\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CategoryUpsertRequest',
    required: ['name'],
    properties: [
        new OA\Property(property: 'name', type: 'string', maxLength: 255),
        new OA\Property(property: 'description', type: 'string', nullable: true),
        new OA\Property(property: 'is_active', type: 'boolean', default: true),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'SubcategoryUpsertRequest',
    required: ['category_id', 'name'],
    properties: [
        new OA\Property(property: 'category_id', type: 'integer'),
        new OA\Property(property: 'name', type: 'string', maxLength: 255),
        new OA\Property(property: 'description', type: 'string', nullable: true),
        new OA\Property(property: 'is_active', type: 'boolean', default: true),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'UnitUpsertRequest',
    required: ['name', 'symbol'],
    properties: [
        new OA\Property(property: 'name', type: 'string', maxLength: 255),
        new OA\Property(property: 'symbol', type: 'string', maxLength: 50),
        new OA\Property(property: 'is_active', type: 'boolean', default: true),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'SupplierUpsertRequest',
    required: ['name'],
    properties: [
        new OA\Property(property: 'name', type: 'string', maxLength: 255),
        new OA\Property(property: 'contact_person', type: 'string', maxLength: 255, nullable: true),
        new OA\Property(property: 'phone', type: 'string', maxLength: 50, nullable: true),
        new OA\Property(property: 'email', type: 'string', format: 'email', nullable: true),
        new OA\Property(property: 'address', type: 'string', nullable: true),
        new OA\Property(property: 'is_active', type: 'boolean', default: true),
    ],
    type: 'object'
)]
class SettingsRequestSchemas {}
