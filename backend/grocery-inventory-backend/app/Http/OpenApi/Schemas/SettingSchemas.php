<?php

namespace App\Http\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Category',
    required: ['id', 'name', 'is_active'],
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'description', type: 'string', nullable: true),
        new OA\Property(property: 'is_active', type: 'boolean'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'CategoryPaginatedResponse',
    required: ['success', 'message', 'data', 'meta'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'message', type: 'string', example: 'Categories fetched successfully.'),
        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Category')),
        new OA\Property(property: 'meta', ref: '#/components/schemas/PaginationMeta'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'CategoryResponse',
    required: ['success', 'message', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'message', type: 'string', example: 'Category retrieved.'),
        new OA\Property(property: 'data', ref: '#/components/schemas/Category'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'Subcategory',
    required: ['id', 'category_id', 'name', 'is_active'],
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'category_id', type: 'integer'),
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'description', type: 'string', nullable: true),
        new OA\Property(property: 'is_active', type: 'boolean'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'SubcategoryPaginatedResponse',
    required: ['success', 'message', 'data', 'meta'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'message', type: 'string', example: 'Subcategories fetched successfully.'),
        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Subcategory')),
        new OA\Property(property: 'meta', ref: '#/components/schemas/PaginationMeta'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'SubcategoryResponse',
    required: ['success', 'message', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'message', type: 'string', example: 'Subcategory retrieved.'),
        new OA\Property(property: 'data', ref: '#/components/schemas/Subcategory'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'Unit',
    required: ['id', 'name', 'symbol', 'is_active'],
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'symbol', type: 'string'),
        new OA\Property(property: 'is_active', type: 'boolean'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'UnitPaginatedResponse',
    required: ['success', 'message', 'data', 'meta'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'message', type: 'string', example: 'Units fetched successfully.'),
        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Unit')),
        new OA\Property(property: 'meta', ref: '#/components/schemas/PaginationMeta'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'UnitResponse',
    required: ['success', 'message', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'message', type: 'string', example: 'Unit retrieved.'),
        new OA\Property(property: 'data', ref: '#/components/schemas/Unit'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'Supplier',
    required: ['id', 'name', 'is_active'],
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'contact_person', type: 'string', nullable: true),
        new OA\Property(property: 'phone', type: 'string', nullable: true),
        new OA\Property(property: 'email', type: 'string', format: 'email', nullable: true),
        new OA\Property(property: 'address', type: 'string', nullable: true),
        new OA\Property(property: 'is_active', type: 'boolean'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'SupplierPaginatedResponse',
    required: ['success', 'message', 'data', 'meta'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'message', type: 'string', example: 'Suppliers fetched successfully.'),
        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Supplier')),
        new OA\Property(property: 'meta', ref: '#/components/schemas/PaginationMeta'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'SupplierResponse',
    required: ['success', 'message', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'message', type: 'string', example: 'Supplier retrieved.'),
        new OA\Property(property: 'data', ref: '#/components/schemas/Supplier'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'DeleteSuccessResponse',
    required: ['success', 'message'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'message', type: 'string', example: 'Record deleted successfully.'),
    ],
    type: 'object'
)]
class SettingSchemas {}
