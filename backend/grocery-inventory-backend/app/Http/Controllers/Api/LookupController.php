<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryLookupResource;
use App\Http\Resources\SubcategoryLookupResource;
use App\Http\Resources\SupplierLookupResource;
use App\Http\Resources\UnitLookupResource;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Supplier;
use App\Models\Unit;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class LookupController extends Controller
{
    #[OA\Get(path: '/api/lookups/categories', summary: 'Active categories for dropdowns', description: 'Returns a flat, unpaginated list of active categories projected to {id, name}. Alphabetical by name.', security: [['bearerAuth' => []]], tags: ['Lookups'], responses: [new OA\Response(response: 200, description: 'List of active categories.', content: new OA\JsonContent(required: ['success', 'message', 'data'], properties: [new OA\Property(property: 'success', type: 'boolean', example: true), new OA\Property(property: 'message', type: 'string', example: 'Categories lookups retrieved.'), new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/CategoryLookup'))], type: 'object')), new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')), new OA\Response(response: 405, description: 'Method not allowed', content: new OA\JsonContent(ref: '#/components/schemas/MethodNotAllowedResponse')), new OA\Response(response: 500, description: 'Unexpected backend failure', content: new OA\JsonContent(ref: '#/components/schemas/ServerErrorResponse'))])]
    public function categories(): JsonResponse
    {
        $this->authorize('viewAny', Category::class);

        $categories = Category::query()
            ->select(['id', 'name'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return ApiResponse::ok(CategoryLookupResource::collection($categories), 'Categories lookups retrieved.');
    }

    #[OA\Get(path: '/api/lookups/subcategories', summary: 'Active subcategories for dropdowns', description: 'Returns a flat, unpaginated list of active subcategories whose parent category is also active. Optionally restrict to a single parent via category_id.', security: [['bearerAuth' => []]], tags: ['Lookups'], parameters: [new OA\Parameter(name: 'category_id', description: 'Restrict the response to subcategories whose parent category matches this id. Omit or empty for no filter.', in: 'query', required: false, schema: new OA\Schema(type: 'integer', nullable: true))], responses: [new OA\Response(response: 200, description: 'List of active subcategories.', content: new OA\JsonContent(required: ['success', 'message', 'data'], properties: [new OA\Property(property: 'success', type: 'boolean', example: true), new OA\Property(property: 'message', type: 'string', example: 'Subcategories lookups retrieved.'), new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/SubcategoryLookup'))], type: 'object')), new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')), new OA\Response(response: 405, description: 'Method not allowed', content: new OA\JsonContent(ref: '#/components/schemas/MethodNotAllowedResponse')), new OA\Response(response: 422, description: 'Validation failed', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')), new OA\Response(response: 500, description: 'Unexpected backend failure', content: new OA\JsonContent(ref: '#/components/schemas/ServerErrorResponse'))])]
    public function subcategories(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Subcategory::class);

        $validated = $request->validate([
            'category_id' => ['nullable', 'integer'],
        ]);

        $parentId = $validated['category_id'] ?? null;

        $subcategories = Subcategory::query()
            ->select(['id', 'category_id', 'name'])
            ->where('is_active', true)
            ->whereHas('category', fn ($query) => $query->where('is_active', true))
            ->when($parentId !== null, fn ($query) => $query->where('category_id', $parentId))
            ->orderBy('name')
            ->get();

        return ApiResponse::ok(SubcategoryLookupResource::collection($subcategories), 'Subcategories lookups retrieved.');
    }

    #[OA\Get(path: '/api/lookups/units', summary: 'Active units for dropdowns', description: 'Returns a flat, unpaginated list of active units projected to {id, name, symbol}. Alphabetical by name.', security: [['bearerAuth' => []]], tags: ['Lookups'], responses: [new OA\Response(response: 200, description: 'List of active units.', content: new OA\JsonContent(required: ['success', 'message', 'data'], properties: [new OA\Property(property: 'success', type: 'boolean', example: true), new OA\Property(property: 'message', type: 'string', example: 'Units lookups retrieved.'), new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/UnitLookup'))], type: 'object')), new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')), new OA\Response(response: 405, description: 'Method not allowed', content: new OA\JsonContent(ref: '#/components/schemas/MethodNotAllowedResponse')), new OA\Response(response: 500, description: 'Unexpected backend failure', content: new OA\JsonContent(ref: '#/components/schemas/ServerErrorResponse'))])]
    public function units(): JsonResponse
    {
        $this->authorize('viewAny', Unit::class);

        $units = Unit::query()
            ->select(['id', 'name', 'symbol'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return ApiResponse::ok(UnitLookupResource::collection($units), 'Units lookups retrieved.');
    }

    #[OA\Get(path: '/api/lookups/suppliers', summary: 'Active suppliers for dropdowns', description: 'Returns a flat, unpaginated list of active suppliers projected to {id, name}. Alphabetical by name.', security: [['bearerAuth' => []]], tags: ['Lookups'], responses: [new OA\Response(response: 200, description: 'List of active suppliers.', content: new OA\JsonContent(required: ['success', 'message', 'data'], properties: [new OA\Property(property: 'success', type: 'boolean', example: true), new OA\Property(property: 'message', type: 'string', example: 'Suppliers lookups retrieved.'), new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/SupplierLookup'))], type: 'object')), new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')), new OA\Response(response: 405, description: 'Method not allowed', content: new OA\JsonContent(ref: '#/components/schemas/MethodNotAllowedResponse')), new OA\Response(response: 500, description: 'Unexpected backend failure', content: new OA\JsonContent(ref: '#/components/schemas/ServerErrorResponse'))])]
    public function suppliers(): JsonResponse
    {
        $this->authorize('viewAny', Supplier::class);

        $suppliers = Supplier::query()
            ->select(['id', 'name'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return ApiResponse::ok(SupplierLookupResource::collection($suppliers), 'Suppliers lookups retrieved.');
    }
}
