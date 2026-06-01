<?php

namespace App\Http\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'DashboardStats',
    required: ['total_items', 'total_categories', 'total_suppliers', 'low_stock_items', 'total_stock_value', 'summary_cards', 'inventory_growth_year', 'inventory_growth', 'category_breakdown', 'recent_items', 'low_stock_list'],
    properties: [
        new OA\Property(property: 'total_items', type: 'integer', example: 120),
        new OA\Property(property: 'total_categories', type: 'integer', example: 8),
        new OA\Property(property: 'total_suppliers', type: 'integer', example: 12),
        new OA\Property(property: 'low_stock_items', type: 'integer', example: 9),
        new OA\Property(property: 'total_stock_value', type: 'string', example: '15420.50'),
        new OA\Property(property: 'summary_cards', type: 'array', items: new OA\Items(ref: '#/components/schemas/DashboardSummaryCard')),
        new OA\Property(property: 'inventory_growth_year', type: 'integer', example: 2026),
        new OA\Property(property: 'inventory_growth', type: 'array', items: new OA\Items(ref: '#/components/schemas/DashboardGrowthPoint')),
        new OA\Property(property: 'category_breakdown', ref: '#/components/schemas/DashboardCategoryBreakdown'),
        new OA\Property(property: 'recent_items', type: 'array', items: new OA\Items(ref: '#/components/schemas/DashboardItem')),
        new OA\Property(property: 'low_stock_list', type: 'array', items: new OA\Items(ref: '#/components/schemas/DashboardItem')),
    ],
    type: 'object',
    example: [
        'total_items' => 120,
        'total_categories' => 5,
        'total_suppliers' => 3,
        'low_stock_items' => 9,
        'total_stock_value' => '15420.50',
        'summary_cards' => [
            ['key' => 'total_items', 'label' => 'Total items', 'value' => 120, 'badge' => '+12', 'badge_tone' => 'success'],
            ['key' => 'categories', 'label' => 'Categories', 'value' => 5, 'badge' => '+5', 'badge_tone' => 'success'],
            ['key' => 'suppliers', 'label' => 'Suppliers', 'value' => 3, 'badge' => '+3', 'badge_tone' => 'success'],
            ['key' => 'low_stock', 'label' => 'Low stock', 'value' => 9, 'badge' => 'needs action', 'badge_tone' => 'danger'],
        ],
        'inventory_growth_year' => 2026,
        'inventory_growth' => [
            ['month' => 'Jan', 'count' => 8],
            ['month' => 'Feb', 'count' => 10],
        ],
        'category_breakdown' => [
            'total' => 120,
            'items' => [
                ['name' => 'Dairy', 'items_count' => 34, 'percentage' => 28],
                ['name' => 'Bakery', 'items_count' => 22, 'percentage' => 18],
            ],
        ],
        'recent_items' => [
            [
                'id' => 17,
                'name' => 'Whole Milk 1L',
                'sku' => 'MILK-WHOLE-1L',
                'category' => 'Dairy',
                'supplier' => 'Daily Dairy Co',
                'unit_symbol' => 'L',
                'stock_quantity' => 4,
                'low_stock_threshold' => 15,
                'price' => '1.85',
                'status' => 'low_stock',
                'status_label' => 'Low stock',
                'status_tone' => 'warning',
            ],
        ],
        'low_stock_list' => [
            [
                'id' => 17,
                'name' => 'Whole Milk 1L',
                'sku' => 'MILK-WHOLE-1L',
                'category' => 'Dairy',
                'supplier' => 'Daily Dairy Co',
                'unit_symbol' => 'L',
                'stock_quantity' => 4,
                'low_stock_threshold' => 15,
                'price' => '1.85',
                'status' => 'low_stock',
                'status_label' => 'Low stock',
                'status_tone' => 'warning',
            ],
        ],
    ]
)]
class DashboardSchemas {}
