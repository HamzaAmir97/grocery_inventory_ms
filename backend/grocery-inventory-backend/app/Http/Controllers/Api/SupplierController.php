<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\IndexRequest;
use App\Http\Requests\Settings\StoreSupplierRequest;
use App\Http\Requests\Settings\UpdateSupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use App\Services\DeleteGuardService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class SupplierController extends Controller
{
    #[OA\Get(
        path: '/api/suppliers',
        summary: 'List suppliers',
        security: [['bearerAuth' => []]],
        tags: ['Suppliers'],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'sort_by', in: 'query', schema: new OA\Schema(type: 'string', default: 'id', enum: ['name', 'contact_person', 'phone', 'email', 'is_active', 'items_count', 'created_at'])),
            new OA\Parameter(name: 'sort_dir', in: 'query', schema: new OA\Schema(type: 'string', default: 'asc', enum: ['asc', 'desc'])),
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', default: 1, minimum: 1)),
            new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer', default: 10, maximum: 100, minimum: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Paginated supplier list', content: new OA\JsonContent(ref: '#/components/schemas/SupplierPaginatedResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 405, description: 'Method not allowed', content: new OA\JsonContent(ref: '#/components/schemas/MethodNotAllowedResponse')),
            new OA\Response(response: 422, description: 'Validation failed', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
            new OA\Response(response: 429, description: 'Too many requests'),
            new OA\Response(response: 500, description: 'Unexpected backend failure', content: new OA\JsonContent(ref: '#/components/schemas/ServerErrorResponse')),
        ]
    )]
    public function index(IndexRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Supplier::class);

        $search = $request->searchTerm();
        $sortBy = $request->sortBy(['name', 'contact_person', 'phone', 'email', 'is_active', 'items_count', 'created_at']);
        $sortDir = $request->sortDirection();

        $paginator = Supplier::query()
            ->withCount('items')
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                $escaped = addcslashes($search, '%_\\');

                $query->where('name', 'ilike', "%{$escaped}%")
                    ->orWhere('contact_person', 'ilike', "%{$escaped}%")
                    ->orWhere('phone', 'ilike', "%{$escaped}%")
                    ->orWhere('email', 'ilike', "%{$escaped}%");
            }))
            ->orderBy($sortBy, $sortDir)
            ->orderBy('id', $sortDir)
            ->paginate($request->perPage(), ['*'], 'page', $request->pageNumber())
            ->through(fn (Supplier $supplier): array => SupplierResource::make($supplier)->resolve($request));

        return ApiResponse::paginated($paginator, 'Suppliers fetched successfully.');
    }

    #[OA\Post(
        path: '/api/suppliers',
        summary: 'Create supplier',
        security: [['bearerAuth' => []]],
        tags: ['Suppliers'],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/SupplierUpsertRequest')),
        responses: [
            new OA\Response(response: 201, description: 'Supplier created', content: new OA\JsonContent(ref: '#/components/schemas/SupplierResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 405, description: 'Method not allowed', content: new OA\JsonContent(ref: '#/components/schemas/MethodNotAllowedResponse')),
            new OA\Response(response: 422, description: 'Validation failed', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
            new OA\Response(response: 500, description: 'Unexpected backend failure', content: new OA\JsonContent(ref: '#/components/schemas/ServerErrorResponse')),
        ]
    )]
    public function store(StoreSupplierRequest $request): JsonResponse
    {
        $this->authorize('create', Supplier::class);

        return ApiResponse::created(new SupplierResource(Supplier::query()->create($request->validated())), 'Supplier created successfully.');
    }

    #[OA\Get(
        path: '/api/suppliers/{supplier}',
        summary: 'Show supplier',
        security: [['bearerAuth' => []]],
        tags: ['Suppliers'],
        parameters: [new OA\Parameter(name: 'supplier', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Supplier retrieved', content: new OA\JsonContent(ref: '#/components/schemas/SupplierResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Not found', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
            new OA\Response(response: 405, description: 'Method not allowed', content: new OA\JsonContent(ref: '#/components/schemas/MethodNotAllowedResponse')),
            new OA\Response(response: 500, description: 'Unexpected backend failure', content: new OA\JsonContent(ref: '#/components/schemas/ServerErrorResponse')),
        ]
    )]
    public function show(Supplier $supplier): JsonResponse
    {
        $this->authorize('view', $supplier);

        return ApiResponse::ok(new SupplierResource($supplier), 'Supplier retrieved.');
    }

    #[OA\Put(
        path: '/api/suppliers/{supplier}',
        summary: 'Update supplier',
        security: [['bearerAuth' => []]],
        tags: ['Suppliers'],
        parameters: [new OA\Parameter(name: 'supplier', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/SupplierUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Supplier updated', content: new OA\JsonContent(ref: '#/components/schemas/SupplierResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Not found', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
            new OA\Response(response: 405, description: 'Method not allowed', content: new OA\JsonContent(ref: '#/components/schemas/MethodNotAllowedResponse')),
            new OA\Response(response: 422, description: 'Validation failed', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
            new OA\Response(response: 500, description: 'Unexpected backend failure', content: new OA\JsonContent(ref: '#/components/schemas/ServerErrorResponse')),
        ]
    )]
    public function update(UpdateSupplierRequest $request, Supplier $supplier): JsonResponse
    {
        $this->authorize('update', $supplier);

        $supplier->update($request->validated());

        return ApiResponse::ok(new SupplierResource($supplier->fresh()), 'Supplier updated successfully.');
    }

    #[OA\Delete(
        path: '/api/suppliers/{supplier}',
        summary: 'Delete supplier',
        security: [['bearerAuth' => []]],
        tags: ['Suppliers'],
        parameters: [new OA\Parameter(name: 'supplier', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Supplier deleted', content: new OA\JsonContent(ref: '#/components/schemas/DeleteSuccessResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Not found', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
            new OA\Response(response: 405, description: 'Method not allowed', content: new OA\JsonContent(ref: '#/components/schemas/MethodNotAllowedResponse')),
            new OA\Response(response: 409, description: 'Delete restricted', content: new OA\JsonContent(ref: '#/components/schemas/ConflictResponse')),
            new OA\Response(response: 500, description: 'Unexpected backend failure', content: new OA\JsonContent(ref: '#/components/schemas/ServerErrorResponse')),
        ]
    )]
    public function destroy(DeleteGuardService $guard, Supplier $supplier): JsonResponse
    {
        $this->authorize('delete', $supplier);

        $guard->guardSupplier($supplier);
        $supplier->delete();

        return ApiResponse::deleted('Supplier deleted successfully.');
    }
}
