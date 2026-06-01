<?php

namespace App\Http\Controllers\Api;

use App\Actions\Inventory\DeleteItem;
use App\Actions\Inventory\StoreItem;
use App\Actions\Inventory\UpdateItem;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\ItemIndexRequest;
use App\Http\Requests\Inventory\StoreItemRequest;
use App\Http\Requests\Inventory\UpdateItemRequest;
use App\Http\Resources\ItemResource;
use App\Http\Resources\StockMovementResource;
use App\Models\Item;
use App\Services\InventoryQueryService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ItemUpsertRequest',
    required: ['name', 'category_id', 'subcategory_id', 'unit_id', 'supplier_id', 'price', 'stock_quantity'],
    properties: [
        new OA\Property(property: 'name', type: 'string', maxLength: 255),
        new OA\Property(property: 'sku', type: 'string', nullable: true, maxLength: 64),
        new OA\Property(property: 'category_id', type: 'integer'),
        new OA\Property(property: 'subcategory_id', description: 'MUST belong to the chosen or existing category_id.', type: 'integer'),
        new OA\Property(property: 'unit_id', type: 'integer'),
        new OA\Property(property: 'supplier_id', type: 'integer'),
        new OA\Property(property: 'price', type: 'number', format: 'float', minimum: 0, maximum: 99999999),
        new OA\Property(property: 'stock_quantity', type: 'integer', minimum: 0, maximum: 999999),
        new OA\Property(property: 'low_stock_threshold', type: 'integer', default: 10, nullable: true, minimum: 0),
        new OA\Property(property: 'description', type: 'string', nullable: true, maxLength: 2000),
        new OA\Property(property: 'is_active', type: 'boolean', default: true),
        new OA\Property(property: 'version', description: 'Required for safe concurrent updates. Send the version returned by the previous read. If it does not match, the response is 409 Conflict.', type: 'integer', minimum: 0),
    ],
    type: 'object'
)]
class ItemController extends Controller
{
    private const RELATIONS = ['category:id,name', 'subcategory:id,name', 'unit:id,name,symbol', 'supplier:id,name'];

    #[OA\Get(
        path: '/api/items',
        summary: 'List items with search, filter, sort, and pagination',
        security: [['bearerAuth' => []]],
        tags: ['Items'],
        parameters: [
            new OA\Parameter(name: 'search', description: 'Case-insensitive substring on name or sku. Special characters are treated as literals.', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'category_id', in: 'query', schema: new OA\Schema(type: 'integer', nullable: true)),
            new OA\Parameter(name: 'subcategory_id', in: 'query', schema: new OA\Schema(type: 'integer', nullable: true)),
            new OA\Parameter(name: 'unit_id', in: 'query', schema: new OA\Schema(type: 'integer', nullable: true)),
            new OA\Parameter(name: 'supplier_id', in: 'query', schema: new OA\Schema(type: 'integer', nullable: true)),
            new OA\Parameter(name: 'low_stock', in: 'query', schema: new OA\Schema(type: 'boolean')),
            new OA\Parameter(name: 'sort_by', in: 'query', schema: new OA\Schema(type: 'string', default: 'created_at', enum: ['name', 'sku', 'category', 'subcategory', 'unit', 'supplier', 'price', 'stock_quantity', 'created_at'])),
            new OA\Parameter(name: 'sort_dir', in: 'query', schema: new OA\Schema(type: 'string', default: 'desc', enum: ['asc', 'desc'])),
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', default: 1, minimum: 1)),
            new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer', default: 10, maximum: 100, minimum: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Paginated items.', content: new OA\JsonContent(ref: '#/components/schemas/ItemPaginatedResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 405, description: 'Method not allowed', content: new OA\JsonContent(ref: '#/components/schemas/MethodNotAllowedResponse')),
            new OA\Response(response: 422, description: 'Validation failed', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
            new OA\Response(response: 429, description: 'Too many requests'),
            new OA\Response(response: 500, description: 'Unexpected backend failure', content: new OA\JsonContent(ref: '#/components/schemas/ServerErrorResponse')),
        ]
    )]
    public function index(ItemIndexRequest $request, InventoryQueryService $query): JsonResponse
    {
        $this->authorize('viewAny', Item::class);

        $paginator = $query->list($request->validated())
            ->through(fn (Item $item): array => ItemResource::make($item)->resolve($request));

        return ApiResponse::paginated($paginator, 'Items fetched successfully.');
    }

