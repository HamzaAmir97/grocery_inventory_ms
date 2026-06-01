<?php

namespace App\Actions\Inventory;

use App\Models\Item;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class DeleteItem
{
    public function execute(Item $item, ?int $userId): void
    {
        DB::transaction(function () use ($item, $userId): void {
            if ((int) $item->stock_quantity !== 0) {
                StockMovement::query()->create([
                    'item_id' => $item->id,
                    'user_id' => $userId,
                    'delta' => -((int) $item->stock_quantity),
                    'reason' => StockMovement::REASON_DELETED,
                ]);
            }

            $item->delete();
        });
    }
}
