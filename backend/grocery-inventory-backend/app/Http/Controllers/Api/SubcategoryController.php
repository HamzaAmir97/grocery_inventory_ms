<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\IndexRequest;
use App\Http\Requests\Settings\StoreSubcategoryRequest;
use App\Http\Requests\Settings\UpdateSubcategoryRequest;
use App\Http\Resources\SubcategoryResource;
use App\Models\Category;
use App\Models\Subcategory;
use App\Services\DeleteGuardService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class SubcategoryController extends Controller
{
    #[OA\Get(
        path: '/api/subcategories',
        summary: 'List subcategories',
        security: [['bearerAuth' => []]],
        tags: ['Subcategories'],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'sort_by', in: 'query', schema: new OA\Schema(type: 'string', default: 'id', enum: ['name', 'category', 'description', 'is_active', 'items_count', 'created_at'])),
            new OA\Parameter(name: 'sort_dir', in: 'query', schema: new OA\Schema(type: 'string', default: 'asc', enum: ['asc', 'desc'])),
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', default: 1, minimum: 1)),
            new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer', default: 10, maximum: 100, minimum: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Paginated subcategory list', content: new OA\JsonContent(ref: '#/components/schemas/SubcategoryPaginatedResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 405, description: 'Method not allowed', content: new OA\JsonContent(ref: '#/components/schemas/MethodNotAllowedResponse')),
            new OA\Response(response: 422, description: 'Validation failed', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
            new OA\Response(response: 429, description: 'Too many requests'),
            new OA\Response(response: 500, description: 'Unexpected backend failure', content: new OA\JsonContent(ref: '#/components/schemas/ServerErrorResponse')),
        ]
    )]
    public function index(IndexRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Subcategory::class);

        $search = $request->searchTerm();
        $sortBy = $request->sortBy(['name', 'category', 'description', 'is_active', 'items_count', 'created_at']);
        $sortDir = $request->sortDirection();

        $paginator = Subcategory::query()
            ->with(['category:id,name'])
            ->withCount('items')
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                $escaped = addcslashes($search, '%_\\');

                $query->where('name', 'ilike', "%{$escaped}%")
                    ->orWhere('description', 'ilike', "%{$escaped}%")
                    ->orWhereHas('category', fn ($categoryQuery) => $categoryQuery->where('name', 'ilike', "%{$escaped}%"));
            }))
            ->when(
                $sortBy === 'category',
                fn ($query) => $query->orderBy(
                    Category::query()
                        ->select('name')
                        ->whereColumn('categories.id', 'subcategories.category_id'),
                    $sortDir
                ),
                fn ($query) => $query->orderBy($sortBy, $sortDir)
            )
            ->orderBy('id', $sortDir)
            ->paginate($request->perPage(), ['*'], 'page', $request->pageNumber())
            ->through(fn (Subcategory $subcategory): array => SubcategoryResource::make($subcategory)->resolve($request));

        return ApiResponse::paginated($paginator, 'Subcategories fetched successfully.');
    }

    #[OA\Post(
        path: '/api/subcategories',
        summary: 'Create subcategory',
        security: [['bearerAuth' => []]],
        tags: ['Subcategories'],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/SubcategoryUpsertRequest')),
        responses: [
            new OA\Response(response: 201, description: 'Subcategory created', content: new OA\JsonContent(ref: '#/components/schemas/SubcategoryResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 405, description: 'Method not allowed', content: new OA\JsonContent(ref: '#/components/schemas/MethodNotAllowedResponse')),
            new OA\Response(response: 422, description: 'Validation failed', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
            new OA\Response(response: 500, description: 'Unexpected backend failure', content: new OA\JsonContent(ref: '#/components/schemas/ServerErrorResponse')),
        ]
    )]
    public function store(StoreSubcategoryRequest $request): JsonResponse
    {
        $this->authorize('create', Subcategory::class);

        return ApiResponse::created(new SubcategoryResource(Subcategory::query()->create($request->validated())), 'Subcategory created successfully.');
    }

    #[OA\Get(
        path: '/api/subcategories/{subcategory}',
        summary: 'Show subcategory',
        security: [['bearerAuth' => []]],
        tags: ['Subcategories'],
        parameters: [new OA\Parameter(name: 'subcategory', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Subcategory retrieved', content: new OA\JsonContent(ref: '#/components/schemas/SubcategoryResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Not found', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
            new OA\Response(response: 405, description: 'Method not allowed', content: new OA\JsonContent(ref: '#/components/schemas/MethodNotAllowedResponse')),
            new OA\Response(response: 500, description: 'Unexpected backend failure', content: new OA\JsonContent(ref: '#/components/schemas/ServerErrorResponse')),
        ]
    )]
    public function show(Subcategory $subcategory): JsonResponse
    {
        $this->authorize('view', $subcategory);

        return ApiResponse::ok(new SubcategoryResource($subcategory), 'Subcategory retrieved.');
    }

    #[OA\Put(
        path: '/api/subcategories/{subcategory}',
        summary: 'Update subcategory',
        security: [['bearerAuth' => []]],
        tags: ['Subcategories'],
        parameters: [new OA\Parameter(name: 'subcategory', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/SubcategoryUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Subcategory updated', content: new OA\JsonContent(ref: '#/components/schemas/SubcategoryResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Not found', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
            new OA\Response(response: 405, description: 'Method not allowed', content: new OA\JsonContent(ref: '#/components/schemas/MethodNotAllowedResponse')),
            new OA\Response(response: 422, description: 'Validation failed', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
            new OA\Response(response: 500, description: 'Unexpected backend failure', content: new OA\JsonContent(ref: '#/components/schemas/ServerErrorResponse')),
        ]
    )]
    public function update(UpdateSubcategoryRequest $request, Subcategory $subcategory): JsonResponse
    {
        $this->authorize('update', $subcategory);

        $subcategory->update($request->validated());

        return ApiResponse::ok(new SubcategoryResource($subcategory->fresh()), 'Subcategory updated successfully.');
    }

    #[OA\Delete(
        path: '/api/subcategories/{subcategory}',
        summary: 'Delete subcategory',
        security: [['bearerAuth' => []]],
        tags: ['Subcategories'],
        parameters: [new OA\Parameter(name: 'subcategory', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Subcategory deleted', content: new OA\JsonContent(ref: '#/components/schemas/DeleteSuccessResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Not found', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
            new OA\Response(response: 405, description: 'Method not allowed', content: new OA\JsonContent(ref: '#/components/schemas/MethodNotAllowedResponse')),
            new OA\Response(response: 409, description: 'Delete restricted', content: new OA\JsonContent(ref: '#/components/schemas/ConflictResponse')),
            new OA\Response(response: 500, description: 'Unexpected backend failure', content: new OA\JsonContent(ref: '#/components/schemas/ServerErrorResponse')),
        ]
    )]
    public function destroy(DeleteGuardService $guard, Subcategory $subcategory): JsonResponse
    {
        $this->authorize('delete', $subcategory);

        $guard->guardSubcategory($subcategory);
        $subcategory->delete();

        return ApiResponse::deleted('Subcategory deleted successfully.');
    }
}
