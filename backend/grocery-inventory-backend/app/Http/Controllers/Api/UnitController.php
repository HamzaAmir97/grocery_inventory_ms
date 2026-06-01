<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\IndexRequest;
use App\Http\Requests\Settings\StoreUnitRequest;
use App\Http\Requests\Settings\UpdateUnitRequest;
use App\Http\Resources\UnitResource;
use App\Models\Unit;
use App\Services\DeleteGuardService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class UnitController extends Controller
{
    #[OA\Get(
        path: '/api/units',
        summary: 'List units',
        security: [['bearerAuth' => []]],
        tags: ['Units'],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'sort_by', in: 'query', schema: new OA\Schema(type: 'string', default: 'id', enum: ['name', 'symbol', 'is_active', 'items_count', 'created_at'])),
            new OA\Parameter(name: 'sort_dir', in: 'query', schema: new OA\Schema(type: 'string', default: 'asc', enum: ['asc', 'desc'])),
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', default: 1, minimum: 1)),
            new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer', default: 10, maximum: 100, minimum: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Paginated unit list', content: new OA\JsonContent(ref: '#/components/schemas/UnitPaginatedResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 405, description: 'Method not allowed', content: new OA\JsonContent(ref: '#/components/schemas/MethodNotAllowedResponse')),
            new OA\Response(response: 422, description: 'Validation failed', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
            new OA\Response(response: 429, description: 'Too many requests'),
            new OA\Response(response: 500, description: 'Unexpected backend failure', content: new OA\JsonContent(ref: '#/components/schemas/ServerErrorResponse')),
        ]
    )]
    public function index(IndexRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Unit::class);

        $search = $request->searchTerm();
        $sortBy = $request->sortBy(['name', 'symbol', 'is_active', 'items_count', 'created_at']);
        $sortDir = $request->sortDirection();

        $paginator = Unit::query()
            ->withCount('items')
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                $escaped = addcslashes($search, '%_\\');

                $query->where('name', 'ilike', "%{$escaped}%")
                    ->orWhere('symbol', 'ilike', "%{$escaped}%");
            }))
            ->orderBy($sortBy, $sortDir)
            ->orderBy('id', $sortDir)
            ->paginate($request->perPage(), ['*'], 'page', $request->pageNumber())
            ->through(fn (Unit $unit): array => UnitResource::make($unit)->resolve($request));

        return ApiResponse::paginated($paginator, 'Units fetched successfully.');
    }

    #[OA\Post(
        path: '/api/units',
        summary: 'Create unit',
        security: [['bearerAuth' => []]],
        tags: ['Units'],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/UnitUpsertRequest')),
        responses: [
            new OA\Response(response: 201, description: 'Unit created', content: new OA\JsonContent(ref: '#/components/schemas/UnitResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 405, description: 'Method not allowed', content: new OA\JsonContent(ref: '#/components/schemas/MethodNotAllowedResponse')),
            new OA\Response(response: 422, description: 'Validation failed', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
            new OA\Response(response: 500, description: 'Unexpected backend failure', content: new OA\JsonContent(ref: '#/components/schemas/ServerErrorResponse')),
        ]
    )]
    public function store(StoreUnitRequest $request): JsonResponse
    {
        $this->authorize('create', Unit::class);

        return ApiResponse::created(new UnitResource(Unit::query()->create($request->validated())), 'Unit created successfully.');
    }

    #[OA\Get(
        path: '/api/units/{unit}',
        summary: 'Show unit',
        security: [['bearerAuth' => []]],
        tags: ['Units'],
        parameters: [new OA\Parameter(name: 'unit', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Unit retrieved', content: new OA\JsonContent(ref: '#/components/schemas/UnitResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Not found', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
            new OA\Response(response: 405, description: 'Method not allowed', content: new OA\JsonContent(ref: '#/components/schemas/MethodNotAllowedResponse')),
            new OA\Response(response: 500, description: 'Unexpected backend failure', content: new OA\JsonContent(ref: '#/components/schemas/ServerErrorResponse')),
        ]
    )]
    public function show(Unit $unit): JsonResponse
    {
        $this->authorize('view', $unit);

        return ApiResponse::ok(new UnitResource($unit), 'Unit retrieved.');
    }

    #[OA\Put(
        path: '/api/units/{unit}',
        summary: 'Update unit',
        security: [['bearerAuth' => []]],
        tags: ['Units'],
        parameters: [new OA\Parameter(name: 'unit', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/UnitUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Unit updated', content: new OA\JsonContent(ref: '#/components/schemas/UnitResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Not found', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
            new OA\Response(response: 405, description: 'Method not allowed', content: new OA\JsonContent(ref: '#/components/schemas/MethodNotAllowedResponse')),
            new OA\Response(response: 422, description: 'Validation failed', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
            new OA\Response(response: 500, description: 'Unexpected backend failure', content: new OA\JsonContent(ref: '#/components/schemas/ServerErrorResponse')),
        ]
    )]
    public function update(UpdateUnitRequest $request, Unit $unit): JsonResponse
    {
        $this->authorize('update', $unit);

        $unit->update($request->validated());

        return ApiResponse::ok(new UnitResource($unit->fresh()), 'Unit updated successfully.');
    }

    #[OA\Delete(
        path: '/api/units/{unit}',
        summary: 'Delete unit',
        security: [['bearerAuth' => []]],
        tags: ['Units'],
        parameters: [new OA\Parameter(name: 'unit', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Unit deleted', content: new OA\JsonContent(ref: '#/components/schemas/DeleteSuccessResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Not found', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
            new OA\Response(response: 405, description: 'Method not allowed', content: new OA\JsonContent(ref: '#/components/schemas/MethodNotAllowedResponse')),
            new OA\Response(response: 409, description: 'Delete restricted', content: new OA\JsonContent(ref: '#/components/schemas/ConflictResponse')),
            new OA\Response(response: 500, description: 'Unexpected backend failure', content: new OA\JsonContent(ref: '#/components/schemas/ServerErrorResponse')),
        ]
    )]
    public function destroy(DeleteGuardService $guard, Unit $unit): JsonResponse
    {
        $this->authorize('delete', $unit);

        $guard->guardUnit($unit);
        $unit->delete();

        return ApiResponse::deleted('Unit deleted successfully.');
    }
}
