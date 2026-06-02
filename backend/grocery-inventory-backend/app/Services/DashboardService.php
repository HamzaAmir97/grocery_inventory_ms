<?php

namespace App\Services;

use App\Http\Resources\DashboardItemResource;
use App\Models\Category;
use App\Models\Item;
use App\Models\Supplier;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * @return array<string, mixed>
     */
    public function summary(): array
    {
        $now = CarbonImmutable::now();
        $counts = DB::selectOne(
            'SELECT
                (SELECT COUNT(*) FROM items WHERE deleted_at IS NULL) AS total_items,
                (SELECT COUNT(*) FROM categories) AS total_categories,
                (SELECT COUNT(*) FROM suppliers) AS total_suppliers,
                (SELECT COUNT(*) FROM items WHERE deleted_at IS NULL AND stock_quantity <= low_stock_threshold) AS low_stock_items,
                (SELECT COALESCE(SUM(price * stock_quantity), 0) FROM items WHERE deleted_at IS NULL)::text AS total_stock_value'
        );

        $recent = Item::query()
            ->with(['category:id,name', 'unit:id,symbol', 'supplier:id,name'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $lowStock = Item::query()
            ->lowStock()
            ->with(['category:id,name', 'unit:id,symbol', 'supplier:id,name'])
            ->orderBy('stock_quantity')
            ->orderBy('low_stock_threshold')
            ->orderBy('id')
            ->limit(4)
            ->get();

        $inventoryGrowth = $this->inventoryGrowth($now);
        $categoryBreakdown = $this->categoryBreakdown();

        $monthRange = [$now->startOfMonth(), $now->endOfMonth()];
        $newItemsThisMonth = (int) Item::query()->whereBetween('created_at', $monthRange)->count();
        $newCategoriesThisMonth = (int) Category::query()->whereBetween('created_at', $monthRange)->count();
        $newSuppliersThisMonth = (int) Supplier::query()->whereBetween('created_at', $monthRange)->count();

        return [
            'total_items' => (int) $counts->total_items,
            'total_categories' => (int) $counts->total_categories,
            'total_suppliers' => (int) $counts->total_suppliers,
            'low_stock_items' => (int) $counts->low_stock_items,
            'total_stock_value' => number_format((float) $counts->total_stock_value, 2, '.', ''),
            'summary_cards' => [
                [
                    'key' => 'total_items',
                    'label' => 'Total items',
                    'value' => (int) $counts->total_items,
                    'badge' => '+'.$newItemsThisMonth,
                    'badge_tone' => 'success',
                ],
                [
                    'key' => 'categories',
                    'label' => 'Categories',
                    'value' => (int) $counts->total_categories,
                    'badge' => '+'.$newCategoriesThisMonth,
                    'badge_tone' => 'success',
                ],
                [
                    'key' => 'suppliers',
                    'label' => 'Suppliers',
                    'value' => (int) $counts->total_suppliers,
                    'badge' => '+'.$newSuppliersThisMonth,
                    'badge_tone' => 'success',
                ],
                [
                    'key' => 'low_stock',
                    'label' => 'Low stock',
                    'value' => (int) $counts->low_stock_items,
                    'badge' => (int) $counts->low_stock_items > 0 ? 'needs action' : 'healthy',
                    'badge_tone' => (int) $counts->low_stock_items > 0 ? 'danger' : 'success',
                ],
            ],
            'inventory_growth_year' => (int) $now->year,
            'inventory_growth' => $inventoryGrowth,
            'category_breakdown' => $categoryBreakdown,
            'recent_items' => DashboardItemResource::collection($recent)->resolve(),
            'low_stock_list' => DashboardItemResource::collection($lowStock)->resolve(),
        ];
    }

    /**
     * @return array<int, array{month: string, count: int}>
     */
    private function inventoryGrowth(CarbonImmutable $now): array
    {
        $startOfYear = $now->startOfYear();
        $endOfYear = $now->endOfYear();

        $countsByMonth = Item::query()
            ->whereBetween('created_at', [$startOfYear, $endOfYear])
            ->toBase()
            ->selectRaw('CAST(EXTRACT(MONTH FROM created_at) AS INTEGER) AS month, COUNT(*) AS aggregate')
            ->groupBy('month')
            ->pluck('aggregate', 'month');

        return collect(range(1, 12))
            ->map(function (int $month) use ($now, $countsByMonth): array {
                $date = CarbonImmutable::create($now->year, $month, 1, 0, 0, 0, $now->timezone);

                return [
                    'month' => $date->format('M'),
                    'count' => (int) ($countsByMonth[$month] ?? 0),
                ];
            })
            ->all();
    }

    /**
     * @return array{total: int, items: array<int, array{name: string, items_count: int, percentage: int}>}
     */
    private function categoryBreakdown(): array
    {
        $rows = Item::query()
            ->join('categories', 'categories.id', '=', 'items.category_id')
            ->groupBy('categories.name')
            ->orderByDesc(DB::raw('COUNT(items.id)'))
            ->orderBy('categories.name')
            ->get([
                'categories.name',
                DB::raw('COUNT(items.id) AS items_count'),
            ]);

        $total = (int) $rows->sum(fn (object $row): int => (int) $row->items_count);

        return [
            'total' => $total,
            'items' => $rows->map(function (object $row) use ($total): array {
                $count = (int) $row->items_count;

                return [
                    'name' => $row->name,
                    'items_count' => $count,
                    'percentage' => $total > 0 ? (int) round(($count / $total) * 100) : 0,
                ];
            })->all(),
        ];
    }
}
