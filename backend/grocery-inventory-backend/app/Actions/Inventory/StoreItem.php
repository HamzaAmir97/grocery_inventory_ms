<?php

namespace App\Actions\Inventory;

use App\Models\Item;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class StoreItem
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(array $data, ?int $userId): Item
    {
        return DB::transaction(function () use ($data, $userId): Item {
            $item = Item::query()->create($data);

            if ((int) $item->stock_quantity !== 0) {
                StockMovement::query()->create([
                    'item_id' => $item->id,
                    'user_id' => $userId,
                    'delta' => (int) $item->stock_quantity,
                    'reason' => StockMovement::REASON_INITIAL,
                ]);
            }

            return $item;
        });
    }
}
