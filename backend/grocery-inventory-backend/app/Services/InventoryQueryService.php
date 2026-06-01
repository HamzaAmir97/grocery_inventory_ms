<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Item;
use App\Models\Subcategory;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class InventoryQueryService
{
    /**
     * @param  array<string, mixed>  $params
     */
    public function list(array $params): LengthAwarePaginator
    {
        $search = trim((string) ($params['search'] ?? ''));
        $perPage = (int) ($params['per_page'] ?? 10);
        $page = (int) ($params['page'] ?? 1);
        $sortBy = $params['sort_by'] ?? 'created_at';
        $sortDir = $params['sort_dir'] ?? 'desc';

        $query = Item::query()->with([
            'category:id,name',
            'subcategory:id,name',
            'unit:id,name,symbol',
            'supplier:id,name',
        ]);

        if ($search !== '') {
            $escaped = addcslashes($search, '%_\\');

            $query->where(function ($query) use ($escaped): void {
                $query->where('name', 'ILIKE', "%{$escaped}%")
                    ->orWhere('sku', 'ILIKE', "%{$escaped}%");
            });
        }

        foreach (['category_id', 'subcategory_id', 'unit_id', 'supplier_id'] as $key) {
            if (($params[$key] ?? null) !== null && $params[$key] !== '') {
                $query->where($key, $params[$key]);
            }
        }

        if (! empty($params['low_stock'])) {
            $query->lowStock();
        }

        $this->applySort($query, $sortBy, $sortDir);

        return $query
            ->orderBy('id', $sortDir)
            ->paginate($perPage, ['*'], 'page', $page);
    }

    private function applySort(Builder $query, string $sortBy, string $sortDir): void
    {
        match ($sortBy) {
            'category' => $query->orderBy(
                Category::query()
                    ->select('name')
                    ->whereColumn('categories.id', 'items.category_id'),
                $sortDir
            ),
            'subcategory' => $query->orderBy(
                Subcategory::query()
                    ->select('name')
                    ->whereColumn('subcategories.id', 'items.subcategory_id'),
                $sortDir
            ),
            'unit' => $query->orderBy(
                Unit::query()
                    ->select('name')
                    ->whereColumn('units.id', 'items.unit_id'),
                $sortDir
            ),
            'supplier' => $query->orderBy(
                Supplier::query()
                    ->select('name')
                    ->whereColumn('suppliers.id', 'items.supplier_id'),
                $sortDir
            ),
            default => $query->orderBy($sortBy, $sortDir),
        };
    }
}
