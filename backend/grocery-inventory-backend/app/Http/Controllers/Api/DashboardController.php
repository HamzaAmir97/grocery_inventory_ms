<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'DashboardItem',
    description: 'Slim item projection used inside DashboardStats.recent_items and DashboardStats.low_stock_list.',
    required: ['id', 'name', 'sku', 'category', 'supplier', 'unit_symbol', 'stock_quantity', 'low_stock_threshold', 'price', 'status', 'status_label', 'status_tone'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 17),
        new OA\Property(property: 'name', type: 'string', example: 'Whole Milk 1L'),
        new OA\Property(property: 'sku', type: 'string', nullable: true, example: 'MILK-WHOLE-1L'),
        new OA\Property(property: 'category', type: 'string', example: 'Dairy'),
        new OA\Property(property: 'supplier', type: 'string', example: 'Daily Dairy Co'),
        new OA\Property(property: 'unit_symbol', type: 'string', example: 'L'),
        new OA\Property(property: 'stock_quantity', type: 'integer', example: 4),
        new OA\Property(property: 'low_stock_threshold', type: 'integer', example: 15),
        new OA\Property(property: 'price', description: 'Decimal value with 2 decimal places, returned as a string to preserve precision.', type: 'string', example: '1.85'),
        new OA\Property(property: 'status', type: 'string', example: 'low_stock'),
        new OA\Property(property: 'status_label', type: 'string', example: 'Low stock'),
        new OA\Property(property: 'status_tone', type: 'string', example: 'warning'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'DashboardSummaryCard',
    required: ['key', 'label', 'value', 'badge', 'badge_tone'],
    properties: [
        new OA\Property(property: 'key', type: 'string', example: 'total_items'),
        new OA\Property(property: 'label', type: 'string', example: 'Total items'),
        new OA\Property(property: 'value', type: 'integer', example: 20),
        new OA\Property(property: 'badge', type: 'string', example: '+3'),
        new OA\Property(property: 'badge_tone', type: 'string', example: 'success'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'DashboardGrowthPoint',
    required: ['month', 'count'],
    properties: [
        new OA\Property(property: 'month', type: 'string', example: 'Jan'),
        new OA\Property(property: 'count', type: 'integer', example: 7),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'DashboardCategoryBreakdownItem',
    required: ['name', 'items_count', 'percentage'],
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Dairy'),
        new OA\Property(property: 'items_count', type: 'integer', example: 8),
        new OA\Property(property: 'percentage', type: 'integer', example: 40),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'DashboardCategoryBreakdown',
    required: ['total', 'items'],
    properties: [
        new OA\Property(property: 'total', type: 'integer', example: 20),
        new OA\Property(property: 'items', type: 'array', items: new OA\Items(ref: '#/components/schemas/DashboardCategoryBreakdownItem')),
    ],
    type: 'object'
)]
class DashboardController extends Controller
{
    #[OA\Get(
        path: '/api/dashboard/stats',
        summary: 'Dashboard summary - counts, growth, category breakdown, recent items, and low-stock list',
        description: 'Returns a single snapshot of the catalog. Inactive items are counted.',
        security: [['bearerAuth' => []]],
        tags: ['Dashboard'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Current dashboard snapshot.',
                content: new OA\JsonContent(
                    required: ['success', 'message', 'data'],
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Dashboard summary retrieved.'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/DashboardStats'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')),
            new OA\Response(response: 405, description: 'Method not allowed', content: new OA\JsonContent(ref: '#/components/schemas/MethodNotAllowedResponse')),
            new OA\Response(response: 500, description: 'Unexpected backend failure', content: new OA\JsonContent(ref: '#/components/schemas/ServerErrorResponse')),
        ]
    )]
    public function stats(DashboardService $dashboard): JsonResponse
    {
        Gate::authorize('viewDashboard');

        return ApiResponse::ok($dashboard->summary(), 'Dashboard summary retrieved.');
    }
}
