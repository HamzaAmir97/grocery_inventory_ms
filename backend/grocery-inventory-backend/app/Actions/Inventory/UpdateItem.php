<?php

namespace App\Actions\Inventory;

use App\Exceptions\ConflictException;
use App\Models\Item;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class UpdateItem
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(Item $item, array $data, ?int $userId): Item
    {
        return DB::transaction(function () use ($item, $data, $userId): Item {
            $previousStock = (int) $item->stock_quantity;
            $submittedVersion = array_key_exists('version', $data) ? (int) $data['version'] : null;
            unset($data['version']);

            if ($submittedVersion !== null) {
                $affected = Item::query()
                    ->whereKey($item->id)
                    ->where('version', $submittedVersion)
                    ->update(array_merge($data, ['version' => $submittedVersion + 1]));

                if ($affected === 0) {
                    throw new ConflictException;
                }

                $item = Item::query()->findOrFail($item->id);
            } else {
                $item->fill($data);
                $item->version = (int) $item->version + 1;
                $item->save();
            }

            $newStock = (int) $item->stock_quantity;
            if ($newStock !== $previousStock) {
                StockMovement::query()->create([
                    'item_id' => $item->id,
                    'user_id' => $userId,
                    'delta' => $newStock - $previousStock,
                    'reason' => StockMovement::REASON_MANUAL_EDIT,
                ]);
            }

            return $item;
        });
    }
}
