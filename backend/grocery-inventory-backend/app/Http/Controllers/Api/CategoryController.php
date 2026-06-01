<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\IndexRequest;
use App\Http\Requests\Settings\StoreCategoryRequest;
use App\Http\Requests\Settings\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\DeleteGuardService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class CategoryController extends Controller
{
    #[OA\Get(
        path: '/api/categories',
        summary: 'List categories',
        security: [['bearerAuth' => []]],
        tags: ['Categories'],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'sort_by', in: 'query', schema: new OA\Schema(type: 'string', default: 'id', enum: ['name', 'description', 'is_active', 'subcategories_count', 'items_count', 'created_at'])),
            new OA\Parameter(name: 'sort_dir', in: 'query', schema: new OA\Schema(type: 'string', default: 'asc', enum: ['asc', 'desc'])),
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', default: 1, minimum: 1)),
            new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer', default: 10, maximum: 100, minimum: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Paginated category list', content: new OA\JsonContent(ref: '#/components/schemas/CategoryPaginatedResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 405, description: 'Method not allowed', content: new OA\JsonContent(ref: '#/components/schemas/MethodNotAllowedResponse')),
            new OA\Response(response: 422, description: 'Validation failed', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
            new OA\Response(response: 429, description: 'Too many requests'),
            new OA\Response(response: 500, description: 'Unexpected backend failure', content: new OA\JsonContent(ref: '#/components/schemas/ServerErrorResponse')),
        ]
    )]
    public function index(IndexRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Category::class);

        $search = $request->searchTerm();
        $sortBy = $request->sortBy(['name', 'description', 'is_active', 'subcategories_count', 'items_count', 'created_at']);
        $sortDir = $request->sortDirection();

        $paginator = Category::query()
            ->withCount(['subcategories', 'items'])
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                $escaped = addcslashes($search, '%_\\');

                $query->where('name', 'ilike', "%{$escaped}%")
                    ->orWhere('description', 'ilike', "%{$escaped}%");
            }))
            ->orderBy($sortBy, $sortDir)
            ->orderBy('id', $sortDir)
            ->paginate($request->perPage(), ['*'], 'page', $request->pageNumber())
            ->through(fn (Category $category): array => CategoryResource::make($category)->resolve($request));

        return ApiResponse::paginated($paginator, 'Categories fetched successfully.');
    }

    #[OA\Post(
        path: '/api/categories',
        summary: 'Create category',
        security: [['bearerAuth' => []]],
        tags: ['Categories'],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/CategoryUpsertRequest')),
        responses: [
            new OA\Response(response: 201, description: 'Category created', content: new OA\JsonContent(ref: '#/components/schemas/CategoryResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 405, description: 'Method not allowed', content: new OA\JsonContent(ref: '#/components/schemas/MethodNotAllowedResponse')),
            new OA\Response(response: 422, description: 'Validation failed', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
            new OA\Response(response: 500, description: 'Unexpected backend failure', content: new OA\JsonContent(ref: '#/components/schemas/ServerErrorResponse')),
        ]
    )]
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $this->authorize('create', Category::class);

        return ApiResponse::created(new CategoryResource(Category::query()->create($request->validated())), 'Category created successfully.');
    }

    #[OA\Get(
        path: '/api/categories/{category}',
        summary: 'Show category',
        security: [['bearerAuth' => []]],
        tags: ['Categories'],
        parameters: [new OA\Parameter(name: 'category', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Category retrieved', content: new OA\JsonContent(ref: '#/components/schemas/CategoryResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Not found', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
            new OA\Response(response: 405, description: 'Method not allowed', content: new OA\JsonContent(ref: '#/components/schemas/MethodNotAllowedResponse')),
            new OA\Response(response: 500, description: 'Unexpected backend failure', content: new OA\JsonContent(ref: '#/components/schemas/ServerErrorResponse')),
        ]
    )]
    public function show(Category $category): JsonResponse
    {
        $this->authorize('view', $category);

        return ApiResponse::ok(new CategoryResource($category), 'Category retrieved.');
    }

    #[OA\Put(
        path: '/api/categories/{category}',
        summary: 'Update category',
        security: [['bearerAuth' => []]],
        tags: ['Categories'],
        parameters: [new OA\Parameter(name: 'category', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/CategoryUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Category updated', content: new OA\JsonContent(ref: '#/components/schemas/CategoryResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Not found', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
            new OA\Response(response: 405, description: 'Method not allowed', content: new OA\JsonContent(ref: '#/components/schemas/MethodNotAllowedResponse')),
            new OA\Response(response: 422, description: 'Validation failed', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
            new OA\Response(response: 500, description: 'Unexpected backend failure', content: new OA\JsonContent(ref: '#/components/schemas/ServerErrorResponse')),
        ]
    )]
    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $this->authorize('update', $category);

        $category->update($request->validated());

        return ApiResponse::ok(new CategoryResource($category->fresh()), 'Category updated successfully.');
    }

    #[OA\Delete(
        path: '/api/categories/{category}',
        summary: 'Delete category',
        security: [['bearerAuth' => []]],
        tags: ['Categories'],
        parameters: [new OA\Parameter(name: 'category', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Category deleted', content: new OA\JsonContent(ref: '#/components/schemas/DeleteSuccessResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 404, description: 'Not found', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundResponse')),
            new OA\Response(response: 405, description: 'Method not allowed', content: new OA\JsonContent(ref: '#/components/schemas/MethodNotAllowedResponse')),
            new OA\Response(response: 409, description: 'Delete restricted', content: new OA\JsonContent(ref: '#/components/schemas/ConflictResponse')),
            new OA\Response(response: 500, description: 'Unexpected backend failure', content: new OA\JsonContent(ref: '#/components/schemas/ServerErrorResponse')),
        ]
    )]
    public function destroy(DeleteGuardService $guard, Category $category): JsonResponse
    {
        $this->authorize('delete', $category);

        $guard->guardCategory($category);
        $category->delete();

        return ApiResponse::deleted('Category deleted successfully.');
    }
}
