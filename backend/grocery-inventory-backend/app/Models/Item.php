<?php

namespace App\Models;

use App\Exceptions\SubcategoryMismatchException;
use Database\Factories\ItemFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    /** @use HasFactory<ItemFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'category_id',
        'subcategory_id',
        'unit_id',
        'supplier_id',
        'price',
        'stock_quantity',
        'low_stock_threshold',
        'description',
        'is_active',
        'version',
    ];

    protected static function booted(): void
    {
        static::saving(function (Item $item): void {
            if ($item->category_id === null || $item->subcategory_id === null) {
                return;
            }

            $isValidPairing = Subcategory::query()
                ->whereKey($item->subcategory_id)
                ->where('category_id', $item->category_id)
                ->exists();

            if (! $isValidPairing) {
                throw new SubcategoryMismatchException;
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'low_stock_threshold' => 'integer',
            'is_active' => 'boolean',
            'version' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class)->orderByDesc('created_at')->orderByDesc('id');
    }

    public function scopeLowStock(Builder $query): Builder
    {
        return $query->whereColumn('stock_quantity', '<=', 'low_stock_threshold');
    }
}
