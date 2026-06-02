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
            // Lock and re-read the row inside the transaction so concurrent edits can't
            // compute the stock delta from a stale snapshot (lost-update / ledger drift).
            $locked = Item::query()->whereKey($item->id)->lockForUpdate()->firstOrFail();

            $previousStock = (int) $locked->stock_quantity;
            $submittedVersion = array_key_exists('version', $data) ? (int) $data['version'] : null;
            unset($data['version']);

            // Optimistic-lock check (when the client opts in by sending the version).
            if ($submittedVersion !== null && $submittedVersion !== (int) $locked->version) {
                throw new ConflictException;
            }

            // Always go through save() so the model's saving() guards (e.g. the
            // category/subcategory consistency check) run on every update path.
            $locked->fill($data);
            $locked->version = (int) $locked->version + 1;
            $locked->save();

            $newStock = (int) $locked->stock_quantity;
            if ($newStock !== $previousStock) {
                StockMovement::query()->create([
                    'item_id' => $locked->id,
                    'user_id' => $userId,
                    'delta' => $newStock - $previousStock,
                    'reason' => StockMovement::REASON_MANUAL_EDIT,
                ]);
            }

            return $locked;
        });
    }
}