    #[OA\Post(
        path: '/api/items',
        summary: 'Create item',
        security: [['bearerAuth' => []]],
        tags: ['Items'],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ItemUpsertRequest')),
        responses: [
            new OA\Response(response: 201, description: 'Item created.', content: new OA\JsonContent(ref: '#/components/schemas/ItemResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 405, description: 'Method not allowed', content: new OA\JsonContent(ref: '#/components/schemas/MethodNotAllowedResponse')),
            new OA\Response(response: 422, description: 'Validation failed', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
            new OA\Response(response: 500, description: 'Unexpected backend failure', content: new OA\JsonContent(ref: '#/components/schemas/ServerErrorResponse')),
        ]
    )]
    public function store(StoreItemRequest $request, StoreItem $action): JsonResponse
    {
        $this->authorize('create', Item::class);

        $item = $action->execute($request->validated(), optional($request->user('api'))->id);

        return ApiResponse::created(new ItemResource($item->load(self::RELATIONS)), 'Item created successfully.');
    }

    #[OA\Get(
        path: '/api/items/{item}',
        summary: 'Show item',
        security: [['bearerAuth' => []]],
        tags: ['Items'],
        parameters: [new OA\Parameter(name: 'item', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Item retrieved.', content: new OA\JsonContent(ref: '#/components/schemas/ItemResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Item not found', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
            new OA\Response(response: 405, description: 'Method not allowed', content: new OA\JsonContent(ref: '#/components/schemas/MethodNotAllowedResponse')),
            new OA\Response(response: 500, description: 'Unexpected backend failure', content: new OA\JsonContent(ref: '#/components/schemas/ServerErrorResponse')),
        ]
    )]
    public function show(Item $item): JsonResponse
    {
        $this->authorize('view', $item);

        return ApiResponse::ok(new ItemResource($item->load(self::RELATIONS)), 'Item retrieved.');
    }

    #[OA\Put(
        path: '/api/items/{item}',
        summary: 'Update item',
        description: 'Supports partial updates. Send `version` from the last read to enable optimistic locking; mismatch returns 409.',
        security: [['bearerAuth' => []]],
        tags: ['Items'],
        parameters: [new OA\Parameter(name: 'item', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ItemUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Item updated.', content: new OA\JsonContent(ref: '#/components/schemas/ItemResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Item not found', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
            new OA\Response(response: 405, description: 'Method not allowed', content: new OA\JsonContent(ref: '#/components/schemas/MethodNotAllowedResponse')),
            new OA\Response(response: 409, description: 'Optimistic lock conflict', content: new OA\JsonContent(ref: '#/components/schemas/ConflictResponse')),
            new OA\Response(response: 422, description: 'Validation failed', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
            new OA\Response(response: 500, description: 'Unexpected backend failure', content: new OA\JsonContent(ref: '#/components/schemas/ServerErrorResponse')),
        ]
    )]
    public function update(UpdateItemRequest $request, Item $item, UpdateItem $action): JsonResponse
    {
        $this->authorize('update', $item);

        $updated = $action->execute($item, $request->validated(), optional($request->user('api'))->id);

        return ApiResponse::ok(new ItemResource($updated->fresh(self::RELATIONS)), 'Item updated successfully.');
    }

    #[OA\Delete(
        path: '/api/items/{item}',
        summary: 'Delete item (soft delete)',
        security: [['bearerAuth' => []]],
        tags: ['Items'],
        parameters: [new OA\Parameter(name: 'item', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Item deleted.', content: new OA\JsonContent(ref: '#/components/schemas/ItemDeletedResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Item not found', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
            new OA\Response(response: 405, description: 'Method not allowed', content: new OA\JsonContent(ref: '#/components/schemas/MethodNotAllowedResponse')),
            new OA\Response(response: 500, description: 'Unexpected backend failure', content: new OA\JsonContent(ref: '#/components/schemas/ServerErrorResponse')),
        ]
    )]
    public function destroy(Item $item, DeleteItem $action, Request $request): JsonResponse
    {
        $this->authorize('delete', $item);

        $action->execute($item, optional($request->user('api'))->id);

        return ApiResponse::deleted('Item deleted successfully.');
    }

    #[OA\Get(
        path: '/api/items/{item}/movements',
        summary: 'List stock movements for an item',
        security: [['bearerAuth' => []]],
        tags: ['Items'],
        parameters: [
            new OA\Parameter(name: 'item', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', default: 1, minimum: 1)),
            new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer', default: 20, maximum: 100, minimum: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Stock movements fetched successfully.', content: new OA\JsonContent(ref: '#/components/schemas/StockMovementPaginatedResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Item not found', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
            new OA\Response(response: 405, description: 'Method not allowed', content: new OA\JsonContent(ref: '#/components/schemas/MethodNotAllowedResponse')),
            new OA\Response(response: 500, description: 'Unexpected backend failure', content: new OA\JsonContent(ref: '#/components/schemas/ServerErrorResponse')),
        ]
    )]
    public function movements(Item $item, Request $request): JsonResponse
    {
        $this->authorize('viewMovements', $item);

        $perPage = min(100, max(1, (int) $request->integer('per_page', 20)));
        $page = max(1, (int) $request->integer('page', 1));

        $paginator = $item->stockMovements()
            ->with('user:id,name')
            ->paginate($perPage, ['*'], 'page', $page)
            ->through(fn ($movement): array => StockMovementResource::make($movement)->resolve($request));

        return ApiResponse::paginated($paginator, 'Stock movements fetched successfully.');
    }
}
